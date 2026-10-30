<?php

namespace App\Livewire\Admin;

use App\Models\AdminLog;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class PendingApprovals extends Component
{
    use WithPagination;

    public function approveUser($userId)
    {
        $user = $this->pendingQuery()->findOrFail($userId);
        $user->update(['is_approved' => true]);

        AdminLog::record('provider.approved', $user, 'Approved ' . $user->role . ' ' . $user->email);
        session()->flash('success', __(':name has been approved.', ['name' => $user->name]));
    }

    public function rejectUser($userId)
    {
        // Only empty sign-ups can be rejected (deleted). A blocked account that already has data
        // (schemes, listings, leads, wallet) must stay — it can only be unblocked or kept blocked.
        $user = $this->pendingQuery()->findOrFail($userId);

        $hasData = \App\Models\Town::where('user_id', $user->id)->exists()
            || \App\Models\Property::where('agent_id', $user->id)->exists()
            || \Illuminate\Support\Facades\DB::table('leads')->where('agent_id', $user->id)->exists()
            || \Illuminate\Support\Facades\DB::table('wallet_transactions')->where('user_id', $user->id)->exists();

        if ($hasData) {
            session()->flash('error', __('This account already has data, so it cannot be deleted. Keep it blocked or approve it.'));
            return;
        }

        AdminLog::record('provider.rejected', $user, 'Rejected sign-up ' . $user->role . ' ' . $user->email, [
            'name' => $user->name, 'email' => $user->email, 'role' => $user->role,
        ]);
        $user->delete();

        session()->flash('error', __('The request has been rejected.'));
    }

    protected function pendingQuery()
    {
        return User::whereIn('role', ['town_owner', 'agent'])->whereNull('owner_id')->where('is_approved', false);
    }

    public function render()
    {
        return view('livewire.admin.pending-approvals', [
            'pendingUsers' => $this->pendingQuery()->latest()->paginate(10),
        ]);
    }
}
