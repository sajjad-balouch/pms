<?php

namespace App\Livewire\TownOwner;

use App\Models\Employee;
use App\Models\EmployeeSalary;
use App\Models\Town;
use Livewire\Component;

class ManageEmployees extends Component
{
    public $town_id;
    public $town;

    // Add Employee Form
    public $name = '';
    public $phone = '';
    public $designation = '';
    public $monthly_salary = '';

    // Payment Modal / Form
    public $selected_employee_id;
    public $payment_type = 'salary'; // 'salary' or 'advance'
    public $amount = '';
    public $salary_month = '';
    public $payment_date = '';
    public $remarks = '';

    public function mount($townId = null)
    {
        if (!$townId) {
            $this->town = Town::first();
            $this->town_id = $this->town?->id;
        } else {
            $this->town_id = $townId;
            $this->town = Town::findOrFail($townId);
        }

        $this->expense_date = date('Y-m-d');
    }

    public function addEmployee()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'monthly_salary' => 'required|numeric|min:0',
        ]);

        Employee::create([
            'town_id' => $this->town_id,
            'name' => $this->name,
            'phone' => $this->phone,
            'designation' => $this->designation,
            'monthly_salary' => $this->monthly_salary,
            'status' => 'active',
        ]);

        $this->reset(['name', 'phone', 'designation', 'monthly_salary']);
        session()->flash('success', 'ایمپلائی کامیابی سے رجسٹر ہو گیا ہے۔');
    }

    public function recordPayment()
    {
        $this->validate([
            'selected_employee_id' => 'required|exists:employees,id',
            'payment_type' => 'required|in:salary,advance',
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
        ]);

        EmployeeSalary::create([
            'employee_id' => $this->selected_employee_id,
            'type' => $this->payment_type,
            'amount' => $this->amount,
            'salary_month' => $this->payment_type === 'salary' ? $this->salary_month : null,
            'payment_date' => $this->payment_date,
            'remarks' => $this->remarks,
        ]);

        $this->reset(['selected_employee_id', 'amount', 'remarks']);
        session()->flash('success', 'پیمنٹ/ایڈوانس کی انٹری کامیابی سے درج ہو گئی ہے۔');
    }

    public function render()
    {
        $employees = Employee::where('town_id', $this->town_id)->with('salaryRecords')->get();
        $recentPayments = EmployeeSalary::whereHas('employee', fn($q) => $q->where('town_id', $this->town_id))
            ->with('employee')
            ->latest()
            ->take(10)
            ->get();

        return view('livewire.town-owner.manage-employees', [
            'employees' => $employees,
            'recentPayments' => $recentPayments,
        ])->layout('layouts.app');
    }
}