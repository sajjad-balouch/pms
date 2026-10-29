<?php

namespace App\Http\Controllers;

use App\Models\Installment;
use App\Models\PlotBooking;
use App\Services\BookingService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class BuyerPortalController extends Controller
{
    /* ---------------- Public (buyer) ---------------- */

    // /my-plot/{token} — buyer ka apna hisaab, secret link ke zariye (login ki zaroorat nahi)
    public function show(string $token)
    {
        $booking = $this->activeBookingByToken($token);
        $installments = Installment::where('plot_booking_id', $booking->id)->orderBy('installment_number')->get();

        return view('portal.buyer', [
            'booking' => $booking,
            'plot' => $booking->plot,
            'town' => $booking->plot->town,
            'owner' => $booking->plot->town->user,
            'customer' => $booking->customer,
            'installments' => $installments,
            'stats' => self::ledgerStats($installments),
            'token' => $token,
        ]);
    }

    public function receipt(string $token, Installment $installment)
    {
        $booking = $this->activeBookingByToken($token);
        abort_unless((int) $installment->plot_booking_id === (int) $booking->id && $installment->status === 'paid', 404);

        return $this->renderReceipt($installment, $booking, false);
    }

    // QR on the allotment letter: limited info only (no payment ledger)
    public function verify(PlotBooking $booking, string $hash)
    {
        abort_unless(hash_equals($booking->verifyHash(), $hash), 404);

        $booking->load(['plot.town', 'customer', 'transfers']);

        return view('portal.verify', ['booking' => $booking]);
    }

    /* ---------------- Town owner ---------------- */

    public function allotmentLetter(PlotBooking $booking)
    {
        $booking->load(['plot.town.user', 'customer']);
        abort_unless((int) $booking->plot?->town?->user_id === (int) Auth::id(), 403);

        return view('print.allotment-letter', ['booking' => $booking]);
    }

    public function ownerReceipt(Installment $installment)
    {
        $installment->load('plot.town', 'booking');
        abort_unless((int) $installment->plot?->town?->user_id === (int) Auth::id(), 403);
        abort_unless($installment->status === 'paid', 404);

        return $this->renderReceipt($installment, $installment->booking, true);
    }

    /* ---------------- Helpers ---------------- */

    public static function ledgerStats(Collection $installments): array
    {
        $today = today()->toDateString();
        $unpaid = $installments->where('status', '!=', 'paid');
        $total = (float) $installments->sum('amount');
        $paid = (float) $installments->where('status', 'paid')->sum('amount');

        return [
            'total' => $total,
            'paid' => $paid,
            'remaining' => $total - $paid,
            'overdue' => (float) $unpaid->filter(fn ($i) => (string) $i->due_date < $today)->sum('amount'),
            'overdue_count' => $unpaid->filter(fn ($i) => (string) $i->due_date < $today)->count(),
            'next' => $unpaid->sortBy('due_date')->first(),
            'count' => $installments->count(),
            'paid_count' => $installments->where('status', 'paid')->count(),
            'percent' => $total > 0 ? round($paid / $total * 100) : 0,
        ];
    }

    protected function activeBookingByToken(string $token): PlotBooking
    {
        abort_unless(strlen($token) >= 32, 404);

        return PlotBooking::with(['plot.town.user', 'customer'])
            ->where('portal_token', $token)
            ->where('status', 'active')
            ->firstOrFail();
    }

    protected function renderReceipt(Installment $installment, ?PlotBooking $booking, bool $autoPrint)
    {
        $installment->loadMissing('plot.town');

        $ledger = Installment::where('plot_id', $installment->plot_id)
            ->when($booking, fn ($q) => $q->where('plot_booking_id', $booking->id), fn ($q) => BookingService::scopeUnlinked($q))
            ->get();

        return view('print.receipt', [
            'installment' => $installment,
            'booking' => $booking,
            'customer' => $booking?->customer,
            'stats' => self::ledgerStats($ledger),
            'autoPrint' => $autoPrint,
        ]);
    }
}
