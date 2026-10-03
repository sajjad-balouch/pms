<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\WalletTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TopUpRequests extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'pending';
    public $selectedTransaction = null;
    public $rejectionReason = '';
    public $showRejectModal = false;

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    // Approve Top-Up Request
    public function approve($transactionId)
    {
        DB::transaction(function () use ($transactionId) {
            $transaction = WalletTransaction::where('id', $transactionId)
                ->where('status', 'pending')
                ->lockForUpdate()
                ->first();

            if (!$transaction) {
                session()->flash('error', 'Transaction not found or already processed.');
                return;
            }

            // Update Transaction Status
            $transaction->update([
                'status' => 'approved',
                'approved_at' => now(),
            ]);

            // Increment User Wallet Balance
            $user = User::findOrFail($transaction->user_id);
            $user->increment('wallet_balance', $transaction->amount);
        });

        session()->flash('success', 'Top-up request approved and wallet balance updated!');
    }

    // Open Reject Modal
    public function openRejectModal($transactionId)
    {
        $this->selectedTransaction = WalletTransaction::findOrFail($transactionId);
        $this->rejectionReason = '';
        $this->showRejectModal = true;
    }

    // Confirm Reject Request
    public function reject()
    {
        $this->validate([
            'rejectionReason' => 'required|string|min:5|max:255',
        ]);

        if ($this->selectedTransaction && $this->selectedTransaction->status === 'pending') {
            $this->selectedTransaction->update([
                'status' => 'rejected',
                'admin_note' => $this->rejectionReason,
                'rejected_at' => now(),
            ]);

            session()->flash('success', 'Top-up request rejected successfully.');
        }

        $this->showRejectModal = false;
        $this->reset(['selectedTransaction', 'rejectionReason']);
    }

    public function render()
    {
        $query = WalletTransaction::with('user')
            ->where('type', 'deposit')
            ->latest();

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('sender_account_title', 'like', '%' . $this->search . '%')
                  ->orWhere('sender_account_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('user', function ($u) {
                      $u->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        $transactions = $query->paginate(10);

        return view('livewire.admin.top-up-requests', [
            'transactions' => $transactions,
        ])->layout('layouts.admin-app');
    }
}