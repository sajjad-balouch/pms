<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\WalletTransaction;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ManageDeposits extends Component
{
    // New Method Fields
    public $method_name = '';
    public $account_title = '';
    public $account_number = '';
    public $instructions = '';

    public function createPaymentMethod()
    {
        $this->validate([
            'method_name' => 'required|string|max:255',
            'account_title' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
        ]);

        PaymentMethod::create([
            'method_name' => $this->method_name,
            'account_title' => $this->account_title,
            'account_number' => $this->account_number,
            'instructions' => $this->instructions,
        ]);

        $this->reset(['method_name', 'account_title', 'account_number', 'instructions']);
        session()->flash('success', 'Payment Method added successfully!');
    }

    public function toggleMethodStatus($id)
    {
        $method = PaymentMethod::findOrFail($id);
        $method->is_active = !$method->is_active;
        $method->save();
    }

    public function approveDeposit($transactionId)
    {
        DB::transaction(function () use ($transactionId) {
            $tx = WalletTransaction::where('id', $transactionId)->where('status', 'pending')->firstOrFail();
            
            // Mark Approved
            $tx->status = 'approved';
            $tx->save();

            // Add balance to user
            $user = User::findOrFail($tx->user_id);
            $user->increment('wallet_balance', $tx->amount);
        });

        session()->flash('success', 'Deposit approved and wallet topped up!');
    }

    public function rejectDeposit($transactionId)
    {
        $tx = WalletTransaction::where('id', $transactionId)->where('status', 'pending')->firstOrFail();
        $tx->status = 'rejected';
        $tx->save();

        session()->flash('error', 'Deposit request rejected.');
    }

    public function render()
    {
        $methods = PaymentMethod::latest()->get();
        $pendingDeposits = WalletTransaction::with('user')
            ->where('type', 'deposit')
            ->latest()
            ->paginate(10);

        return view('livewire.admin.manage-deposits', [
            'methods' => $methods,
            'pendingDeposits' => $pendingDeposits,
        ])->layout('layouts.front-app');
    }
}