<?php

namespace App\Livewire\TownOwner;

use App\Models\Town;
use App\Models\TownExpense;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ManageExpenses extends Component
{
    use WithPagination, WithFileUploads;

    const CATEGORIES = [
        'Utilities' => 'Utilities (Bijli/Gas/Water)',
        'Maintenance' => 'Development & Repair',
        'Construction' => 'Construction Material',
        'Marketing' => 'Marketing & Ads',
        'Legal' => 'Legal & Govt Fees',
        'Office' => 'Office Expenses',
        'Transport' => 'Fuel & Transport',
        'General' => 'Other General',
    ];

    const PERIODS = ['this_month', 'last_month', 'this_year', 'all', 'custom'];

    const RECEIPT_DIR = 'expense_receipts';

    // Selected town (only the owner's own towns are allowed)
    #[Url(as: 'town', except: '')]
    public $townId = '';

    // Filters
    #[Url(except: 'this_month')]
    public $period = 'this_month';

    #[Url(except: '')]
    public $from = '';

    #[Url(except: '')]
    public $to = '';

    #[Url(as: 'category', except: '')]
    public $categoryFilter = '';

    #[Url(except: '')]
    public $search = '';

    // Add / Edit Form
    public $editingExpenseId = null;
    public $title = '';
    public $category = 'Utilities';
    public $amount = '';
    public $expense_date = '';
    public $paid_to = '';
    public $details = '';
    public $receipt;
    public $uploadIteration = 0;

    protected $messages = [
        'receipt.max' => 'Receipt ka size 5MB se zyada na ho.',
        'receipt.mimes' => 'Receipt sirf JPG, PNG, WEBP ya PDF ho.',
        'expense_date.before_or_equal' => 'Aage ki tareekh ka kharcha darj nahi ho sakta.',
    ];

    public function mount($townId = null)
    {
        $requested = $townId ?? $this->townId;
        $towns = $this->towns;

        $this->townId = (string) ($towns->firstWhere('id', (int) $requested)?->id ?? $towns->first()?->id ?? '');

        if (! in_array($this->period, self::PERIODS, true)) {
            $this->period = 'this_month';
        }

        $this->expense_date = today()->toDateString();
    }

    #[Computed]
    public function towns()
    {
        return Town::where('user_id', Auth::id())->orderBy('name')->get(['id', 'name']);
    }

    #[Computed]
    public function town()
    {
        return $this->townId !== '' ? Town::where('user_id', Auth::id())->find($this->townId) : null;
    }

    public function updatedTownId()
    {
        if (! $this->towns->contains('id', (int) $this->townId)) {
            $this->townId = (string) ($this->towns->first()?->id ?? '');
        }

        unset($this->town);
        $this->cancelEdit();
        $this->resetPage();
    }

    public function updating($property)
    {
        if (in_array($property, ['period', 'from', 'to', 'categoryFilter', 'search'], true)) {
            $this->resetPage();
        }
    }

    public function updatedPeriod()
    {
        if ($this->period !== 'custom') {
            $this->from = '';
            $this->to = '';
        }
    }

    public function updatedReceipt()
    {
        $this->validateOnly('receipt', ['receipt' => $this->receiptRule()]);
    }

    public function clearFilters()
    {
        $this->reset(['period', 'from', 'to', 'categoryFilter', 'search']);
        $this->resetPage();
    }

    /* ---------------- Add / Edit / Delete ---------------- */

    public function saveExpense()
    {
        $town = $this->requireTown();

        $this->validate([
            'title' => 'required|string|max:255',
            'category' => ['required', Rule::in(array_keys(self::CATEGORIES))],
            'amount' => 'required|numeric|min:1|max:1000000000',
            'expense_date' => 'required|date|before_or_equal:today',
            'paid_to' => 'nullable|string|max:255',
            'details' => 'nullable|string|max:2000',
            'receipt' => $this->receiptRule(),
        ]);

        $expense = $this->editingExpenseId ? $this->ownedExpense($this->editingExpenseId) : null;
        $oldReceipt = $expense?->receipt_path;
        $newReceipt = $this->receipt ? $this->storeReceipt($this->receipt) : null;

        $data = [
            'title' => trim($this->title),
            'category' => $this->category,
            'amount' => $this->amount,
            'expense_date' => $this->expense_date,
            'paid_to' => $this->paid_to ? trim($this->paid_to) : null,
            'details' => $this->details ? trim($this->details) : null,
            'receipt_path' => $newReceipt ?? $oldReceipt,
        ];

        if ($expense) {
            $expense->update($data);
            session()->flash('success', 'انٹری اپ ڈیٹ ہو گئی ہے۔');
        } else {
            TownExpense::create($data + ['town_id' => $town->id]);
            session()->flash('success', 'اخراجات کی انٹری درج کر لی گئی ہے۔');
        }

        // Old receipt is removed only after the new one is saved
        if ($newReceipt && $oldReceipt) {
            $this->deleteReceiptFile($oldReceipt);
        }

        $this->cancelEdit();
    }

    public function editExpense($id)
    {
        $expense = $this->ownedExpense($id);

        $this->resetValidation();
        $this->editingExpenseId = $expense->id;
        $this->title = $expense->title;
        $this->category = array_key_exists($expense->category, self::CATEGORIES) ? $expense->category : 'General';
        $this->amount = $expense->amount;
        $this->expense_date = Carbon::parse($expense->expense_date)->toDateString();
        $this->paid_to = $expense->paid_to;
        $this->details = $expense->details;
        $this->receipt = null;
        $this->uploadIteration++;

        $this->dispatch('expense-form-opened');
    }

    // Same kharcha dobara (e.g. har mahine ka bill) — form bhar deta hai, date aaj ki
    public function duplicateExpense($id)
    {
        $expense = $this->ownedExpense($id);

        $this->cancelEdit();
        $this->title = $expense->title;
        $this->category = array_key_exists($expense->category, self::CATEGORIES) ? $expense->category : 'General';
        $this->amount = $expense->amount;
        $this->paid_to = $expense->paid_to;
        $this->details = $expense->details;

        $this->dispatch('expense-form-opened');
    }

    public function cancelEdit()
    {
        $this->reset(['editingExpenseId', 'title', 'category', 'amount', 'paid_to', 'details', 'receipt']);
        $this->expense_date = today()->toDateString();
        $this->uploadIteration++;
        $this->resetValidation();
    }

    public function removeReceipt()
    {
        if (! $this->editingExpenseId) {
            return;
        }

        $expense = $this->ownedExpense($this->editingExpenseId);
        $this->deleteReceiptFile($expense->receipt_path);
        $expense->update(['receipt_path' => null]);

        session()->flash('success', 'Receipt hata di gayi.');
    }

    public function deleteExpense($id)
    {
        $expense = $this->ownedExpense($id);

        if ($this->editingExpenseId == $expense->id) {
            $this->cancelEdit();
        }

        $receipt = $expense->receipt_path;
        $expense->delete();
        $this->deleteReceiptFile($receipt);

        session()->flash('success', 'انٹری ڈیلیٹ کر دی گئی ہے۔');
    }

    public function exportCsv()
    {
        $town = $this->requireTown();
        $query = $this->filteredQuery($town)->orderBy('expense_date')->orderBy('id');
        $fileName = Str::slug($town->name) . '-expenses-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel shows Urdu correctly

            fputcsv($out, ['Date', 'Title', 'Category', 'Amount', 'Paid To', 'Details', 'Receipt']);

            $query->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $e) {
                    fputcsv($out, [
                        Carbon::parse($e->expense_date)->format('Y-m-d'),
                        $e->title,
                        $this->categoryLabel($e->category),
                        $e->amount,
                        $e->paid_to,
                        $e->details,
                        $e->receipt_path ? 'Yes' : '',
                    ]);
                }
            });

            fclose($out);
        }, $fileName, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /* ---------------- Render ---------------- */

    public function render()
    {
        $town = $this->town;
        $data = [
            'town' => $town,
            'towns' => $this->towns,
            'expenses' => null,
            'filteredTotal' => 0,
            'filteredCount' => 0,
            'breakdown' => [],
            'thisMonth' => 0,
            'lastMonth' => 0,
            'allTime' => 0,
            'editingExpense' => null,
        ];

        if ($town) {
            $filtered = $this->filteredQuery($town);

            $data['expenses'] = (clone $filtered)->latest('expense_date')->latest('id')->paginate(15);
            $data['filteredTotal'] = (float) (clone $filtered)->sum('amount');
            $data['filteredCount'] = (clone $filtered)->count();

            $data['breakdown'] = (clone $filtered)->toBase()
                ->selectRaw('category, SUM(amount) AS total, COUNT(*) AS entries')
                ->groupBy('category')
                ->orderByDesc('total')
                ->get()
                // 'general' (old default) and 'General' are the same category
                ->groupBy(fn ($r) => ucfirst((string) $r->category))
                ->map(fn ($rows, $key) => [
                    'key' => $key,
                    'label' => $this->categoryLabel($key),
                    'total' => (float) $rows->sum('total'),
                    'entries' => (int) $rows->sum('entries'),
                ])
                ->sortByDesc('total')
                ->values()
                ->all();

            $today = today();
            $sumBetween = fn ($start, $end) => (float) TownExpense::where('town_id', $town->id)
                ->whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])
                ->sum('amount');

            $data['thisMonth'] = $sumBetween($today->copy()->startOfMonth(), $today->copy()->endOfMonth());
            $data['lastMonth'] = $sumBetween($today->copy()->subMonthNoOverflow()->startOfMonth(), $today->copy()->subMonthNoOverflow()->endOfMonth());
            $data['allTime'] = (float) TownExpense::where('town_id', $town->id)->sum('amount');

            $data['editingExpense'] = $this->editingExpenseId
                ? TownExpense::where('town_id', $town->id)->find($this->editingExpenseId)
                : null;
        }

        return view('livewire.town-owner.manage-expenses', $data + [
            'categories' => self::CATEGORIES,
            'periodLabel' => $this->periodLabel(),
            'categoryLabel' => fn ($c) => $this->categoryLabel($c),
            'receiptUrl' => fn ($path) => asset('public/' . $path),
            'hasFilters' => $this->period !== 'this_month' || $this->categoryFilter !== '' || trim($this->search) !== '',
        ])->layout('layouts.app');
    }

    /* ---------------- Helpers ---------------- */

    protected function filteredQuery(Town $town)
    {
        $range = $this->dateRange();
        $search = trim($this->search);

        return TownExpense::where('town_id', $town->id)
            ->when($range, fn ($q) => $q->whereBetween('expense_date', $range))
            ->when($this->categoryFilter !== '', fn ($q) => $this->categoryFilter === 'General'
                // old rows were saved with the default 'general'
                ? $q->whereIn('category', ['General', 'general'])
                : $q->where('category', $this->categoryFilter))
            ->when($search !== '', fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('paid_to', 'like', "%{$search}%")
                    ->orWhere('details', 'like', "%{$search}%");
            }));
    }

    protected function dateRange(): ?array
    {
        $today = today();

        [$start, $end] = match ($this->period) {
            'this_month' => [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()],
            'last_month' => [$today->copy()->subMonthNoOverflow()->startOfMonth(), $today->copy()->subMonthNoOverflow()->endOfMonth()],
            'this_year' => [$today->copy()->startOfYear(), $today->copy()->endOfYear()],
            'custom' => [$this->parseDate($this->from), $this->parseDate($this->to) ?? $today->copy()],
            default => [null, null],
        };

        if (! $start || ! $end) {
            return null;
        }
        if ($start->gt($end)) {
            [$start, $end] = [$end, $start];
        }

        return [$start->toDateString(), $end->toDateString()];
    }

    protected function periodLabel(): string
    {
        $range = $this->dateRange();

        return match ($this->period) {
            'this_month' => 'This Month',
            'last_month' => 'Last Month',
            'this_year' => 'This Year',
            'custom' => $range ? Carbon::parse($range[0])->format('d M Y') . ' – ' . Carbon::parse($range[1])->format('d M Y') : 'All Time',
            default => 'All Time',
        };
    }

    protected function parseDate($value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        try {
            return Carbon::createFromFormat('!Y-m-d', $value);
        } catch (\Throwable) {
            return null;
        }
    }

    protected function categoryLabel($category): string
    {
        return self::CATEGORIES[$category] ?? self::CATEGORIES[ucfirst((string) $category)] ?? ucfirst((string) $category);
    }

    protected function receiptRule(): string
    {
        return 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120';
    }

    protected function storeReceipt($file): string
    {
        $dir = public_path(self::RECEIPT_DIR);
        File::ensureDirectoryExists($dir, 0755);

        // Extension comes from the real MIME type, never from the user's file name
        $name = 'receipt_' . now()->format('YmdHis') . '_' . Str::random(10) . '.' . $file->extension();
        File::copy($file->getRealPath(), $dir . DIRECTORY_SEPARATOR . $name);

        return self::RECEIPT_DIR . '/' . $name;
    }

    protected function deleteReceiptFile(?string $path): void
    {
        if (! $path || str_contains($path, '..') || ! Str::startsWith($path, self::RECEIPT_DIR . '/')) {
            return;
        }

        if (File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }

    protected function requireTown(): Town
    {
        $town = $this->town;
        abort_unless($town, 403);

        return $town;
    }

    protected function ownedExpense($id): TownExpense
    {
        return TownExpense::where('town_id', $this->requireTown()->id)->findOrFail($id);
    }
}
