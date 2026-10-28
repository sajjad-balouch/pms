<?php

namespace App\Livewire\TownOwner;

use App\Models\Employee;
use App\Models\EmployeeSalary;
use App\Models\Town;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

class ManageEmployees extends Component
{
    // Selected town (only the owner's own towns are allowed)
    #[Url(as: 'town', except: '')]
    public $townId = '';

    #[Url(as: 'month')]
    public $payrollMonth = ''; // Y-m

    public $search = '';

    // Add / Edit Employee Form
    public $editingEmployeeId = null;
    public $name = '';
    public $phone = '';
    public $designation = '';
    public $monthly_salary = '';

    // Payment Form
    public $selected_employee_id = '';
    public $payment_type = 'salary'; // salary | advance
    public $amount = '';
    public $advance_deduction = 0;
    public $salary_month = '';
    public $payment_date = '';
    public $remarks = '';

    // History modal
    public $historyEmployeeId = null;

    protected $messages = [
        'selected_employee_id.required' => 'Mulazim select karein.',
        'selected_employee_id.exists' => 'Ye mulazim is town ka nahi hai.',
        'salary_month.required_if' => 'Salary ka mahina select karein.',
        'phone.regex' => 'Phone number sahi nahi (misal: 0300-1234567).',
    ];

    public function mount($townId = null)
    {
        $requested = $townId ?? $this->townId;
        $towns = $this->towns;

        $this->townId = (string) ($towns->firstWhere('id', (int) $requested)?->id ?? $towns->first()?->id ?? '');
        $this->payrollMonth = $this->validMonth($this->payrollMonth) ?? now()->format('Y-m');
        $this->salary_month = $this->payrollMonth;
        $this->payment_date = today()->toDateString();
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
        $this->resetPaymentForm();
        $this->historyEmployeeId = null;
    }

    public function updatedPayrollMonth()
    {
        $this->payrollMonth = $this->validMonth($this->payrollMonth) ?? now()->format('Y-m');
    }

    // Employee ya mahina badalte hi baqi salary khud bhar dein
    public function updatedSelectedEmployeeId()
    {
        $this->advance_deduction = 0;
        $this->prefillAmount();
    }

    public function updatedPaymentType()
    {
        $this->advance_deduction = 0;
        $this->prefillAmount();
    }

    public function updatedSalaryMonth()
    {
        $this->prefillAmount();
    }

    public function updatedAdvanceDeduction()
    {
        $this->prefillAmount();
    }

    protected function prefillAmount()
    {
        $employee = $this->selectedEmployee();

        if (! $employee || $this->payment_type !== 'salary' || ! $this->validMonth($this->salary_month)) {
            if ($this->payment_type === 'advance') {
                $this->amount = '';
            }
            return;
        }

        $remaining = (float) $employee->monthly_salary - $this->paidForMonth($employee->id, $this->salary_month);
        $this->amount = max(0, round($remaining - (float) ($this->advance_deduction ?: 0)));
    }

    /* ---------------- Employees ---------------- */

    public function saveEmployee()
    {
        $town = $this->requireTown();

        $this->validate([
            'name' => 'required|string|max:255',
            'phone' => ['nullable', 'regex:/^[0-9+\-\s]{10,15}$/'],
            'designation' => 'required|string|max:255',
            'monthly_salary' => 'required|numeric|min:0|max:10000000',
        ]);

        $data = [
            'name' => trim($this->name),
            'phone' => $this->phone ? trim($this->phone) : null,
            'designation' => trim($this->designation),
            'monthly_salary' => $this->monthly_salary,
        ];

        if ($this->editingEmployeeId) {
            $this->ownedEmployee($this->editingEmployeeId)->update($data);
            session()->flash('success', 'ملازم کی تفصیلات اپ ڈیٹ ہو گئی ہیں۔');
        } else {
            Employee::create($data + ['town_id' => $town->id, 'status' => 'active']);
            session()->flash('success', 'ایمپلائی کامیابی سے رجسٹر ہو گیا ہے۔');
        }

        $this->cancelEdit();
    }

    public function editEmployee($id)
    {
        $employee = $this->ownedEmployee($id);

        $this->resetValidation();
        $this->editingEmployeeId = $employee->id;
        $this->name = $employee->name;
        $this->phone = $employee->phone;
        $this->designation = $employee->designation;
        $this->monthly_salary = $employee->monthly_salary;

        $this->dispatch('employee-form-opened');
    }

    public function cancelEdit()
    {
        $this->reset(['editingEmployeeId', 'name', 'phone', 'designation', 'monthly_salary']);
        $this->resetValidation();
    }

    public function toggleStatus($id)
    {
        $employee = $this->ownedEmployee($id);
        $employee->update(['status' => $employee->status === 'active' ? 'inactive' : 'active']);

        session()->flash('success', $employee->name . ' ab ' . ($employee->status === 'active' ? 'Active' : 'Inactive (naukri chhor di)') . ' hai.');
    }

    public function deleteEmployee($id)
    {
        $employee = $this->ownedEmployee($id);

        if (EmployeeSalary::where('employee_id', $employee->id)->exists()) {
            session()->flash('error', 'Is mulazim ki payments ka record hai, is liye delete nahi ho sakta. "Deactivate" kar dein.');
            return;
        }

        if ($this->editingEmployeeId == $employee->id) {
            $this->cancelEdit();
        }

        $employee->delete();
        session()->flash('success', 'Mulazim delete kar diya gaya.');
    }

    /* ---------------- Payments ---------------- */

    public function payEmployee($id)
    {
        $employee = $this->ownedEmployee($id);

        $this->resetValidation();
        $this->selected_employee_id = (string) $employee->id;
        $this->payment_type = 'salary';
        $this->salary_month = $this->payrollMonth;
        $this->advance_deduction = 0;
        $this->payment_date = today()->toDateString();
        $this->prefillAmount();

        $this->dispatch('payment-form-opened');
    }

    public function recordPayment()
    {
        $town = $this->requireTown();
        $isSalary = $this->payment_type === 'salary';

        $this->validate([
            'selected_employee_id' => ['required', Rule::exists('employees', 'id')->where('town_id', $town->id)],
            'payment_type' => 'required|in:salary,advance',
            'amount' => 'required|numeric|min:0|max:10000000',
            'advance_deduction' => 'nullable|numeric|min:0',
            'salary_month' => 'required_if:payment_type,salary|nullable|date_format:Y-m',
            'payment_date' => 'required|date|before_or_equal:' . today()->addDays(7)->toDateString(),
            'remarks' => 'nullable|string|max:255',
        ]);

        $employee = $this->ownedEmployee($this->selected_employee_id);
        $deduction = $isSalary ? (float) ($this->advance_deduction ?: 0) : 0;
        $advanceBalance = $this->outstandingAdvance($employee->id);

        if ($deduction > $advanceBalance) {
            $this->addError('advance_deduction', 'Deduction advance balance (Rs. ' . number_format($advanceBalance) . ') se zyada nahi ho sakti.');
            return;
        }

        if ((float) $this->amount + $deduction <= 0) {
            $this->addError('amount', 'Raqam 0 se zyada honi chahiye.');
            return;
        }

        EmployeeSalary::create([
            'employee_id' => $employee->id,
            'type' => $this->payment_type,
            'amount' => $this->amount,
            'advance_deduction' => $deduction,
            'salary_month' => $isSalary ? $this->salary_month : null,
            'payment_date' => $this->payment_date,
            'remarks' => $this->remarks ?: null,
        ]);

        session()->flash('success', $employee->name . ' ko ' . ($isSalary ? 'salary' : 'advance') . ' Rs. ' . number_format((float) $this->amount)
            . ($deduction > 0 ? ' (advance se Rs. ' . number_format($deduction) . ' adjust)' : '') . ' di gayi.');

        $this->resetPaymentForm();
    }

    public function deletePayment($id)
    {
        $payment = EmployeeSalary::whereHas('employee', fn ($q) => $q->where('town_id', $this->requireTown()->id))->findOrFail($id);
        $payment->delete();

        session()->flash('success', 'Payment entry delete kar di gayi.');
    }

    public function showHistory($id)
    {
        $this->historyEmployeeId = $this->ownedEmployee($id)->id;
    }

    public function closeHistory()
    {
        $this->historyEmployeeId = null;
    }

    /* ---------------- Render ---------------- */

    public function render()
    {
        $town = $this->town;
        $month = $this->validMonth($this->payrollMonth) ?? now()->format('Y-m');

        $all = collect();
        $recentPayments = collect();
        $history = null;

        if ($town) {
            $all = Employee::where('town_id', $town->id)
                ->withSum(['salaryRecords as advances_total' => fn ($q) => $q->where('type', 'advance')], 'amount')
                ->withSum('salaryRecords as deductions_total', 'advance_deduction')
                ->withSum(['salaryRecords as month_cash' => fn ($q) => $q->where('type', 'salary')->where('salary_month', $month)], 'amount')
                ->withSum(['salaryRecords as month_deducted' => fn ($q) => $q->where('type', 'salary')->where('salary_month', $month)], 'advance_deduction')
                ->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
                ->orderBy('name')
                ->get()
                ->each(function ($e) {
                    $e->advance_balance = max(0, (float) $e->advances_total - (float) $e->deductions_total);
                    $e->month_paid = (float) $e->month_cash + (float) $e->month_deducted;
                    $e->month_pending = max(0, (float) $e->monthly_salary - $e->month_paid);
                });

            $recentPayments = EmployeeSalary::whereHas('employee', fn ($q) => $q->where('town_id', $town->id))
                ->with('employee:id,name,designation')
                ->latest('payment_date')
                ->latest('id')
                ->take(15)
                ->get();

            if ($this->historyEmployeeId) {
                $historyEmployee = $all->firstWhere('id', (int) $this->historyEmployeeId);
                $history = $historyEmployee ? [
                    'employee' => $historyEmployee,
                    'payments' => EmployeeSalary::where('employee_id', $historyEmployee->id)->latest('payment_date')->latest('id')->take(100)->get(),
                ] : null;
            }
        }

        $active = $all->where('status', 'active');
        $search = mb_strtolower(trim($this->search));

        $employees = $search === ''
            ? $all
            : $all->filter(fn ($e) => str_contains(mb_strtolower($e->name . ' ' . $e->designation . ' ' . $e->phone), $search));

        $selected = $this->selected_employee_id !== '' ? $all->firstWhere('id', (int) $this->selected_employee_id) : null;

        return view('livewire.town-owner.manage-employees', [
            'town' => $town,
            'towns' => $this->towns,
            'employees' => $employees,
            'activeEmployees' => $active,
            'recentPayments' => $recentPayments,
            'history' => $history,
            'month' => $month,
            'monthLabel' => fn ($m) => $this->monthLabel($m),
            'selectedInfo' => $selected ? [
                'employee' => $selected,
                'advance_balance' => $selected->advance_balance,
                'paid_for_month' => $this->payment_type === 'salary' && $this->validMonth($this->salary_month)
                    ? $this->paidForMonth($selected->id, $this->salary_month)
                    : null,
            ] : null,
            'stats' => [
                'active' => $active->count(),
                'inactive' => $all->count() - $active->count(),
                'payroll' => $active->sum('monthly_salary'),
                'paid' => $all->sum('month_paid'),
                'pending' => $active->sum('month_pending'),
                'advances' => $all->sum('advance_balance'),
            ],
        ])->layout('layouts.app');
    }

    /* ---------------- Helpers ---------------- */

    protected function requireTown(): Town
    {
        $town = $this->town;
        abort_unless($town, 403);

        return $town;
    }

    protected function ownedEmployee($id): Employee
    {
        return Employee::where('town_id', $this->requireTown()->id)->findOrFail($id);
    }

    protected function selectedEmployee(): ?Employee
    {
        return $this->selected_employee_id !== '' && $this->town
            ? Employee::where('town_id', $this->town->id)->find($this->selected_employee_id)
            : null;
    }

    protected function outstandingAdvance($employeeId): float
    {
        $row = EmployeeSalary::where('employee_id', $employeeId)
            ->toBase()
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'advance' THEN amount ELSE 0 END), 0) AS advances, COALESCE(SUM(advance_deduction), 0) AS deducted")
            ->first();

        return max(0, (float) $row->advances - (float) $row->deducted);
    }

    protected function paidForMonth($employeeId, $month): float
    {
        return (float) EmployeeSalary::where('employee_id', $employeeId)
            ->where('type', 'salary')
            ->where('salary_month', $month)
            ->toBase()
            ->selectRaw('COALESCE(SUM(amount + advance_deduction), 0) AS total')
            ->value('total');
    }

    protected function resetPaymentForm()
    {
        $this->reset(['selected_employee_id', 'payment_type', 'amount', 'advance_deduction', 'remarks']);
        $this->salary_month = $this->payrollMonth;
        $this->payment_date = today()->toDateString();
        $this->resetValidation();
    }

    protected function validMonth($value): ?string
    {
        return is_string($value) && preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $value) ? $value : null;
    }

    // "2026-10" => "October 2026"; old free-text values ("September 2026") are shown as they are
    protected function monthLabel($value): string
    {
        return $this->validMonth($value) ? Carbon::createFromFormat('!Y-m', $value)->format('F Y') : (string) $value;
    }
}
