<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class PendingApprovals extends Component
{
    use WithPagination;

    public function approveUser($userId)
    {
        $user = User::findOrFail($userId);
        $user->update([
            'is_approved' => true,
        ]);

        session()->flash('success', "{$user->name} کا اکاؤنٹ کامیا بی سے منظور (Approve) کر دیا گیا ہے۔");
    }

    public function rejectUser($userId)
    {
        $user = User::findOrFail($userId);
        // Approve نفی کر کے ڈس ایبل یا ڈیلیٹ کر سکتے ہیں
        $user->delete();

        session()->flash('error', "درخواست کو رد کر دیا گیا ہے۔");
    }

    public function render()
    {
        $pendingUsers = User::whereIn('role', ['town_owner', 'agent'])
            ->where('is_approved', false)
            ->latest()
            ->paginate(10);

        return view('livewire.admin.pending-approvals', [
            'pendingUsers' => $pendingUsers,
        ]);
    }
}
