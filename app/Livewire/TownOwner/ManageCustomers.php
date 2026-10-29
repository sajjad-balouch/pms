<?php

namespace App\Livewire\TownOwner;

use App\Models\Customer;
use App\Models\CustomerDocument;
use App\Models\Installment;
use App\Models\PlotBooking;
use App\Services\BookingService;
use App\Support\WhatsApp;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

/**
 * Customers (buyers) of the town owner: profile, plots, balance and document vault.
 */
class ManageCustomers extends Component
{
    use WithPagination, WithFileUploads;

    const FILTERS = ['all', 'overdue', 'no_cnic', 'no_plot'];

    #[Url(except: '')]
    public $search = '';

    #[Url(except: 'all')]
    public $filter = 'all';

    // Profile modal (also opened from other pages with ?customer=ID)
    #[Url(as: 'customer', except: '')]
    public $profileId = '';

    public $editing = false;
    public $c_name, $c_father_name, $c_cnic, $c_phone, $c_address;

    // Document upload
    public $doc_type = 'cnic_front';
    public $doc_title = '';
    public $doc_file;
    public $uploadIteration = 0;

    protected $messages = [
        'c_cnic.regex' => 'CNIC is format mein likhein: 35202-1234567-1',
        'c_cnic.unique' => 'Is CNIC ka customer pehle se maujood hai.',
        'c_phone.regex' => 'Phone number sahi nahi (misal: 0300-1234567).',
        'doc_file.max' => 'File 5MB se zyada na ho.',
        'doc_file.mimes' => 'Sirf JPG, PNG, WEBP ya PDF.',
    ];

    public function mount()
    {
        if (! in_array($this->filter, self::FILTERS, true)) {
            $this->filter = 'all';
        }
        if ($this->profileId !== '' && ! $this->ownedQuery()->whereKey($this->profileId)->exists()) {
            $this->profileId = '';
        }
    }

    public function updating($property)
    {
        if (in_array($property, ['search', 'filter'], true)) {
            $this->resetPage();
        }
    }

    public function updatedDocFile()
    {
        $this->validateOnly('doc_file', ['doc_file' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120']);
    }

    /* ---------------- Profile ---------------- */

    public function openProfile($id)
    {
        $customer = $this->ownedCustomer($id);
        $this->resetValidation();
        $this->profileId = (string) $customer->id;
        $this->editing = false;
        $this->resetDocForm();
    }

    public function closeProfile()
    {
        $this->reset(['profileId', 'editing']);
        $this->resetDocForm();
        $this->resetValidation();
    }

    public function newCustomer()
    {
        $this->resetValidation();
        $this->reset(['profileId', 'c_name', 'c_father_name', 'c_cnic', 'c_phone', 'c_address']);
        $this->profileId = 'new';
        $this->editing = true;
    }

    public function startEdit()
    {
        $customer = $this->ownedCustomer($this->profileId);

        $this->c_name = $customer->name;
        $this->c_father_name = $customer->father_name;
        $this->c_cnic = $customer->cnic;
        $this->c_phone = $customer->phone;
        $this->c_address = $customer->address;
        $this->editing = true;
    }

    public function cancelEdit()
    {
        $this->resetValidation();
        if ($this->profileId === 'new') {
            $this->closeProfile();
            return;
        }
        $this->editing = false;
    }

    public function saveCustomer()
    {
        $isNew = $this->profileId === 'new';
        $customer = $isNew ? null : $this->ownedCustomer($this->profileId);
        $this->c_cnic = $this->formatCnic($this->c_cnic);

        $this->validate([
            'c_name' => 'required|string|max:255',
            'c_father_name' => 'nullable|string|max:255',
            'c_cnic' => [
                'nullable', 'regex:/^\d{5}-\d{7}-\d$/',
                Rule::unique('customers', 'cnic')->where('user_id', Auth::id())->ignore($customer?->id),
            ],
            'c_phone' => ['required', 'regex:/^[0-9+\-\s]{10,15}$/'],
            'c_address' => 'nullable|string|max:500',
        ]);

        $data = [
            'name' => trim($this->c_name),
            'father_name' => $this->c_father_name ? trim($this->c_father_name) : null,
            'cnic' => $this->c_cnic ?: null,
            'phone' => trim($this->c_phone),
            'address' => $this->c_address ? trim($this->c_address) : null,
        ];

        DB::transaction(function () use (&$customer, $data, $isNew) {
            if ($isNew) {
                $customer = Customer::create($data + ['user_id' => Auth::id()]);
                return;
            }

            $customer->update($data);

            // Receipts / reminders use the name & phone copied on unpaid installments
            Installment::whereIn('plot_booking_id', PlotBooking::select('id')->where('customer_id', $customer->id)->where('status', 'active'))
                ->whereIn('status', BookingService::UNPAID)
                ->update(['buyer_name' => $customer->name, 'buyer_phone' => $customer->phone]);
        });

        $this->profileId = (string) $customer->id;
        $this->editing = false;
        session()->flash('success', $isNew ? 'Naya customer ban gaya.' : 'Customer ki tafseel update ho gayi.');
    }

    public function deleteCustomer()
    {
        $customer = $this->ownedCustomer($this->profileId);

        if ($customer->bookings()->exists()) {
            session()->flash('error', 'Is customer ki booking ka record hai, is liye delete nahi ho sakta.');
            return;
        }

        foreach ($customer->documents as $doc) {
            Storage::disk(CustomerDocument::DISK)->delete($doc->path);
        }

        $customer->delete();
        $this->closeProfile();
        session()->flash('success', 'Customer delete kar diya gaya.');
    }

    /* ---------------- Documents ---------------- */

    public function uploadDocument()
    {
        $customer = $this->ownedCustomer($this->profileId);

        $this->validate([
            'doc_type' => ['required', Rule::in(array_keys(CustomerDocument::TYPES))],
            'doc_title' => 'nullable|string|max:255',
            'doc_file' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ], ['doc_file.required' => 'File select karein.']);

        // Extension from the real MIME type, never from the user's file name
        $name = $this->doc_type . '_' . now()->format('YmdHis') . '_' . Str::random(8) . '.' . $this->doc_file->extension();
        $path = $this->doc_file->storeAs('customer_documents/' . $customer->id, $name, CustomerDocument::DISK);

        // CNIC/photo: naya upload purane ki jagah le leta hai
        if (in_array($this->doc_type, ['cnic_front', 'cnic_back', 'photo'], true)) {
            $customer->documents()->where('type', $this->doc_type)->get()->each(function ($old) {
                Storage::disk(CustomerDocument::DISK)->delete($old->path);
                $old->delete();
            });
        }

        $customer->documents()->create([
            'type' => $this->doc_type,
            'title' => $this->doc_title ? trim($this->doc_title) : null,
            'path' => $path,
            'mime' => $this->doc_file->getMimeType(),
            'size' => (int) $this->doc_file->getSize(),
            'uploaded_by' => Auth::id(),
        ]);

        session()->flash('success', CustomerDocument::TYPES[$this->doc_type] . ' upload ho gaya.');
        $this->resetDocForm();
    }

    public function deleteDocument($id)
    {
        $doc = CustomerDocument::whereHas('customer', fn ($q) => $q->where('user_id', Auth::id()))->findOrFail($id);

        Storage::disk(CustomerDocument::DISK)->delete($doc->path);
        $doc->delete();

        session()->flash('success', 'Document delete ho gaya.');
    }

    /* ---------------- Render ---------------- */

    public function render()
    {
        $today = today()->toDateString();

        // Money of the customer's ACTIVE bookings (cancelled ones are history)
        $activeBookings = fn () => PlotBooking::select('id')->whereColumn('plot_bookings.customer_id', 'customers.id')->where('status', 'active');
        $sum = fn () => Installment::selectRaw('COALESCE(SUM(amount), 0)')->whereIn('plot_booking_id', $activeBookings());

        $search = trim($this->search);

        $customers = $this->ownedQuery()
            ->select('customers.*')
            ->addSelect([
                'active_plots' => PlotBooking::selectRaw('COUNT(*)')->whereColumn('plot_bookings.customer_id', 'customers.id')->where('status', 'active'),
                'paid_total' => $sum()->where('status', 'paid'),
                'remaining_total' => $sum()->where('status', '!=', 'paid'),
                'overdue_total' => $sum()->where('status', '!=', 'paid')->where('due_date', '<', $today),
            ])
            ->withCount([
                'documents',
                'documents as cnic_docs_count' => fn ($q) => $q->whereIn('type', ['cnic_front', 'cnic_back']),
            ])
            ->when($search !== '', fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('father_name', 'like', "%{$search}%")
                    ->orWhere('cnic', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            }))
            ->when($this->filter === 'overdue', fn ($q) => $q->having('overdue_total', '>', 0))
            ->when($this->filter === 'no_cnic', fn ($q) => $q->having('cnic_docs_count', '<', 2))
            ->when($this->filter === 'no_plot', fn ($q) => $q->having('active_plots', '=', 0))
            ->orderBy('name')
            ->paginate(20);

        $owned = $this->ownedQuery();
        $stats = [
            'total' => (clone $owned)->count(),
            'with_plots' => (clone $owned)->whereHas('bookings', fn ($q) => $q->where('status', 'active'))->count(),
            'missing_cnic' => (clone $owned)->whereHas('documents', fn ($q) => $q->whereIn('type', ['cnic_front', 'cnic_back']), '<', 2)->count(),
        ];

        return view('livewire.town-owner.manage-customers', [
            'customers' => $customers,
            'stats' => $stats,
            'docTypes' => CustomerDocument::TYPES,
            'profile' => $this->profileData(),
        ])->layout('layouts.app');
    }

    protected function profileData(): ?array
    {
        if ($this->profileId === '' || $this->profileId === 'new') {
            return null;
        }

        $customer = $this->ownedQuery()->with(['documents' => fn ($q) => $q->latest('id')])->find($this->profileId);
        if (! $customer) {
            return null;
        }

        $bookings = PlotBooking::with('plot.town:id,name')
            ->where('customer_id', $customer->id)
            ->latest('booking_date')
            ->latest('id')
            ->get()
            ->map(function ($b) {
                $ledger = Installment::where('plot_booking_id', $b->id)->get(['amount', 'status', 'due_date']);
                $b->paid = (float) $ledger->where('status', 'paid')->sum('amount');
                $b->remaining = (float) $ledger->where('status', '!=', 'paid')->sum('amount');
                $b->overdue = (float) $ledger->filter(fn ($i) => $i->status !== 'paid' && (string) $i->due_date < today()->toDateString())->sum('amount');
                return $b;
            });

        $docs = $customer->documents->groupBy('type');

        return [
            'customer' => $customer,
            'bookings' => $bookings,
            'documents' => $customer->documents,
            'missing' => collect(['cnic_front', 'cnic_back', 'photo'])->reject(fn ($t) => $docs->has($t))->values(),
            'photo' => $docs->get('photo')?->first(),
            'whatsapp' => WhatsApp::url($customer->phone, 'Assalam o Alaikum ' . $customer->name . ','),
        ];
    }

    protected function ownedQuery()
    {
        return Customer::where('customers.user_id', Auth::id());
    }

    protected function ownedCustomer($id): Customer
    {
        return $this->ownedQuery()->findOrFail($id);
    }

    protected function resetDocForm()
    {
        $this->reset(['doc_type', 'doc_title', 'doc_file']);
        $this->uploadIteration++;
    }

    // 3520212345671 => 35202-1234567-1
    protected function formatCnic($cnic): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $cnic);

        if ($digits === '') {
            return null;
        }

        return strlen($digits) === 13
            ? substr($digits, 0, 5) . '-' . substr($digits, 5, 7) . '-' . substr($digits, 12)
            : (string) $cnic;
    }
}
