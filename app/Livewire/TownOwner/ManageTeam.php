<?php

namespace App\Livewire\TownOwner;

use App\Models\Installment;
use App\Models\PlotFollowup;
use App\Models\User;
use App\Support\Tenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;

/**
 * Town owner's team: recovery officers, accountants, sales staff.
 * Each member logs in with their own email/password and only sees the sections given here.
 */
class ManageTeam extends Component
{
    public $editingId = null; // null = form closed, 'new' = adding
    public $name = '', $email = '', $phone = '', $password = '';
    public $permissions = [];

    protected $messages = [
        'email.unique' => 'Ye email pehle se kisi account par hai.',
        'permissions.required' => 'Kam az kam ek section ki ijazat dein.',
        'password.min' => 'Password kam az kam 8 characters ka ho.',
    ];

    public function mount()
    {
        abort_unless(Tenant::isOwner(), 403, 'Sirf owner apni team manage kar sakta hai.');
    }

    public function newMember()
    {
        $this->resetValidation();
        $this->reset(['name', 'email', 'phone', 'password']);
        $this->permissions = Tenant::PRESETS['recovery_officer']['permissions'];
        $this->editingId = 'new';
    }

    public function editMember($id)
    {
        $member = $this->ownedMember($id);

        $this->resetValidation();
        $this->editingId = $member->id;
        $this->name = $member->name;
        $this->email = $member->email;
        $this->phone = $member->phone;
        $this->password = '';
        $this->permissions = (array) $member->permissions;
    }

    public function applyPreset($key)
    {
        if (isset(Tenant::PRESETS[$key])) {
            $this->permissions = Tenant::PRESETS[$key]['permissions'];
        }
    }

    public function closeForm()
    {
        $this->reset(['editingId', 'name', 'email', 'phone', 'password', 'permissions']);
        $this->resetValidation();
    }

    public function saveMember()
    {
        abort_unless(Tenant::isOwner(), 403);
        $isNew = $this->editingId === 'new';
        $member = $isNew ? null : $this->ownedMember($this->editingId);

        $this->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($member?->id)],
            'phone' => ['nullable', 'regex:/^[0-9+\-\s]{10,15}$/'],
            'password' => [$isNew ? 'required' : 'nullable', 'string', 'min:8', 'max:100'],
            'permissions' => 'required|array|min:1',
            'permissions.*' => Rule::in(array_keys(Tenant::SECTIONS)),
        ]);

        $data = [
            'name' => trim($this->name),
            'email' => strtolower(trim($this->email)),
            'phone' => $this->phone ? trim($this->phone) : null,
            'permissions' => array_values(array_intersect(array_keys(Tenant::SECTIONS), $this->permissions)),
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($isNew) {
            $owner = Auth::user();
            User::create($data + [
                'role' => 'town_owner',
                'owner_id' => $owner->id,
                'is_approved' => true,
                'trial_ends_at' => $owner->trial_ends_at,
            ]);
            session()->flash('success', $data['name'] . ' team mein shamil ho gaya. Ab wo ' . $data['email'] . ' se login kar sakta hai.');
        } else {
            $member->update($data);
            session()->flash('success', $data['name'] . ' ki tafseel aur ijazatein update ho gayin.');
        }

        $this->closeForm();
    }

    public function toggleActive($id)
    {
        $member = $this->ownedMember($id);
        $member->update(['is_approved' => ! $member->is_approved]);

        session()->flash('success', $member->name . ($member->is_approved ? ' dobara active.' : ' ka login band kar diya gaya.'));
    }

    public function removeMember($id)
    {
        $member = $this->ownedMember($id);
        $name = $member->name;

        // Their past work stays (payments / follow-ups just lose the name link)
        $member->delete();

        if ($this->editingId == $id) {
            $this->closeForm();
        }

        session()->flash('success', $name . ' team se nikal diya gaya.');
    }

    public function render()
    {
        $members = User::where('owner_id', Auth::id())->orderBy('name')->get();
        $monthStart = now()->startOfMonth()->toDateString();

        // This month's work per member
        $collected = Installment::whereIn('received_by', $members->pluck('id'))
            ->where('status', 'paid')
            ->where('paid_date', '>=', $monthStart)
            ->selectRaw('received_by, COUNT(*) AS entries, COALESCE(SUM(amount + late_fee), 0) AS total')
            ->groupBy('received_by')
            ->get()
            ->keyBy('received_by');

        $followups = PlotFollowup::whereIn('user_id', $members->pluck('id'))
            ->where('created_at', '>=', $monthStart)
            ->selectRaw('user_id, COUNT(*) AS total')
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        return view('livewire.town-owner.manage-team', [
            'members' => $members,
            'collected' => $collected,
            'followups' => $followups,
            'sections' => Tenant::SECTIONS,
            'presets' => Tenant::PRESETS,
        ])->layout('layouts.app');
    }

    protected function ownedMember($id): User
    {
        return User::where('owner_id', Auth::id())->findOrFail($id);
    }
}
