<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\PaymentMethod;

class ManagePaymentMethods extends Component
{
    public $showModal = false;
    public $isEditMode = false;
    public $methodIdBeingEdited = null;

    // Form fields
    public $method_name = '';
    public $account_title = '';
    public $account_number = '';
    public $instructions = '';
    public $is_active = true;

    protected function rules()
    {
        return [
            'method_name' => 'required|string|max:255',
            'account_title' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'instructions' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ];
    }

    public function openCreateModal()
    {
        $this->resetErrorBag();
        $this->reset(['method_name', 'account_title', 'account_number', 'instructions', 'methodIdBeingEdited']);
        $this->is_active = true;
        $this->isEditMode = false;
        $this->showModal = true;
    }

    public function openEditModal($methodId)
    {
        $this->resetErrorBag();
        $method = PaymentMethod::findOrFail($methodId);

        $this->methodIdBeingEdited = $method->id;
        $this->method_name = $method->method_name;
        $this->account_title = $method->account_title;
        $this->account_number = $method->account_number;
        $this->instructions = $method->instructions;
        $this->is_active = (bool) $method->is_active;

        $this->isEditMode = true;
        $this->showModal = true;
    }

    public function toggleStatus($methodId)
    {
        $method = PaymentMethod::findOrFail($methodId);
        $method->update([
            'is_active' => !$method->is_active,
        ]);

        session()->flash('success', 'Payment method status updated successfully!');
    }

    public function saveMethod()
    {
        $this->validate();

        if ($this->isEditMode) {
            $method = PaymentMethod::findOrFail($this->methodIdBeingEdited);
            $method->update([
                'method_name' => $this->method_name,
                'account_title' => $this->account_title,
                'account_number' => $this->account_number,
                'instructions' => $this->instructions,
                'is_active' => $this->is_active,
            ]);

            session()->flash('success', 'Payment method updated successfully!');
        } else {
            PaymentMethod::create([
                'method_name' => $this->method_name,
                'account_title' => $this->account_title,
                'account_number' => $this->account_number,
                'instructions' => $this->instructions,
                'is_active' => $this->is_active,
            ]);

            session()->flash('success', 'New payment method added successfully!');
        }

        $this->showModal = false;
        $this->reset(['method_name', 'account_title', 'account_number', 'instructions', 'methodIdBeingEdited']);
    }

    public function deleteMethod($methodId)
    {
        $method = PaymentMethod::findOrFail($methodId);
        $method->delete();

        session()->flash('success', 'Payment method deleted successfully.');
    }

    public function render()
    {
        $methods = PaymentMethod::latest()->get();

        return view('livewire.admin.manage-payment-methods', [
            'methods' => $methods,
        ])->layout('layouts.admin-app');
    }
}