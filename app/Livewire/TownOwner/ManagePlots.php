<?php

namespace App\Livewire\TownOwner;

use App\Support\Tenant;
use App\Models\Installment;
use App\Models\Plot;
use App\Models\PlotBooking;
use App\Models\Town;
use App\Support\PlotPricing;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ManagePlots extends Component
{
    use WithPagination;

    // Booking / cancel go through PlotBookingManager; only these direct changes are allowed here
    const STATUS_TRANSITIONS = [
        'booked' => ['sold'],
        'sold' => ['booked'],
    ];

    const BULK_LIMIT = 500;
    const GRID_LIMIT = 3000;

    #[Locked]
    public $townId;

    public $mode = 'single'; // single | bulk
    public $editingPlotId = null;

    public $plot_number, $block_name, $type = 'residential', $size = '5 Marla';
    public $rate_per_marla, $base_price, $features = [];
    public $down_payment, $total_installments = 36;
    public $bulk_from, $bulk_to;

    // Bulk price update
    public $showBulkPrice = false;
    public $bp_block = '', $bp_type = '', $bp_size = '', $bp_method = 'percent', $bp_value;

    // Table filters (kept in the URL)
    #[Url(as: 'view', except: 'list')]
    public $viewMode = 'list';

    #[Url(except: '')]
    public $search = '';

    #[Url(as: 'status', except: '')]
    public $statusFilter = '';

    #[Url(as: 'block', except: '')]
    public $blockFilter = '';

    #[Url(as: 'type', except: '')]
    public $typeFilter = '';

    public function mount($townId)
    {
        $this->townId = Town::where('user_id', Tenant::ownerId())->findOrFail($townId)->id;
    }

    #[Computed]
    public function town()
    {
        return Town::where('user_id', Tenant::ownerId())->findOrFail($this->townId);
    }

    protected function rules()
    {
        $rules = [
            'block_name' => 'nullable|string|max:100',
            'type' => 'required|in:residential,commercial',
            'size' => 'required|string|max:50',
            'rate_per_marla' => 'nullable|numeric|min:0',
            'base_price' => 'required|numeric|min:1',
            'features' => 'array',
            'features.*' => Rule::in(array_keys(PlotPricing::FEATURES)),
            'down_payment' => 'required|numeric|min:0',
            'total_installments' => 'required|integer|min:1|max:360',
        ];

        if ($this->isBulk()) {
            $rules['bulk_from'] = 'required|integer|min:1';
            $rules['bulk_to'] = 'required|integer|gte:bulk_from';
        } else {
            $rules['plot_number'] = ['required', 'string', 'max:50', $this->uniquePlotRule()];
        }

        return $rules;
    }

    protected $messages = [
        'plot_number.unique' => 'Is block mein ye plot number pehle se maujood hai.',
        'base_price.required' => 'Base price likhein ya rate per marla se calculate karwayein.',
        'total_installments.max' => 'Zyada se zyada 360 installments (30 saal) ho sakti hain.',
        'bulk_to.gte' => '"To" number "From" se bara ya barabar hona chahiye.',
    ];

    #[On('plot-booking-updated')]
    public function bookingUpdated($message = null, $type = 'success')
    {
        if ($message) {
            session()->flash($type === 'error' ? 'error' : 'success', $message);
        }
    }

    public function updating($property)
    {
        if (in_array($property, ['search', 'statusFilter', 'blockFilter', 'typeFilter'], true)) {
            $this->resetPage();
        }
    }

    // Size ya rate badalte hi base price khud calculate
    public function updatedSize()
    {
        $this->recalculateBase();
    }

    public function updatedRatePerMarla()
    {
        $this->recalculateBase();
    }

    protected function recalculateBase()
    {
        $marlas = PlotPricing::marlas($this->size);

        if (is_numeric($this->rate_per_marla) && $this->rate_per_marla > 0 && $marlas > 0) {
            $this->base_price = round($marlas * (float) $this->rate_per_marla);
        }
    }

    public function setMode($mode)
    {
        $this->mode = $mode === 'bulk' ? 'bulk' : 'single';
        $this->resetValidation();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'statusFilter', 'blockFilter', 'typeFilter']);
        $this->resetPage();
    }

    public function savePlot()
    {
        if ($this->isBulk()) {
            return $this->bulkAddPlots();
        }

        $this->validate();

        $features = PlotPricing::cleanFeatures($this->features);
        $total = PlotPricing::totalPrice($this->base_price, $features);

        if ((float) $this->down_payment > $total) {
            $this->addError('down_payment', 'Down payment total price (Rs. ' . number_format($total) . ') se zyada nahi ho sakti.');
            return;
        }

        $data = [
            'plot_number' => trim($this->plot_number),
            'block_name' => $this->normalizedBlock(),
            'type' => $this->type,
            'size' => $this->size,
            'rate_per_marla' => is_numeric($this->rate_per_marla) ? $this->rate_per_marla : null,
            'base_price' => $this->base_price,
            'features' => $features ?: null,
            'total_price' => $total,
            'down_payment' => $this->down_payment,
            'total_installments' => $this->total_installments,
        ];

        if ($this->editingPlotId) {
            $plot = $this->ownedPlot($this->editingPlotId);

            // Booked/sold plot ki qeemat aur plan booking ke waqt fix ho chuka hai
            if ($plot->status !== 'available') {
                unset($data['rate_per_marla'], $data['base_price'], $data['features'], $data['total_price'], $data['down_payment'], $data['total_installments']);
            }

            $plot->update($data);
            session()->flash('success', 'پلاٹ کی تفصیلات اپ ڈیٹ ہو گئی ہیں۔');
            $this->cancelEdit();

            return;
        }

        Plot::create($data + [
            'town_id' => $this->townId,
            'status' => 'available',
        ]);

        // Block, size, price waghera rehne dein taake agla plot jaldi add ho sake
        $this->reset('plot_number', 'features');
        $this->resetValidation();
        session()->flash('success', 'پلاٹ انوینٹری میں کامیابی سے شامل ہو گیا ہے۔');
    }

    protected function bulkAddPlots()
    {
        $this->validate();

        $from = (int) $this->bulk_from;
        $to = (int) $this->bulk_to;

        if ($to - $from + 1 > self::BULK_LIMIT) {
            $this->addError('bulk_to', 'Ek waqt mein zyada se zyada ' . self::BULK_LIMIT . ' plots add ho sakte hain.');
            return;
        }

        $features = PlotPricing::cleanFeatures($this->features);
        $total = PlotPricing::totalPrice($this->base_price, $features);

        if ((float) $this->down_payment > $total) {
            $this->addError('down_payment', 'Down payment total price (Rs. ' . number_format($total) . ') se zyada nahi ho sakti.');
            return;
        }

        $block = $this->normalizedBlock();

        $existing = Plot::where('town_id', $this->townId)
            ->when($block === null, fn ($q) => $q->whereNull('block_name'), fn ($q) => $q->where('block_name', $block))
            ->pluck('plot_number')
            ->map(fn ($n) => (string) $n)
            ->flip();

        $now = now();
        $rows = [];
        foreach (range($from, $to) as $number) {
            if (isset($existing[(string) $number])) {
                continue;
            }

            $rows[] = [
                'town_id' => $this->townId,
                'plot_number' => (string) $number,
                'block_name' => $block,
                'type' => $this->type,
                'size' => $this->size,
                'rate_per_marla' => is_numeric($this->rate_per_marla) ? $this->rate_per_marla : null,
                'base_price' => $this->base_price,
                'features' => $features ? json_encode($features) : null,
                'total_price' => $total,
                'down_payment' => $this->down_payment,
                'total_installments' => $this->total_installments,
                'status' => 'available',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::transaction(function () use ($rows) {
            foreach (array_chunk($rows, 200) as $chunk) {
                Plot::insert($chunk);
            }
        });

        $skipped = ($to - $from + 1) - count($rows);
        session()->flash('success', count($rows) . ' plots add ho gaye' . ($skipped ? " ({$skipped} pehle se maujood the, skip kiye)" : '') . '۔');

        $this->reset(['bulk_from', 'bulk_to']);
        $this->resetValidation();
    }

    public function editPlot($id)
    {
        $plot = $this->ownedPlot($id);

        $this->resetValidation();
        $this->mode = 'single';
        $this->editingPlotId = $plot->id;
        $this->plot_number = $plot->plot_number;
        $this->block_name = $plot->block_name;
        $this->type = $plot->type;
        $this->size = $plot->size;
        $this->features = PlotPricing::cleanFeatures($plot->features);
        $this->rate_per_marla = $plot->rate_per_marla;
        $this->base_price = $plot->base_price ?? PlotPricing::baseFromTotal($plot->total_price, $this->features);
        $this->down_payment = $plot->down_payment;
        $this->total_installments = $plot->total_installments;

        $this->dispatch('plot-form-opened');
    }

    public function cancelEdit()
    {
        $this->reset([
            'editingPlotId', 'plot_number', 'block_name', 'type', 'size', 'rate_per_marla', 'base_price', 'features',
            'down_payment', 'total_installments', 'bulk_from', 'bulk_to',
        ]);
        $this->resetValidation();
    }

    public function updateStatus($plotId, $status)
    {
        $plot = $this->ownedPlot($plotId);

        // Never trust the status coming from the browser
        if (! in_array($status, self::STATUS_TRANSITIONS[$plot->status] ?? [], true)) {
            session()->flash('error', 'Ye status change allowed nahi hai.');
            return;
        }

        $plot->update([
            'status' => $status,
            'sold_at' => $status === 'sold' ? today()->toDateString() : null,
        ]);

        session()->flash('success', 'پلاٹ کا اسٹیٹس تبدیل ہو گیا ہے۔');
    }

    public function deletePlot($id)
    {
        $plot = $this->ownedPlot($id);

        $hasHistory = Installment::where('plot_id', $plot->id)->exists()
            || PlotBooking::where('plot_id', $plot->id)->exists();

        if ($plot->status !== 'available' || $hasHistory) {
            session()->flash('error', 'Sirf woh available plot delete ho sakta hai jis ki kabhi booking/installment na hui ho.');
            return;
        }

        if ($this->editingPlotId == $plot->id) {
            $this->cancelEdit();
        }

        $plot->delete();
        session()->flash('success', 'Plot #' . $plot->plot_number . ' delete kar diya gaya.');
    }

    public function applyBulkPrice()
    {
        $this->validate([
            'bp_method' => 'required|in:rate,percent,amount',
            'bp_value' => [
                'required', 'numeric',
                match ($this->bp_method) {
                    'rate' => 'min:1',
                    'percent' => 'between:-90,500',
                    default => 'between:-100000000,100000000',
                },
            ],
        ], [
            'bp_value.required' => 'Value likhein.',
        ]);

        $updated = 0;
        $skipped = 0;

        DB::transaction(function () use (&$updated, &$skipped) {
            $this->bulkPriceQuery()->chunkById(200, function ($plots) use (&$updated, &$skipped) {
                foreach ($plots as $plot) {
                    $marlas = PlotPricing::marlas($plot->size);
                    $currentBase = (float) ($plot->base_price ?? PlotPricing::baseFromTotal($plot->total_price, $plot->features));

                    $newBase = match ($this->bp_method) {
                        'rate' => $marlas * (float) $this->bp_value,
                        'percent' => $currentBase * (1 + (float) $this->bp_value / 100),
                        'amount' => $currentBase + (float) $this->bp_value,
                    };

                    if ($newBase <= 0) {
                        $skipped++;
                        continue;
                    }

                    $total = PlotPricing::totalPrice($newBase, $plot->features);

                    $plot->update([
                        'base_price' => round($newBase),
                        'rate_per_marla' => $marlas > 0 ? round($newBase / $marlas, 2) : $plot->rate_per_marla,
                        'total_price' => $total,
                        'down_payment' => min((float) $plot->down_payment, $total),
                    ]);
                    $updated++;
                }
            });
        });

        session()->flash('success', "{$updated} available plots ki qeemat update ho gayi" . ($skipped ? " ({$skipped} skip — size/qeemat sahi nahi thi)" : '') . '۔');
        $this->reset(['showBulkPrice', 'bp_value']);
    }

    public function exportCsv()
    {
        $fileName = Str::slug($this->town->name) . '-plots-' . now()->format('Y-m-d') . '.csv';
        $query = $this->filteredQuery()->with('customer:id,name,phone,cnic');

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel shows Urdu correctly

            fputcsv($out, ['Plot #', 'Block', 'Type', 'Size', 'Features', 'Rate/Marla', 'Base Price', 'Total Price', 'Down Payment', 'Installments', 'Monthly', 'Status', 'Customer', 'CNIC', 'Phone']);

            $query->chunk(500, function ($plots) use ($out) {
                foreach ($plots as $plot) {
                    fputcsv($out, [
                        $plot->plot_number,
                        $plot->block_name ?? 'Main',
                        ucfirst($plot->type),
                        $plot->size,
                        implode(', ', PlotPricing::featureLabels($plot->features)),
                        $plot->rate_per_marla,
                        $plot->base_price,
                        $plot->total_price,
                        $plot->down_payment,
                        $plot->total_installments,
                        $plot->total_installments > 0 ? round(max(0, $plot->total_price - $plot->down_payment) / $plot->total_installments) : 0,
                        ucfirst($plot->status),
                        $plot->customer?->name,
                        $plot->customer?->cnic,
                        $plot->customer?->phone,
                    ]);
                }
            });

            fclose($out);
        }, $fileName, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function render()
    {
        $plotsTable = (new Plot)->getTable();
        $installmentsTable = (new Installment)->getTable();
        $bookingsTable = (new PlotBooking)->getTable();

        // Only installments of the current booking (legacy rows have no booking id); #0 = down payment
        $currentInstallments = fn () => Installment::selectRaw('COUNT(*)')
            ->whereColumn("{$installmentsTable}.plot_id", "{$plotsTable}.id")
            ->where('installment_number', '>', 0)
            ->where(fn ($q) => $q->whereNull('plot_booking_id')
                ->orWhereIn('plot_booking_id', PlotBooking::select('id')->where('status', 'active')));

        $plots = null;
        $gridBlocks = collect();

        if ($this->viewMode === 'grid') {
            $gridBlocks = $this->filteredQuery()
                ->with('customer:id,name')
                ->limit(self::GRID_LIMIT)
                ->get()
                ->groupBy(fn ($p) => $p->block_name ?? 'Main');
        } else {
            $plots = $this->filteredQuery()
                ->with('customer:id,name,phone')
                ->select("{$plotsTable}.*")
                ->addSelect([
                    'installments_count' => $currentInstallments(),
                    'paid_installments_count' => $currentInstallments()->where('status', 'paid'),
                    'bookings_count' => PlotBooking::selectRaw('COUNT(*)')->whereColumn("{$bookingsTable}.plot_id", "{$plotsTable}.id"),
                ])
                ->paginate(15);
        }

        $stats = Plot::where('town_id', $this->townId)
            ->toBase()
            ->selectRaw("
                COUNT(*) AS total,
                COALESCE(SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END), 0) AS available,
                COALESCE(SUM(CASE WHEN status = 'booked' THEN 1 ELSE 0 END), 0) AS booked,
                COALESCE(SUM(CASE WHEN status = 'sold' THEN 1 ELSE 0 END), 0) AS sold,
                COALESCE(SUM(total_price), 0) AS inventory_value,
                COALESCE(SUM(CASE WHEN status IN ('booked', 'sold') THEN total_price ELSE 0 END), 0) AS sold_value,
                COALESCE(SUM(CASE WHEN status = 'available' THEN total_price ELSE 0 END), 0) AS available_value
            ")
            ->first();

        $blocks = Plot::where('town_id', $this->townId)
            ->whereNotNull('block_name')
            ->distinct()
            ->orderBy('block_name')
            ->pluck('block_name');

        $sizes = PlotPricing::SIZES;
        foreach ([$this->size, $this->bp_size] as $extra) {
            if ($extra && ! in_array($extra, $sizes, true)) {
                $sizes[] = $extra; // keep an old custom size selectable
            }
        }

        $editingPlot = $this->editingPlotId ? Plot::where('town_id', $this->townId)->find($this->editingPlotId) : null;

        return view('livewire.town-owner.manage-plots', [
            'town' => $this->town,
            'plots' => $plots,
            'gridBlocks' => $gridBlocks,
            'stats' => $stats,
            'blocks' => $blocks,
            'sizes' => $sizes,
            'featureOptions' => PlotPricing::FEATURES,
            'editingLocked' => $editingPlot && $editingPlot->status !== 'available',
            'bulkPriceCount' => $this->showBulkPrice ? $this->bulkPriceQuery()->count() : 0,
            'hasFilters' => trim($this->search) !== '' || $this->statusFilter !== '' || $this->blockFilter !== '' || $this->typeFilter !== '',
        ])->layout('layouts.app');
    }

    protected function filteredQuery()
    {
        $search = trim($this->search);

        return Plot::where('town_id', $this->townId)
            ->when($search !== '', fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('plot_number', 'like', "%{$search}%")
                    ->orWhere('block_name', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$search}%")
                        ->orWhere('cnic', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%"));
            }))
            ->when($this->statusFilter !== '', fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->blockFilter !== '', fn ($q) => $this->blockFilter === '__none'
                ? $q->whereNull('block_name')
                : $q->where('block_name', $this->blockFilter))
            ->when($this->typeFilter !== '', fn ($q) => $q->where('type', $this->typeFilter))
            // Natural order: Block A → 1, 2, 10 (not 1, 10, 2)
            ->orderBy('block_name')
            ->orderByRaw('LENGTH(plot_number)')
            ->orderBy('plot_number');
    }

    // Bulk price update only touches AVAILABLE plots (booked prices are fixed at booking time)
    protected function bulkPriceQuery()
    {
        return Plot::where('town_id', $this->townId)
            ->where('status', 'available')
            ->when($this->bp_block !== '', fn ($q) => $this->bp_block === '__none'
                ? $q->whereNull('block_name')
                : $q->where('block_name', $this->bp_block))
            ->when($this->bp_type !== '', fn ($q) => $q->where('type', $this->bp_type))
            ->when($this->bp_size !== '', fn ($q) => $q->where('size', $this->bp_size));
    }

    protected function uniquePlotRule()
    {
        $block = $this->normalizedBlock();

        return Rule::unique((new Plot)->getTable(), 'plot_number')
            ->where('town_id', $this->townId)
            ->where(fn ($q) => $block === null ? $q->whereNull('block_name') : $q->where('block_name', $block))
            ->ignore($this->editingPlotId);
    }

    protected function ownedPlot($id): Plot
    {
        return Plot::where('town_id', $this->townId)->findOrFail($id);
    }

    protected function normalizedBlock(): ?string
    {
        $block = trim((string) $this->block_name);

        return $block === '' ? null : $block;
    }

    protected function isBulk(): bool
    {
        return $this->mode === 'bulk' && ! $this->editingPlotId;
    }
}
