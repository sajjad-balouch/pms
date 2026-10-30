<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Installment;
use App\Models\Plot;
use App\Models\PlotBooking;
use App\Models\PlotTransfer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingService
{
    const UNPAID = ['pending', 'overdue'];

    /**
     * Equal installments in whole rupees; the last one absorbs the rounding difference.
     */
    public static function schedule(float $balance, int $count, Carbon $firstDue, int $intervalMonths = 1): array
    {
        if ($count < 1 || $balance <= 0) {
            return [];
        }

        $each = floor($balance / $count);
        $rows = [];

        for ($i = 1; $i <= $count; $i++) {
            $rows[] = [
                'number' => $i,
                'due_date' => $firstDue->copy()->addMonthsNoOverflow(($i - 1) * $intervalMonths),
                'amount' => $i === $count ? round($balance - $each * ($count - 1), 2) : $each,
            ];
        }

        return $rows;
    }

    public function book(Plot $plot, Customer $customer, array $data): PlotBooking
    {
        return DB::transaction(function () use ($plot, $customer, $data) {
            $plot = Plot::whereKey($plot->id)->lockForUpdate()->firstOrFail();

            if ($plot->status !== 'available') {
                throw ValidationException::withMessages(['plot' => 'Ye plot ab available nahi hai.']);
            }

            $bookingDate = Carbon::parse($data['booking_date']);
            $discount = (float) ($data['discount'] ?? 0);
            $finalPrice = max(0, (float) $plot->total_price - $discount);
            $downPayment = min((float) $data['down_payment'], $finalPrice);
            $interval = (int) ($data['interval_months'] ?? 1);

            $schedule = self::schedule(
                $finalPrice - $downPayment,
                (int) $data['installments_count'],
                Carbon::parse($data['first_due_date']),
                $interval
            );

            $booking = PlotBooking::create([
                'plot_id' => $plot->id,
                'customer_id' => $customer->id,
                'booking_date' => $bookingDate->toDateString(),
                'total_price' => $plot->total_price,
                'discount' => $discount,
                'final_price' => $finalPrice,
                'down_payment' => $downPayment,
                'installments_count' => count($schedule),
                'installment_amount' => $schedule[0]['amount'] ?? 0,
                'interval_months' => $interval,
                'first_due_date' => isset($schedule[0]) ? $schedule[0]['due_date']->toDateString() : null,
                'status' => 'active',
                'portal_token' => PlotBooking::newPortalToken(),
                'remarks' => $data['remarks'] ?? null,
            ]);

            $now = now();
            $common = [
                'plot_id' => $plot->id,
                'plot_booking_id' => $booking->id,
                'buyer_name' => $customer->name,
                'buyer_phone' => $customer->phone,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $rows = [];

            // Down payment is saved as installment #0 so the dashboard counts it as collected cash
            if ($downPayment > 0) {
                $received = ! empty($data['down_payment_received']);
                $rows[] = $common + [
                    'installment_number' => 0,
                    'amount' => $downPayment,
                    'due_date' => $bookingDate->toDateString(),
                    'paid_date' => $received ? $bookingDate->toDateString() : null,
                    'status' => $received ? 'paid' : 'pending',
                    'remarks' => 'Down Payment',
                ];
            }

            foreach ($schedule as $row) {
                $rows[] = $common + [
                    'installment_number' => $row['number'],
                    'amount' => $row['amount'],
                    'due_date' => $row['due_date']->toDateString(),
                    'paid_date' => null,
                    'status' => 'pending',
                    'remarks' => null,
                ];
            }

            foreach (array_chunk($rows, 200) as $chunk) {
                Installment::insert($chunk);
            }

            $plot->update([
                'status' => 'booked',
                'customer_id' => $customer->id,
                'booked_at' => $bookingDate->toDateString(),
                'sold_at' => null,
            ]);

            return $booking;
        });
    }

    public function cancel(Plot $plot, array $data): PlotBooking
    {
        return DB::transaction(function () use ($plot, $data) {
            $plot = Plot::whereKey($plot->id)->lockForUpdate()->firstOrFail();

            if (! in_array($plot->status, ['booked', 'sold'], true)) {
                throw ValidationException::withMessages(['plot' => 'Is plot ki koi active booking nahi hai.']);
            }

            $booking = $this->activeBookingFor($plot);

            // Paid installments record ke liye rehti hain, baqi (unpaid) khatam
            Installment::where('plot_booking_id', $booking->id)->whereIn('status', self::UNPAID)->delete();

            $refund = (float) ($data['refund_amount'] ?? 0);
            $booking->update([
                'status' => 'cancelled',
                'portal_token' => null, // buyer portal link band
                'cancelled_at' => now()->toDateString(),
                'cancel_reason' => $data['cancel_reason'] ?? null,
                'refund_amount' => $refund,
                'refund_date' => $refund > 0 ? Carbon::parse($data['refund_date'])->toDateString() : null,
            ]);

            $plot->update([
                'status' => 'available',
                'customer_id' => null,
                'booked_at' => null,
                'sold_at' => null,
            ]);

            return $booking;
        });
    }

    public function transfer(Plot $plot, Customer $to, array $data): PlotTransfer
    {
        return DB::transaction(function () use ($plot, $to, $data) {
            $plot = Plot::whereKey($plot->id)->lockForUpdate()->firstOrFail();

            if (! in_array($plot->status, ['booked', 'sold'], true)) {
                throw ValidationException::withMessages(['plot' => 'Sirf booked/sold plot transfer ho sakta hai.']);
            }

            $booking = $this->activeBookingFor($plot);

            if ((int) $booking->customer_id === (int) $to->id) {
                throw ValidationException::withMessages(['plot' => 'Plot pehle se isi customer ke naam hai.']);
            }

            $transfer = PlotTransfer::create([
                'plot_id' => $plot->id,
                'plot_booking_id' => $booking->id,
                'from_customer_id' => $booking->customer_id,
                'to_customer_id' => $to->id,
                'transfer_date' => Carbon::parse($data['transfer_date'])->toDateString(),
                'transfer_fee' => (float) ($data['transfer_fee'] ?? 0),
                'remarks' => $data['remarks'] ?? null,
            ]);

            // Naya owner, naya portal link — purane owner ka link band ho jata hai
            $booking->update(['customer_id' => $to->id, 'portal_token' => PlotBooking::newPortalToken()]);
            $plot->update(['customer_id' => $to->id]);

            Installment::where('plot_booking_id', $booking->id)
                ->whereIn('status', self::UNPAID)
                ->update(['buyer_name' => $to->name, 'buyer_phone' => $to->phone]);

            return $transfer;
        });
    }

    /**
     * Defaulter ka plan dobara: tamam unpaid (regular) qistein hata kar baqi raqam nayi qiston mein.
     * Paid qistein aur down payment waisi hi rehti hain.
     */
    public function reschedule(PlotBooking $booking, int $count, Carbon $firstDue, int $intervalMonths = 1, ?string $note = null): array
    {
        return DB::transaction(function () use ($booking, $count, $firstDue, $intervalMonths, $note) {
            $booking = PlotBooking::whereKey($booking->id)->lockForUpdate()->firstOrFail();

            $unpaid = Installment::where('plot_booking_id', $booking->id)
                ->whereIn('status', self::UNPAID)
                ->where('installment_number', '>', 0);

            $balance = (float) (clone $unpaid)->sum('amount');
            if ($balance <= 0) {
                throw ValidationException::withMessages(['rs_count' => 'Koi baqi qist nahi jise reschedule kiya ja sake.']);
            }

            $template = (clone $unpaid)->first();
            $startNumber = (int) Installment::where('plot_booking_id', $booking->id)->where('status', 'paid')->max('installment_number') + 1;
            $schedule = self::schedule($balance, $count, $firstDue, $intervalMonths);

            $unpaid->delete();

            $now = now();
            $remark = 'Rescheduled ' . $now->format('d M Y') . ($note ? ': ' . $note : '');
            $rows = array_map(fn ($row) => [
                'plot_id' => $booking->plot_id,
                'plot_booking_id' => $booking->id,
                'buyer_name' => $template?->buyer_name,
                'buyer_phone' => $template?->buyer_phone,
                'installment_number' => $startNumber + $row['number'] - 1,
                'amount' => $row['amount'],
                'due_date' => $row['due_date']->toDateString(),
                'paid_date' => null,
                'status' => 'pending',
                'remarks' => $row['number'] === 1 ? $remark : null,
                'created_at' => $now,
                'updated_at' => $now,
            ], $schedule);

            foreach (array_chunk($rows, 200) as $chunk) {
                Installment::insert($chunk);
            }

            $booking->update([
                'remarks' => trim(($booking->remarks ? $booking->remarks . "\n" : '') . $remark . " — Rs. " . number_format($balance) . " in {$count} installments"),
            ]);

            return ['balance' => $balance, 'count' => count($schedule), 'first_due' => $firstDue->toDateString()];
        });
    }

    /**
     * Paid / remaining / overdue figures for the plot's current booking.
     */
    public function summary(Plot $plot): array
    {
        $booking = PlotBooking::with('customer')
            ->where('plot_id', $plot->id)
            ->where('status', 'active')
            ->latest('id')
            ->first();

        $row = Installment::where('plot_id', $plot->id)
            ->when($booking, fn ($q) => $q->where('plot_booking_id', $booking->id), fn ($q) => self::scopeUnlinked($q))
            ->toBase()
            ->selectRaw("
                COALESCE(SUM(CASE WHEN status = 'paid' THEN amount ELSE 0 END), 0) AS paid,
                COALESCE(SUM(CASE WHEN status <> 'paid' THEN amount ELSE 0 END), 0) AS remaining,
                COALESCE(SUM(CASE WHEN status <> 'paid' AND due_date < ? THEN amount ELSE 0 END), 0) AS overdue,
                COUNT(*) AS total_count,
                COALESCE(SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END), 0) AS paid_count
            ", [today()->toDateString()])
            ->first();

        return [
            'booking' => $booking,
            'paid' => (float) $row->paid,
            'remaining' => (float) $row->remaining,
            'overdue' => (float) $row->overdue,
            'count' => (int) $row->total_count,
            'paid_count' => (int) $row->paid_count,
        ];
    }

    /**
     * Returns the active booking. Plots booked before this feature existed get a booking
     * record created on the fly, and their old installments are linked to it.
     */
    public function activeBookingFor(Plot $plot): PlotBooking
    {
        $booking = PlotBooking::where('plot_id', $plot->id)->where('status', 'active')->latest('id')->first();
        if ($booking) {
            return $booking;
        }

        return DB::transaction(function () use ($plot) {
            $customerId = $this->resolveLegacyCustomerId($plot);

            $booking = PlotBooking::create([
                'plot_id' => $plot->id,
                'customer_id' => $customerId,
                'booking_date' => ($plot->booked_at ?? $plot->updated_at ?? now())->toDateString(),
                'total_price' => $plot->total_price,
                'discount' => 0,
                'final_price' => $plot->total_price,
                'down_payment' => $plot->down_payment ?? 0,
                'installments_count' => (int) $plot->total_installments,
                'status' => 'active',
                'remarks' => 'Legacy booking (auto-created)',
            ]);

            self::scopeUnlinked(Installment::where('plot_id', $plot->id))->update(['plot_booking_id' => $booking->id]);

            if ((int) $plot->customer_id !== (int) $customerId) {
                $plot->update(['customer_id' => $customerId]);
            }

            return $booking;
        });
    }

    /**
     * Installments with no booking, or pointing to a booking row that no longer exists
     * (e.g. after the bookings table was re-created). Cancelled bookings still exist, so their rows are never taken.
     */
    public static function scopeUnlinked($query)
    {
        return $query->where(fn ($q) => $q->whereNull('plot_booking_id')
            ->orWhereNotIn('plot_booking_id', PlotBooking::select('id')));
    }

    /**
     * The plot's customer if it really exists; otherwise one is rebuilt from the buyer name/phone
     * saved on its installments (so old bookings still get a customer record).
     */
    protected function resolveLegacyCustomerId(Plot $plot): ?int
    {
        if ($plot->customer_id && Customer::whereKey($plot->customer_id)->exists()) {
            return (int) $plot->customer_id;
        }

        $source = Installment::where('plot_id', $plot->id)
            ->whereNotNull('buyer_name')
            ->where('buyer_name', '!=', '')
            ->latest('id')
            ->first();

        $ownerId = $plot->town()->value('user_id');

        if (! $source || ! $ownerId) {
            return null;
        }

        return Customer::firstOrCreate([
            'user_id' => $ownerId,
            'name' => trim($source->buyer_name),
            'phone' => $source->buyer_phone ? trim($source->buyer_phone) : null,
        ])->id;
    }
}
