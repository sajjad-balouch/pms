<?php

namespace App\Http\Controllers\Api\V1\Owner;

use App\Http\Controllers\Api\V1\Transform;
use App\Http\Controllers\BuyerPortalController;
use App\Models\Installment;
use App\Models\Plot;
use App\Models\PlotBooking;
use App\Services\BookingService;
use App\Support\WhatsApp;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstallmentController extends OwnerController
{
    public function index($plot)
    {
        $plot = $this->ownedPlot($plot)->load('town');
        $booking = $this->activeBooking($plot->id);
        $installments = $this->ledger($plot->id, $booking)->with('receivedBy:id,name')->orderBy('installment_number')->orderBy('due_date')->get();
        $stats = BuyerPortalController::ledgerStats($installments);

        $buyerName = $booking?->customer?->name ?? $installments->first()?->buyer_name;
        $buyerPhone = $booking?->customer?->phone ?? $installments->first()?->buyer_phone;
        $portalUrl = $booking?->portalUrl();

        return $this->ok([
            'plot' => Transform::plot($plot),
            'booking' => $booking ? Transform::booking($booking) : null,
            'buyer' => ['name' => $buyerName, 'phone' => $buyerPhone],
            'summary' => [
                'total' => round((float) $stats['total'], 2),
                'paid' => round((float) $stats['paid'], 2),
                'remaining' => round((float) $stats['remaining'], 2),
                'overdue' => round((float) $stats['overdue'], 2),
                'overdue_count' => $stats['overdue_count'],
                'paid_count' => $stats['paid_count'],
                'count' => $stats['count'],
                'percent' => $stats['percent'],
                'next_due' => $stats['next'] ? Transform::installment($stats['next']) : null,
            ],
            'late_fee_percent' => (float) ($plot->town->late_fee_percent ?? 0),
            'portal' => $portalUrl ? [
                'url' => $portalUrl,
                'share_whatsapp_url' => WhatsApp::url($buyerPhone, __(':place — see your full account, installments and receipts here: :url', [
                    'place' => $plot->town->name . ' — Plot #' . $plot->plot_number, 'url' => $portalUrl,
                ])),
            ] : null,
            'installments' => $installments->map(function ($i) use ($plot, $buyerPhone, $buyerName, $portalUrl, $booking) {
                $row = Transform::installment($i);
                // receipt_url needs a website login; this one opens without login (shareable, safe for the app's browser)
                $row['public_receipt_url'] = $i->status === 'paid' && $booking?->portal_token ? route('portal.receipt', [$booking->portal_token, $i->id]) : null;
                $row['suggested_late_fee'] = $this->suggestedLateFee($i, $plot, today());
                $row['whatsapp_url'] = WhatsApp::url($buyerPhone, $this->message($i, $plot, $buyerName, $portalUrl));

                return $row;
            }),
        ]);
    }

    public function receive(Request $request, Installment $installment)
    {
        [$plot, $booking] = $this->authorizeInstallment($installment);
        abort_unless(in_array($installment->status, BookingService::UNPAID, true), 422, __('This installment is already paid.'));

        $data = $request->validate([
            'paid_date' => 'required|date|before_or_equal:today',
            'late_fee' => 'nullable|numeric|min:0|max:100000000',
            'remarks' => 'nullable|string|max:500',
        ]);

        $installment->update([
            'status' => 'paid',
            'paid_date' => $data['paid_date'],
            'late_fee' => array_key_exists('late_fee', $data) && $data['late_fee'] !== null
                ? (float) $data['late_fee']
                : $this->suggestedLateFee($installment, $plot, Carbon::parse($data['paid_date'])),
            'received_by' => Auth::id(),
            'remarks' => ! empty($data['remarks']) ? trim($data['remarks']) : $installment->remarks,
        ]);

        if ($plot->status === 'booked' && ! $this->ledger($plot->id, $booking)->whereIn('status', BookingService::UNPAID)->exists()) {
            $plot->update(['status' => 'sold', 'sold_at' => today()->toDateString()]);
        }

        $installment->load('receivedBy:id,name');
        $buyerPhone = $booking?->customer?->phone ?? $installment->buyer_phone;

        return $this->ok(Transform::installment($installment) + [
            'receipt_whatsapp_url' => WhatsApp::url($buyerPhone, $this->message($installment, $plot->load('town'), $booking?->customer?->name ?? $installment->buyer_name, $booking?->portalUrl())),
        ], __('Payment received.'));
    }

    public function undo(Installment $installment)
    {
        [$plot] = $this->authorizeInstallment($installment);
        abort_unless($installment->status === 'paid', 422, __('This installment is not paid.'));

        $installment->update(['status' => 'pending', 'paid_date' => null, 'late_fee' => 0, 'received_by' => null]);

        if ($plot->status === 'sold') {
            $plot->update(['status' => 'booked', 'sold_at' => null]);
        }

        return $this->ok(Transform::installment($installment), __('Payment reversed — the installment is pending again.'));
    }

    protected function authorizeInstallment(Installment $installment): array
    {
        $plot = $this->ownedPlot($installment->plot_id)->load('town');
        $booking = $this->activeBooking($plot->id);

        // Must belong to the plot's current booking (not an old cancelled one)
        abort_unless($this->ledger($plot->id, $booking)->whereKey($installment->id)->exists(), 404);

        return [$plot, $booking];
    }

    protected function activeBooking(int $plotId): ?PlotBooking
    {
        return PlotBooking::with('customer')->where('plot_id', $plotId)->where('status', 'active')->latest('id')->first();
    }

    protected function ledger(int $plotId, ?PlotBooking $booking)
    {
        return Installment::where('plot_id', $plotId)
            ->when($booking, fn ($q) => $q->where('plot_booking_id', $booking->id), fn ($q) => BookingService::scopeUnlinked($q));
    }

    protected function suggestedLateFee(Installment $i, Plot $plot, Carbon $paidOn): float
    {
        $percent = (float) ($plot->town->late_fee_percent ?? 0);
        $due = Carbon::parse($i->due_date);

        if ($i->status === 'paid' || $percent <= 0 || (int) $i->installment_number === 0 || $paidOn->lte($due)) {
            return 0;
        }

        return round((float) $i->amount * $percent / 100 * (int) ceil($due->diffInDays($paidOn) / 30));
    }

    protected function message(Installment $i, Plot $plot, ?string $name, ?string $portalUrl): string
    {
        $vars = [
            'client' => $name ?: __('Customer'),
            'place' => $plot->town->name . ' — Plot #' . $plot->plot_number,
            'what' => (int) $i->installment_number === 0 ? __('down payment') : __('installment #:n', ['n' => $i->installment_number]),
            'amount' => number_format((float) $i->amount),
        ];

        if ($i->status === 'paid') {
            return __('Assalam o Alaikum :client, :place: your :what of Rs. :amount has been received on :date. Thank you!', $vars + [
                'date' => Carbon::parse($i->paid_date)->format('d M Y'),
            ]);
        }

        $due = Carbon::parse($i->due_date);

        return __($due->lt(today())
            ? 'Assalam o Alaikum :client, :place: your :what of Rs. :amount was due on :date and is still unpaid. Please pay soon.'
            : 'Assalam o Alaikum :client, :place: your :what of Rs. :amount is due on :date.', $vars + ['date' => $due->format('d M Y')])
            . ($portalUrl ? ' ' . __('Your account: :url', ['url' => $portalUrl]) : '');
    }
}
