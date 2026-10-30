<?php

namespace App\Http\Controllers\Api\V1\Owner;

use App\Http\Controllers\Api\V1\Transform;
use App\Models\Customer;
use App\Models\PlotBooking;
use App\Models\PlotTransfer;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class BookingController extends OwnerController
{
    public function show($plot, BookingService $service)
    {
        $plot = $this->ownedPlot($plot)->load(['town:id,name', 'customer']);
        $summary = $service->summary($plot);

        return $this->ok([
            'plot' => Transform::plot($plot, true),
            'booking' => $summary['booking'] ? Transform::booking($summary['booking']) : null,
            'ledger' => [
                'paid' => $summary['paid'],
                'remaining' => $summary['remaining'],
                'overdue' => $summary['overdue'],
                'installments' => $summary['count'],
                'paid_installments' => $summary['paid_count'],
            ],
            'history' => [
                'bookings' => PlotBooking::with('customer')->where('plot_id', $plot->id)->latest('booking_date')->latest('id')->get()->map(fn ($b) => Transform::booking($b)),
                'transfers' => PlotTransfer::with(['fromCustomer:id,name', 'toCustomer:id,name'])->where('plot_id', $plot->id)->latest('transfer_date')->get()->map(fn ($t) => [
                    'id' => $t->id,
                    'from' => $t->fromCustomer?->name,
                    'to' => $t->toCustomer?->name,
                    'date' => Transform::date($t->transfer_date),
                    'fee' => (float) $t->transfer_fee,
                    'remarks' => $t->remarks,
                ]),
            ],
        ]);
    }

    // customer_id (existing) OR customer{name, phone, father_name, cnic, address} (new)
    public function book(Request $request, $plot, BookingService $service)
    {
        $plot = $this->ownedPlot($plot);
        $finalPrice = max(0, (float) $plot->total_price - (float) $request->input('discount', 0));

        $data = $request->validate($this->customerRules($request) + [
            'booking_date' => 'required|date',
            'discount' => 'nullable|numeric|min:0|max:' . (float) $plot->total_price,
            'down_payment' => 'required|numeric|min:0|max:' . $finalPrice,
            'installments_count' => 'required|integer|min:0|max:360',
            'interval_months' => 'required|in:1,3,6,12',
            'first_due_date' => 'required|date|after_or_equal:booking_date',
            'down_payment_received' => 'boolean',
            'remarks' => 'nullable|string|max:1000',
        ]);

        if ($finalPrice - (float) $data['down_payment'] > 0 && (int) $data['installments_count'] < 1) {
            throw ValidationException::withMessages(['installments_count' => [__('At least one installment is needed for the remaining amount.')]]);
        }

        $booking = DB::transaction(function () use ($request, $data, $plot, $service) {
            return $service->book($plot, $this->resolveCustomer($request), [
                'booking_date' => $data['booking_date'],
                'discount' => $data['discount'] ?? 0,
                'down_payment' => $data['down_payment'],
                'installments_count' => $data['installments_count'],
                'interval_months' => $data['interval_months'],
                'first_due_date' => $data['first_due_date'],
                'down_payment_received' => $data['down_payment_received'] ?? true,
                'remarks' => $data['remarks'] ?? null,
            ]);
        });

        return $this->created(Transform::booking($booking->load('customer')), __('Plot booked and the installment schedule has been created.'));
    }

    public function cancel(Request $request, $plot, BookingService $service)
    {
        $plot = $this->ownedPlot($plot);
        $paid = $service->summary($plot)['paid'];

        $data = $request->validate([
            'refund_amount' => 'required|numeric|min:0|max:' . $paid,
            'refund_date' => 'nullable|date',
            'cancel_reason' => 'required|string|max:1000',
        ]);

        $booking = $service->cancel($plot, $data + ['refund_date' => $data['refund_date'] ?? today()->toDateString()]);

        return $this->ok(Transform::booking($booking->load('customer')), __('Booking cancelled. The plot is available again.'));
    }

    public function transfer(Request $request, $plot, BookingService $service)
    {
        $plot = $this->ownedPlot($plot);

        $data = $request->validate($this->customerRules($request) + [
            'transfer_date' => 'required|date',
            'transfer_fee' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $transfer = DB::transaction(fn () => $service->transfer($plot, $this->resolveCustomer($request), [
            'transfer_date' => $data['transfer_date'],
            'transfer_fee' => $data['transfer_fee'] ?? 0,
            'remarks' => $data['remarks'] ?? null,
        ]));

        return $this->ok(['transfer_id' => $transfer->id, 'booking' => Transform::booking($transfer->booking->load('customer'))], __('Plot transferred.'));
    }

    protected function customerRules(Request $request): array
    {
        if ($request->filled('customer_id')) {
            return ['customer_id' => ['required', Rule::exists('customers', 'id')->where('user_id', $this->ownerId())]];
        }

        $request->merge(['customer' => array_merge((array) $request->input('customer'), [
            'cnic' => self::formatCnic(data_get($request->input('customer'), 'cnic')),
        ])]);

        return [
            'customer.name' => 'required|string|max:255',
            'customer.phone' => ['required', 'regex:/^[0-9+\-\s]{10,15}$/'],
            'customer.father_name' => 'nullable|string|max:255',
            'customer.cnic' => ['nullable', 'regex:/^\d{5}-\d{7}-\d$/', Rule::unique('customers', 'cnic')->where('user_id', $this->ownerId())],
            'customer.address' => 'nullable|string|max:500',
        ];
    }

    protected function resolveCustomer(Request $request): Customer
    {
        if ($request->filled('customer_id')) {
            return Customer::where('user_id', $this->ownerId())->findOrFail($request->integer('customer_id'));
        }

        $c = (array) $request->input('customer');

        return Customer::create([
            'user_id' => $this->ownerId(),
            'name' => trim($c['name']),
            'phone' => trim($c['phone']),
            'father_name' => ! empty($c['father_name']) ? trim($c['father_name']) : null,
            'cnic' => $c['cnic'] ?? null,
            'address' => ! empty($c['address']) ? trim($c['address']) : null,
        ]);
    }

    // 3520212345671 => 35202-1234567-1
    public static function formatCnic($cnic): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $cnic);

        if ($digits === '') {
            return null;
        }

        return strlen($digits) === 13 ? substr($digits, 0, 5) . '-' . substr($digits, 5, 7) . '-' . substr($digits, 12) : (string) $cnic;
    }
}
