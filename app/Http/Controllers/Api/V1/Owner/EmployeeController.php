<?php

namespace App\Http\Controllers\Api\V1\Owner;

use App\Http\Controllers\Api\V1\Transform;
use App\Models\Employee;
use App\Models\EmployeeSalary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EmployeeController extends OwnerController
{
    public function index(Request $request)
    {
        $month = preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', (string) $request->query('month')) ? $request->query('month') : now()->format('Y-m');

        $employees = Employee::whereIn('town_id', $this->ownerTowns()->select('id'))
            ->when($request->filled('town_id'), fn ($q) => $q->where('town_id', $request->integer('town_id')))
            ->withSum(['salaryRecords as advances_total' => fn ($q) => $q->where('type', 'advance')], 'amount')
            ->withSum('salaryRecords as deductions_total', 'advance_deduction')
            ->withSum(['salaryRecords as month_cash' => fn ($q) => $q->where('type', 'salary')->where('salary_month', $month)], 'amount')
            ->withSum(['salaryRecords as month_deducted' => fn ($q) => $q->where('type', 'salary')->where('salary_month', $month)], 'advance_deduction')
            ->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")->orderBy('name')
            ->get();

        return $this->ok($employees->map(fn ($e) => $this->present($e)), null, 200, ['month' => $month]);
    }

    public function store(Request $request)
    {
        $data = $this->payload($request);
        $this->ensureTownOwned((int) $data['town_id']);

        $employee = Employee::create($data + ['status' => 'active']);

        return $this->created($this->present($employee), __('Employee added.'));
    }

    public function update(Request $request, $employee)
    {
        $employee = $this->owned($employee);
        $data = $this->payload($request, true);
        $this->ensureTownOwned((int) $data['town_id']);
        $employee->update($data);

        return $this->ok($this->present($employee), __('Employee updated.'));
    }

    public function payments($employee)
    {
        $employee = $this->owned($employee);

        return $this->ok(EmployeeSalary::where('employee_id', $employee->id)->latest('payment_date')->latest('id')->limit(100)->get()->map(fn ($p) => [
            'id' => $p->id,
            'type' => $p->type, // salary | advance
            'amount' => (float) $p->amount,
            'advance_deduction' => (float) ($p->advance_deduction ?? 0),
            'salary_month' => $p->salary_month,
            'payment_date' => Transform::date($p->payment_date),
            'remarks' => $p->remarks,
        ]));
    }

    // type: salary (with optional advance_deduction + salary_month Y-m) | advance
    public function pay(Request $request, $employee)
    {
        $employee = $this->owned($employee);

        $data = $request->validate([
            'type' => 'required|in:salary,advance',
            'amount' => 'required|numeric|min:0|max:10000000',
            'advance_deduction' => 'nullable|numeric|min:0',
            'salary_month' => 'required_if:type,salary|nullable|date_format:Y-m',
            'payment_date' => 'required|date|before_or_equal:' . today()->addDays(7)->toDateString(),
            'remarks' => 'nullable|string|max:255',
        ]);

        $isSalary = $data['type'] === 'salary';
        $deduction = $isSalary ? (float) ($data['advance_deduction'] ?? 0) : 0;

        $payment = DB::transaction(function () use ($employee, $data, $isSalary, $deduction) {
            $balance = (float) EmployeeSalary::where('employee_id', $employee->id)->lockForUpdate()->toBase()
                ->selectRaw("COALESCE(SUM(CASE WHEN type = 'advance' THEN amount ELSE 0 END), 0) - COALESCE(SUM(advance_deduction), 0) AS bal")
                ->value('bal');

            if ($deduction > max(0, $balance)) {
                throw ValidationException::withMessages(['advance_deduction' => [__('Deduction cannot be more than the advance balance (Rs. :amount).', ['amount' => number_format(max(0, $balance))])]]);
            }

            if ((float) $data['amount'] + $deduction <= 0) {
                throw ValidationException::withMessages(['amount' => [__('Amount must be more than 0.')]]);
            }

            return EmployeeSalary::create([
                'employee_id' => $employee->id,
                'type' => $data['type'],
                'amount' => $data['amount'],
                'advance_deduction' => $deduction,
                'salary_month' => $isSalary ? $data['salary_month'] : null,
                'payment_date' => $data['payment_date'],
                'remarks' => $data['remarks'] ?? null,
            ]);
        });

        return $this->created(['id' => $payment->id], __('Payment recorded.'));
    }

    protected function owned($id): Employee
    {
        return Employee::whereIn('town_id', $this->ownerTowns()->select('id'))->findOrFail($id);
    }

    protected function payload(Request $request, bool $withStatus = false): array
    {
        return $request->validate([
            'town_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'phone' => ['nullable', 'regex:/^[0-9+\-\s]{10,15}$/'],
            'designation' => 'required|string|max:255',
            'monthly_salary' => 'required|numeric|min:0|max:10000000',
        ] + ($withStatus ? ['status' => 'nullable|in:active,inactive'] : []));
    }

    protected function present(Employee $e): array
    {
        $paid = (float) ($e->month_cash ?? 0) + (float) ($e->month_deducted ?? 0);

        return [
            'id' => $e->id,
            'town_id' => $e->town_id,
            'name' => $e->name,
            'phone' => $e->phone,
            'designation' => $e->designation,
            'monthly_salary' => (float) $e->monthly_salary,
            'status' => $e->status,
            'month_paid' => $paid,
            'month_pending' => max(0, (float) $e->monthly_salary - $paid),
            'advance_balance' => max(0, (float) ($e->advances_total ?? 0) - (float) ($e->deductions_total ?? 0)),
        ];
    }
}
