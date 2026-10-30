<?php

namespace App\Http\Controllers\Api\V1\Owner;

use App\Http\Controllers\Api\V1\Transform;
use App\Models\Customer;
use App\Models\Installment;
use App\Models\PlotBooking;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends OwnerController
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));

        $page = Customer::where('user_id', $this->ownerId())
            ->withCount(['bookings as active_plots' => fn ($q) => $q->where('status', 'active'), 'documents'])
            ->when($search !== '', fn ($q) => $q->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('cnic', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%")))
            ->orderBy('name')
            ->paginate($this->perPage());

        return $this->paginated($page, fn ($c) => Transform::customer($c, [
            'active_plots' => (int) $c->active_plots,
            'documents_count' => (int) $c->documents_count,
        ]));
    }

    public function show($customer)
    {
        $customer = Customer::where('user_id', $this->ownerId())->with('documents')->findOrFail($customer);

        $bookings = PlotBooking::with('plot.town:id,name')->where('customer_id', $customer->id)->latest('booking_date')->get()
            ->map(function ($b) {
                $ledger = Installment::where('plot_booking_id', $b->id)->get(['amount', 'status', 'due_date']);

                return Transform::booking($b) + [
                    'plot' => $b->plot ? ['id' => $b->plot->id, 'plot_number' => $b->plot->plot_number, 'block' => $b->plot->block_name, 'size' => $b->plot->size, 'town' => $b->plot->town?->name] : null,
                    'paid' => (float) $ledger->where('status', 'paid')->sum('amount'),
                    'remaining' => (float) $ledger->whereIn('status', BookingService::UNPAID)->sum('amount'),
                    'overdue' => (float) $ledger->filter(fn ($i) => $i->status !== 'paid' && (string) $i->due_date < today()->toDateString())->sum('amount'),
                ];
            });

        return $this->ok(Transform::customer($customer, [
            'bookings' => $bookings,
            'documents' => $customer->documents->map(fn ($d) => [
                'id' => $d->id, 'type' => $d->type, 'label' => $d->label(), 'mime' => $d->mime, 'uploaded_at' => Transform::time($d->created_at),
            ]),
        ]));
    }

    public function store(Request $request)
    {
        $customer = Customer::create($this->payload($request) + ['user_id' => $this->ownerId()]);

        return $this->created(Transform::customer($customer), __('Customer created.'));
    }

    public function update(Request $request, $customer)
    {
        $customer = Customer::where('user_id', $this->ownerId())->findOrFail($customer);
        $customer->update($this->payload($request, $customer->id));

        // Reminders / receipts use the name & phone copied on unpaid installments
        Installment::whereIn('plot_booking_id', PlotBooking::select('id')->where('customer_id', $customer->id)->where('status', 'active'))
            ->whereIn('status', BookingService::UNPAID)
            ->update(['buyer_name' => $customer->name, 'buyer_phone' => $customer->phone]);

        return $this->ok(Transform::customer($customer), __('Customer updated.'));
    }

    protected function payload(Request $request, ?int $ignoreId = null): array
    {
        $request->merge(['cnic' => BookingController::formatCnic($request->input('cnic'))]);

        return $request->validate([
            'name' => 'required|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'cnic' => ['nullable', 'regex:/^\d{5}-\d{7}-\d$/', Rule::unique('customers', 'cnic')->where('user_id', $this->ownerId())->ignore($ignoreId)],
            'phone' => ['required', 'regex:/^[0-9+\-\s]{10,15}$/'],
            'address' => 'nullable|string|max:500',
        ]);
    }
}
