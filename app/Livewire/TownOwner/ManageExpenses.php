<?php

namespace App\Livewire\TownOwner;

use App\Models\Town;
use App\Models\TownExpense;
use Livewire\Component;

class ManageExpenses extends Component
{
    public $town_id;
    public $town;

    public $title = '';
    public $category = 'Utilities';
    public $amount = '';
    public $expense_date = '';
    public $paid_to = '';
    public $details = '';

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

    public function addExpense()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'expense_date' => 'required|date',
        ]);

        TownExpense::create([
            'town_id' => $this->town_id,
            'title' => $this->title,
            'category' => $this->category,
            'amount' => $this->amount,
            'expense_date' => $this->expense_date,
            'paid_to' => $this->paid_to,
            'details' => $this->details,
        ]);

        $this->reset(['title', 'amount', 'paid_to', 'details']);
        session()->flash('success', 'اخراجات کی انٹری درج کر لی گئی ہے۔');
    }

    public function deleteExpense($id)
    {
        TownExpense::where('town_id', $this->town_id)->findOrFail($id)->delete();
        session()->flash('success', 'انٹری ڈیلیٹ کر دی گئی ہے۔');
    }

    public function render()
    {
        $expenses = TownExpense::where('town_id', $this->town_id)->latest()->get();
        $totalExpenseSum = $expenses->sum('amount');

        return view('livewire.town-owner.manage-expenses', [
            'expenses' => $expenses,
            'totalExpenseSum' => $totalExpenseSum,
        ])->layout('layouts.app');
    }
}