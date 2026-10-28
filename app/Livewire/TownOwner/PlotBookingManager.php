<?php

namespace App\Livewire\TownOwner;

use App\Models\Customer;
use App\Models\Plot;
use App\Models\PlotBooking;
use App\Models\PlotTransfer;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Modals for one plot: book, cancel booking (refund), transfer, details/history.
 * Opened from anywhere with: $dispatch('open-plot-action', { plotId: 5, action: 'book' })
 */
class PlotBookingManager extends Component
{
    const ACTIONS = ['book', 'cancel', 'transfer', 'details'];

    #[Locked]
    public $townId;

    #[Locked]
    public $plotId = null;

    #[Locked]
    public $action = null;

    // Customer (used by book + transfer)
    public $customerMode = 'new'; // new | existing
    public $customerSearch = '';
    public $selectedCustomerId = null;
    public $c_name, $c_father_name, $c_cnic, $c_phone, $c_address;

    // Booking
    public $booking_date, $discount = 0, $down_payment, $installments_count, $interval_months = 1, $first_due_date;
    public $down_payment_received = true;
    public $remarks;

    // Cancel
    public $refund_amount = 0, $refund_date, $cancel_reason;

    // Transfer
    public $transfer_fee = 0, $transfer_date, $transfer_remarks;

    protected $messages = [
        'c_name.required' => 'Customer ka naam zaroori hai.',
        'c_phone.required' => 'Customer ka phone number zaroori hai.',
        'c_phone.regex' => 'Phone number sahi nahi (misal: 0300-1234567).',
        'c_cnic.regex' => 'CNIC is format mein likhein: 35202-1234567-1',
        'c_cnic.unique' => 'Is CNIC ka customer pehle se maujood hai — "Existing Customer" se select karein.',
        'selectedCustomerId.required' => 'Customer select karein.',
        'down_payment.max' => 'Down payment final price se zyada nahi ho sakti.',
        'discount.max' => 'Discount plot ki qeemat se zyada nahi ho sakta.',
        'first_due_date.after_or_equal' => 'Pehli qist ki tareekh booking date ke baad honi chahiye.',
        'refund_amount.max' => 'Refund wusool shuda raqam se zyada nahi ho sakta.',
        'cancel_reason.required' => 'Cancel karne ki wajah likhein.',
    ];

    public function mount($townId)
    {
        $this->townId = $townId;
    }

    #[On('open-plot-action')]
    public function open($plotId, $action)
    {
        if (! in_array($action, self::ACTIONS, true)) {
            return;
        }

        $plot = $this->ownedPlot($plotId);

        $allowed = match ($action) {
            'book' => $plot->status === 'available',
            'cancel', 'transfer' => in_array($plot->status, ['booked', 'sold'], true),
            default => true,
        };

        if (! $allowed) {
            $this->dispatch('plot-booking-updated', message: 'Is plot par ye action nahi ho sakta.', type: 'error');
            return;
        }

        $this->resetForm();
        $this->plotId = $plot->id;
        $this->action = $action;

        $today = today();

        if ($action === 'book') {
            $this->booking_date = $today->toDateString();
            $this->down_payment = (float) $plot->down_payment;
            $this->installments_count = (int) ($plot->total_installments ?: 1);
            $this->first_due_date = $today->copy()->addMonthNoOverflow()->toDateString();
        } elseif ($action === 'cancel') {
            $this->refund_date = $today->toDateString();
        } elseif ($action === 'transfer') {
            $this->transfer_date = $today->toDateString();
        }
    }

    public function switchAction($action)
    {
        if ($this->plotId) {
            $this->open($this->plotId, $action);
        }
    }

    public function close()
    {
        $this->resetForm();
        $this->reset(['plotId', 'action']);
    }

    public function setCustomerMode($mode)
    {
        $this->customerMode = $mode === 'existing' ? 'existing' : 'new';
        $this->selectedCustomerId = null;
        $this->resetValidation();
    }

    public function selectCustomer($id)
    {
        $customer = Customer::where('user_id', Auth::id())->find($id);
        if ($customer) {
            $this->selectedCustomerId = $customer->id;
            $this->customerSearch = '';
            $this->resetValidation('selectedCustomerId');
        }
    }

    public function confirmBooking()
    {
        $plot = $this->ownedPlot($this->plotId);
        $this->c_cnic = $this->formatCnic($this->c_cnic);

        $finalPrice = max(0, (float) $plot->total_price - (float) $this->discount);

        $this->validate($this->customerRules() + [
            'booking_date' => 'required|date',
            'discount' => 'nullable|numeric|min:0|max:' . (float) $plot->total_price,
            'down_payment' => 'required|numeric|min:0|max:' . $finalPrice,
            'installments_count' => 'required|integer|min:0|max:360',
            'interval_months' => 'required|in:1,3,6,12',
            'first_due_date' => 'required|date|after_or_equal:booking_date',
            'down_payment_received' => 'boolean',
            'remarks' => 'nullable|string|max:1000',
        ]);

        if ($finalPrice - (float) $this->down_payment > 0 && (int) $this->installments_count < 1) {
            $this->addError('installments_count', 'Baqi raqam ke liye kam az kam 1 installment honi chahiye.');
            return;
        }

        $customer = DB::transaction(function () use ($plot) {
            $customer = $this->resolveCustomer();

            app(BookingService::class)->book($plot, $customer, [
                'booking_date' => $this->booking_date,
                'discount' => $this->discount ?: 0,
                'down_payment' => $this->down_payment,
                'installments_count' => $this->installments_count,
                'interval_months' => $this->interval_months,
                'first_due_date' => $this->first_due_date,
                'down_payment_received' => $this->down_payment_received,
                'remarks' => $this->remarks,
            ]);

            return $customer;
        });

        $this->finish("Plot #{$plot->plot_number} {$customer->name} ke naam book ho gaya, installments ka schedule ban gaya.");
    }

    public function confirmCancel()
    {
        $plot = $this->ownedPlot($this->plotId);
        $paid = app(BookingService::class)->summary($plot)['paid'];

        $this->validate([
            'refund_amount' => 'required|numeric|min:0|max:' . $paid,
            'refund_date' => 'required_unless:refund_amount,0|nullable|date',
            'cancel_reason' => 'required|string|max:1000',
        ]);

        app(BookingService::class)->cancel($plot, [
            'refund_amount' => $this->refund_amount,
            'refund_date' => $this->refund_date,
            'cancel_reason' => $this->cancel_reason,
        ]);

        $this->finish("Plot #{$plot->plot_number} ki booking cancel ho gayi, plot dobara available hai.");
    }

    public function confirmTransfer()
    {
        $plot = $this->ownedPlot($this->plotId);
        $this->c_cnic = $this->formatCnic($this->c_cnic);

        $this->validate($this->customerRules() + [
            'transfer_date' => 'required|date',
            'transfer_fee' => 'nullable|numeric|min:0',
            'transfer_remarks' => 'nullable|string|max:1000',
        ]);

        $customer = DB::transaction(function () use ($plot) {
            $customer = $this->resolveCustomer();

            app(BookingService::class)->transfer($plot, $customer, [
                'transfer_date' => $this->transfer_date,
                'transfer_fee' => $this->transfer_fee ?: 0,
                'remarks' => $this->transfer_remarks,
            ]);

            return $customer;
        });

        $this->finish("Plot #{$plot->plot_number} {$customer->name} ke naam transfer ho gaya.");
    }

    public function render()
    {
        $data = [
            'plot' => null,
            'summary' => null,
            'schedule' => [],
            'finalPrice' => 0,
            'balance' => 0,
            'bookings' => collect(),
            'transfers' => collect(),
            'customerResults' => collect(),
            'selectedCustomer' => null,
        ];

        if ($this->plotId) {
            $plot = Plot::with('customer')->where('town_id', $this->townId)->find($this->plotId);

            if (! $plot) {
                $this->close();
            } else {
                $data['plot'] = $plot;

                if ($this->action === 'book') {
                    $data['finalPrice'] = max(0, (float) $plot->total_price - (float) $this->discount);
                    $data['balance'] = max(0, $data['finalPrice'] - (float) $this->down_payment);
                    $data['schedule'] = $this->previewSchedule($data['balance']);
                } else {
                    $data['summary'] = app(BookingService::class)->summary($plot);
                }

                if ($this->action === 'details') {
                    $data['bookings'] = PlotBooking::with('customer')->where('plot_id', $plot->id)->latest('booking_date')->latest('id')->get();
                    $data['transfers'] = PlotTransfer::with(['fromCustomer', 'toCustomer'])->where('plot_id', $plot->id)->latest('transfer_date')->latest('id')->get();
                }
            }
        }

        if (in_array($this->action, ['book', 'transfer'], true) && $this->customerMode === 'existing') {
            $search = trim($this->customerSearch);

            $data['customerResults'] = Customer::where('user_id', Auth::id())
                ->when($search !== '', fn ($q) => $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('cnic', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                }))
                ->orderBy('name')
                ->limit(8)
                ->get();

            $data['selectedCustomer'] = $this->selectedCustomerId
                ? Customer::where('user_id', Auth::id())->find($this->selectedCustomerId)
                : null;
        }

        return view('livewire.town-owner.plot-booking-manager', $data);
    }

    protected function previewSchedule(float $balance): array
    {
        try {
            $firstDue = Carbon::parse($this->first_due_date);
        } catch (\Throwable) {
            return [];
        }

        $count = min(360, max(0, (int) $this->installments_count));
        $interval = in_array((int) $this->interval_months, [1, 3, 6, 12], true) ? (int) $this->interval_months : 1;

        return BookingService::schedule($balance, $count, $firstDue, $interval);
    }

    protected function customerRules(): array
    {
        if ($this->customerMode === 'existing') {
            return [
                'selectedCustomerId' => ['required', Rule::exists('customers', 'id')->where('user_id', Auth::id())],
            ];
        }

        return [
            'c_name' => 'required|string|max:255',
            'c_father_name' => 'nullable|string|max:255',
            'c_cnic' => ['nullable', 'regex:/^\d{5}-\d{7}-\d$/', Rule::unique('customers', 'cnic')->where('user_id', Auth::id())],
            'c_phone' => ['required', 'regex:/^[0-9+\-\s]{10,15}$/'],
            'c_address' => 'nullable|string|max:500',
        ];
    }

    protected function resolveCustomer(): Customer
    {
        if ($this->customerMode === 'existing') {
            return Customer::where('user_id', Auth::id())->findOrFail($this->selectedCustomerId);
        }

        return Customer::create([
            'user_id' => Auth::id(),
            'name' => trim($this->c_name),
            'father_name' => $this->c_father_name ? trim($this->c_father_name) : null,
            'cnic' => $this->c_cnic ?: null,
            'phone' => trim($this->c_phone),
            'address' => $this->c_address ? trim($this->c_address) : null,
        ]);
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

    protected function ownedPlot($id): Plot
    {
        return Plot::where('town_id', $this->townId)
            ->whereHas('town', fn ($q) => $q->where('user_id', Auth::id()))
            ->findOrFail($id);
    }

    protected function finish(string $message)
    {
        $this->close();
        $this->dispatch('plot-booking-updated', message: $message, type: 'success');
    }

    protected function resetForm()
    {
        $this->resetValidation();
        $this->reset([
            'customerMode', 'customerSearch', 'selectedCustomerId',
            'c_name', 'c_father_name', 'c_cnic', 'c_phone', 'c_address',
            'booking_date', 'discount', 'down_payment', 'installments_count', 'interval_months', 'first_due_date',
            'down_payment_received', 'remarks',
            'refund_amount', 'refund_date', 'cancel_reason',
            'transfer_fee', 'transfer_date', 'transfer_remarks',
        ]);
    }
}
