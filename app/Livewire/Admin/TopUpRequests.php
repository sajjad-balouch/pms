<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AdminLog;
use App\Models\WalletTransaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TopUpRequests extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'pending';
    public $selectedTransaction = null;
    public $rejectionReason = '';
    public $showRejectModal = false;


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
        $approved = DB::transaction(function () use ($transactionId) {
            $transaction = WalletTransaction::where('id', $transactionId)
                ->where('type', 'deposit')
                ->where('status', 'pending')
                ->lockForUpdate()
                ->first();

            if (! $transaction) {
                return null;
            }

            $transaction->update([
                'status' => 'approved',
                'approved_at' => now(),
                'processed_by' => Auth::id(),
            ]);

            User::whereKey($transaction->user_id)->lockForUpdate()->firstOrFail()->increment('wallet_balance', $transaction->amount);

            AdminLog::record('topup.approved', $transaction, 'Approved top-up of Rs. ' . number_format($transaction->amount) . ' for user #' . $transaction->user_id, [
                'amount' => (float) $transaction->amount,
                'user_id' => $transaction->user_id,
            ]);

            return $transaction;
        });

        // Pehle "approved" ka message hamesha aata tha, chahe request pehle hi process ho chuki ho
        $approved
            ? session()->flash('success', __('Top-up request approved and wallet balance updated!'))
            : session()->flash('error', __('Transaction not found or already processed.'));
    }

    // Open Reject Modal
    public function openRejectModal($transactionId)
    {
        $this->selectedTransaction = WalletTransaction::where('type', 'deposit')->where('status', 'pending')->findOrFail($transactionId);
        $this->rejectionReason = '';
        $this->showRejectModal = true;
    }

    // Confirm Reject Request
    public function reject()
    {
        $this->validate([
            'rejectionReason' => 'required|string|min:5|max:255',
        ]);

        // Re-read with a lock: an approve in another tab must not be overwritten by a stale reject
        $rejected = DB::transaction(function () {
            $transaction = WalletTransaction::whereKey($this->selectedTransaction?->id)
                ->where('type', 'deposit')
                ->where('status', 'pending')
                ->lockForUpdate()
                ->first();

            if (! $transaction) {
                return null;
            }

            $transaction->update([
                'status' => 'rejected',
                'admin_note' => $this->rejectionReason,
                'rejected_at' => now(),
                'processed_by' => Auth::id(),
            ]);

            AdminLog::record('topup.rejected', $transaction, 'Rejected top-up of Rs. ' . number_format($transaction->amount) . ': ' . $this->rejectionReason, [
                'amount' => (float) $transaction->amount,
                'user_id' => $transaction->user_id,
            ]);

            return $transaction;
        });

        $rejected
            ? session()->flash('success', __('Top-up request rejected successfully.'))
            : session()->flash('error', __('Transaction not found or already processed.'));

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