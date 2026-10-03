<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Property;
use App\Models\Town;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ManageUsers extends Component
{
    use WithPagination;

    public $search = '';

    // Create User Modal States & Fields
    public $showCreateModal = false;
    public $name = '';
    public $email = '';
    public $phone = '';
    public $role = 'user';
    public $password = '';

    // Change Password Modal States & Fields
    public $showPasswordModal = false;
    public $selectedUserId = null;
    public $new_password = '';

    // View User Activity Modal States & Data
    public $showActivityModal = false;
    public $selectedUser = null;
    public $userProperties = [];
    public $userTowns = [];
    public $userWalletTransactions = [];
    public $userUnlockedProperties = [];
    public $userLeads = [];

    // Selected Town Plots Detailed View
    public $selectedTownPlots = null;
    public $selectedTownName = '';
    public $showPlotsModal = false;

    protected $queryString = ['search' => ['except' => '']];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    // --- VIEW COMPLETE USER ACTIVITY ---
    public function viewUserActivity($userId)
    {
        $this->selectedUser = User::findOrFail($userId);

        // 1. Properties (agent_id migration key ke mutabiq)
        $this->userProperties = Property::where('agent_id', $userId)
            ->latest()
            ->get();

        // 2. Towns / Housing Schemes with complete Plots aggregation & details
        $this->userTowns = Town::where('user_id', $userId)
            ->with(['plots'])
            ->withCount([
                'plots as total_plots_count',
                'plots as available_plots_count' => function ($q) {
                    $q->where('status', 'available');
                },
                'plots as booked_plots_count' => function ($q) {
                    $q->where('status', 'booked');
                },
                'plots as sold_plots_count' => function ($q) {
                    $q->where('status', 'sold');
                },
                'plots as residential_plots_count' => function ($q) {
                    $q->where('type', 'residential');
                },
                'plots as commercial_plots_count' => function ($q) {
                    $q->where('type', 'commercial');
                },
            ])
            ->latest()
            ->get();

        // 3. Wallet Transactions
        $this->userWalletTransactions = DB::table('wallet_transactions')
            ->where('user_id', $userId)
            ->latest()
            ->take(15)
            ->get();

        // 4. Property Unlocks
        $this->userUnlockedProperties = DB::table('property_unlocks')
            ->join('properties', 'property_unlocks.property_id', '=', 'properties.id')
            ->where('property_unlocks.user_id', $userId)
            ->select('properties.title', 'properties.price', 'properties.city', 'property_unlocks.created_at')
            ->latest('property_unlocks.created_at')
            ->take(10)
            ->get();

        // 5. Leads Created / Unlocked
        $this->userLeads = DB::table('leads')
            ->where('agent_id', $userId)
            ->latest()
            ->take(10)
            ->get();

        $this->showActivityModal = true;
    }

    // --- VIEW DETAILED PLOTS FOR A SPECIFIC TOWN ---
    public function viewTownPlots($townId)
    {
        $town = Town::with('plots')->findOrFail($townId);
        $this->selectedTownName = $town->name;
        $this->selectedTownPlots = $town->plots;

        // Pehle activity modal ko close karein taake stacking/overlap issue na ho
        $this->showActivityModal = false;
        $this->showPlotsModal = true;
    }

    // --- CREATE NEW USER ---
    public function openCreateModal()
    {
        $this->reset(['name', 'email', 'phone', 'role', 'password']);
        $this->resetValidation();
        $this->showCreateModal = true;
    }

    public function createUser()
    {
        $this->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|string|max:20',
            'role'     => 'required|in:admin,user,town_owner,agent',
            'password' => 'required|min:8',
        ]);

        User::create([
            'name'        => $this->name,
            'email'       => $this->email,
            'phone'       => $this->phone,
            'role'        => $this->role,
            'password'    => Hash::make($this->password),
            'is_approved' => true,
        ]);

        session()->flash('status', 'Naya user kamyabi se add ho gaya hai!');
        $this->showCreateModal = false;
        $this->reset(['name', 'email', 'phone', 'role', 'password']);
    }

    // --- TOGGLE BLOCK / UNBLOCK ---
    public function toggleBlock($userId)
    {
        $user = User::findOrFail($userId);
        
        if ($user->id === auth()->id()) {
            session()->flash('error', 'Aap apne account ko block nahi kar sakte!');
            return;
        }

        $user->is_approved = !$user->is_approved;
        $user->save();

        $statusMessage = $user->is_approved ? 'User ko kamyabi se unblock kar diya gaya hai.' : 'User ko block kar diya gaya hai.';
        session()->flash('status', $statusMessage);
    }

    // --- CHANGE USER PASSWORD ---
    public function openPasswordModal($userId)
    {
        $this->selectedUserId = $userId;
        $this->new_password = '';
        $this->resetValidation();
        $this->showPasswordModal = true;
    }

    public function updatePassword()
    {
        $this->validate([
            'new_password' => 'required|min:8',
        ]);

        $user = User::findOrFail($this->selectedUserId);
        $user->password = Hash::make($this->new_password);
        $user->save();

        session()->flash('status', 'User ka password kamyabi se change ho gaya hai.');
        $this->showPasswordModal = false;
        $this->reset(['selectedUserId', 'new_password']);
    }

    public function render()
    {
        $users = User::where(function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('email', 'like', '%' . $this->search . '%')
                ->orWhere('phone', 'like', '%' . $this->search . '%');
        })
        ->latest()
        ->paginate(10);

        return view('livewire.admin.manage-users', [
            'users' => $users
        ])->layout('layouts.admin-app');
    }
}