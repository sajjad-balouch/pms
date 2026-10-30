<?php

namespace App\Livewire\TownOwner;

use App\Support\Tenant;
use App\Http\Controllers\BuyerPortalController;
use App\Models\Installment;
use App\Models\Plot;
use App\Models\PlotBooking;
use App\Services\BookingService;
use App\Support\WhatsApp;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class ManageInstallments extends Component
{
    #[Locked]
    public $plotId;

    // Receive payment form
    public $receivingId = null;
    public $received_date = '';
    public $received_remarks = '';
    public $received_late_fee = 0;

    // Last received installment (shows "send receipt on WhatsApp")
    public $justPaidId = null;

    // Reschedule unpaid installments
    public $showReschedule = false;
    public $rs_count = 12;
    public $rs_interval = 1;
    public $rs_first_due = '';
    public $rs_note = '';

    protected $messages = [
        'received_date.before_or_equal' => 'Aage ki tareekh par wusooli darj nahi ho sakti.',
        'rs_first_due.after_or_equal' => 'Nayi pehli qist aaj ya baad ki honi chahiye.',
    ];

    public function mount($plotId)
    {
        // Pehle koi bhi town owner kisi bhi plot ki qistein dekh/badal sakta tha
        $this->plotId = $this->ownedPlotQuery()->findOrFail($plotId)->id;
    }

    #[Computed]
    public function plot()
    {
        return $this->ownedPlotQuery()->with(['town', 'customer'])->findOrFail($this->plotId);
    }

    #[On('plot-booking-updated')]
    public function bookingUpdated($message = null, $type = 'success')
    {
        unset($this->plot);

        if ($message) {
            session()->flash($type === 'error' ? 'error' : 'success', $message);
        }
    }

    public function startReceive($id)
    {
        $installment = $this->ledgerQuery()->whereIn('status', BookingService::UNPAID)->findOrFail($id);

        $this->resetValidation();
        $this->receivingId = $installment->id;
        $this->received_date = today()->toDateString();
        $this->received_remarks = '';
        $this->received_late_fee = $this->suggestedLateFee($installment, today());
    }

    // Town ki setting: har shuru hone wale late mahine par X% (owner chahe to badal/maaf kar sakta hai)
    public function updatedReceivedDate()
    {
        $installment = $this->receivingId ? $this->ledgerQuery()->find($this->receivingId) : null;

        if ($installment) {
            try {
                $this->received_late_fee = $this->suggestedLateFee($installment, Carbon::parse($this->received_date));
            } catch (\Throwable) {
                // invalid date typed; validation will catch it
            }
        }
    }

    public function waiveLateFee()
    {
        $this->received_late_fee = 0;
    }

    protected function suggestedLateFee(Installment $installment, Carbon $paidOn): float
    {
        $percent = (float) ($this->plot->town->late_fee_percent ?? 0);
        $due = Carbon::parse($installment->due_date);

        if ($percent <= 0 || $installment->installment_number == 0 || $paidOn->lte($due)) {
            return 0;
        }

        $lateMonths = (int) ceil($due->diffInDays($paidOn) / 30);

        return round((float) $installment->amount * $percent / 100 * $lateMonths);
    }

    public function cancelReceive()
    {
        $this->reset(['receivingId', 'received_date', 'received_remarks', 'received_late_fee']);
        $this->resetValidation();
    }

    public function confirmReceive()
    {
        $this->validate([
            'received_date' => 'required|date|before_or_equal:today',
            'received_remarks' => 'nullable|string|max:500',
            'received_late_fee' => 'nullable|numeric|min:0|max:100000000',
        ]);

        $installment = $this->ledgerQuery()->whereIn('status', BookingService::UNPAID)->findOrFail($this->receivingId);

        $installment->update([
            'status' => 'paid',
            'paid_date' => $this->received_date,
            'late_fee' => (float) ($this->received_late_fee ?: 0),
            'received_by' => Auth::id(),
            'remarks' => $this->received_remarks ? trim($this->received_remarks) : $installment->remarks,
        ]);

        // Sab qistein clear => plot Sold
        if (! $this->ledgerQuery()->whereIn('status', BookingService::UNPAID)->exists() && $this->plot->status === 'booked') {
            $this->plot->update(['status' => 'sold', 'sold_at' => today()->toDateString()]);
        }

        $label = $installment->installment_number == 0 ? 'Down payment' : "قسط #{$installment->installment_number}";
        session()->flash('success', "{$label} کی وصولی کامیابی سے درج ہو گئی۔");

        $this->justPaidId = $installment->id;
        $this->cancelReceive();
        unset($this->plot);
    }

    public function undoPayment($id)
    {
        $installment = $this->ledgerQuery()->where('status', 'paid')->findOrFail($id);

        $installment->update(['status' => 'pending', 'paid_date' => null, 'late_fee' => 0, 'received_by' => null]);

        if ($this->plot->status === 'sold') {
            $this->plot->update(['status' => 'booked', 'sold_at' => null]);
        }

        if ($this->justPaidId == $installment->id) {
            $this->justPaidId = null;
        }

        unset($this->plot);
        session()->flash('success', 'Wusooli wapas le li gayi — qist dobara pending hai.');
    }

    /* ---------------- Reschedule ---------------- */

    public function openReschedule()
    {
        $unpaid = $this->ledgerQuery()->whereIn('status', BookingService::UNPAID)->where('installment_number', '>', 0);
        if (! $unpaid->exists()) {
            return;
        }

        $this->resetValidation();
        $this->rs_count = min(360, max(1, (clone $unpaid)->count()));
        $this->rs_interval = 1;
        $this->rs_first_due = today()->addMonthNoOverflow()->toDateString();
        $this->rs_note = '';
        $this->showReschedule = true;
    }

    public function closeReschedule()
    {
        $this->reset(['showReschedule', 'rs_count', 'rs_interval', 'rs_first_due', 'rs_note']);
        $this->resetValidation();
    }

    public function confirmReschedule()
    {
        $this->validate([
            'rs_count' => 'required|integer|min:1|max:360',
            'rs_interval' => 'required|in:1,3,6,12',
            'rs_first_due' => 'required|date|after_or_equal:today',
            'rs_note' => 'nullable|string|max:255',
        ]);

        $booking = app(BookingService::class)->activeBookingFor($this->plot);
        $result = app(BookingService::class)->reschedule($booking, (int) $this->rs_count, Carbon::parse($this->rs_first_due), (int) $this->rs_interval, $this->rs_note);

        $this->closeReschedule();
        unset($this->plot);
        session()->flash('success', "Baqi Rs. " . number_format($result['balance']) . " ab {$result['count']} nayi qiston mein hain (pehli " . Carbon::parse($result['first_due'])->format('d M Y') . ').');
    }

    // Purani (feature se pehle ki) booking ke liye booking record + portal link banana
    public function enablePortal()
    {
        if (! in_array($this->plot->status, ['booked', 'sold'], true)) {
            return;
        }

        app(BookingService::class)->activeBookingFor($this->plot)->portalUrl();
        session()->flash('success', 'Buyer portal link tayyar hai.');
    }

    public function render()
    {
        $plot = $this->plot;
        $booking = $this->activeBooking();
        $installments = $this->ledgerQuery()->with('receivedBy:id,name')->orderBy('installment_number')->orderBy('due_date')->get();
        $stats = BuyerPortalController::ledgerStats($installments);

        $unpaidRegular = $installments->filter(fn ($i) => $i->status !== 'paid' && $i->installment_number > 0);
        $reschedulePreview = [];
        if ($this->showReschedule) {
            try {
                $reschedulePreview = BookingService::schedule(
                    (float) $unpaidRegular->sum('amount'),
                    min(360, max(0, (int) $this->rs_count)),
                    Carbon::parse($this->rs_first_due),
                    in_array((int) $this->rs_interval, [1, 3, 6, 12], true) ? (int) $this->rs_interval : 1
                );
            } catch (\Throwable) {
                $reschedulePreview = [];
            }
        }

        $first = $installments->first();
        $buyerName = $booking?->customer?->name ?? $first?->buyer_name;
        $buyerPhone = $booking?->customer?->phone ?? $first?->buyer_phone;
        $portalUrl = $booking?->portalUrl();
        $greeting = 'Assalam o Alaikum' . ($buyerName ? " {$buyerName}" : '');
        $place = "{$plot->town->name} — Plot #{$plot->plot_number}";

        // Ready WhatsApp messages per installment
        $whatsapp = [];
        foreach ($installments as $i) {
            $what = $i->installment_number == 0 ? 'down payment' : "qist #{$i->installment_number}";
            $amount = 'Rs. ' . number_format((float) $i->amount);

            if ($i->status === 'paid') {
                $balanceAfter = $stats['remaining'];
                $message = "{$greeting}, {$place}: aap ki {$what} {$amount} " . Carbon::parse($i->paid_date)->format('d M Y')
                    . " ko wusool ho gayi. Shukriya! Baqi raqam: Rs. " . number_format($balanceAfter) . '.'
                    . ($booking?->portal_token ? ' Receipt: ' . route('portal.receipt', [$booking->portal_token, $i->id]) : '');
            } else {
                $due = Carbon::parse($i->due_date);
                $message = "{$greeting}, {$place}: aap ki {$what} {$amount} " . $due->format('d M Y')
                    . ($due->isPast() && ! $due->isToday() ? ' ko due thi aur abhi tak ada nahi hui. Barah-e-karam jald ada karein.' : ' ko due hai.')
                    . ($portalUrl ? " Apna hisaab yahan dekhein: {$portalUrl}" : '');
            }

            $whatsapp[$i->id] = WhatsApp::url($buyerPhone, $message);
        }

        return view('livewire.town-owner.manage-installments', [
            'plot' => $plot,
            'booking' => $booking,
            'installments' => $installments,
            'stats' => $stats,
            'buyerName' => $buyerName,
            'buyerPhone' => $buyerPhone,
            'portalUrl' => $portalUrl,
            'portalShare' => $portalUrl
                ? WhatsApp::url($buyerPhone, "{$greeting}, {$place} — aap ke plot ka mukammal hisaab, qistein aur receipts is link par dekhein: {$portalUrl}")
                : null,
            'whatsapp' => $whatsapp,
            'receiving' => $this->receivingId ? $installments->firstWhere('id', (int) $this->receivingId) : null,
            'justPaid' => $this->justPaidId ? $installments->firstWhere('id', (int) $this->justPaidId) : null,
            'unpaidRegular' => $unpaidRegular,
            'reschedulePreview' => $reschedulePreview,
            'lateFeePercent' => (float) ($plot->town->late_fee_percent ?? 0),
            'totalLateFees' => (float) $installments->sum('late_fee'),
        ])->layout('layouts.app');
    }

    protected function ownedPlotQuery()
    {
        return Plot::whereHas('town', fn ($q) => $q->where('user_id', Tenant::ownerId()));
    }

    protected function activeBooking(): ?PlotBooking
    {
        return PlotBooking::with('customer')
            ->where('plot_id', $this->plotId)
            ->where('status', 'active')
            ->latest('id')
            ->first();
    }

    // Current booking's installments (legacy rows have no booking id)
    protected function ledgerQuery()
    {
        $booking = $this->activeBooking();

        return Installment::where('plot_id', $this->plotId)
            ->when($booking, fn ($q) => $q->where('plot_booking_id', $booking->id), fn ($q) => BookingService::scopeUnlinked($q));
    }
}
