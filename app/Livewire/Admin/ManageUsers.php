<?php

namespace App\Livewire\Admin;

use App\Models\AdminLog;
use App\Models\User;
use App\Models\Property;
use App\Models\Town;
use App\Models\WalletTransaction;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ManageUsers extends Component
{
    use WithPagination;

    public $search = '';
    public $roleFilter = '';
    public $statusFilter = ''; // approved | blocked

    // Wallet adjustment modal
    public $showWalletModal = false;
    public $walletUserId = null;
    public $walletDirection = 'credit'; // credit | debit
    public $walletAmount = '';
    public $walletReason = '';

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

    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => '', 'as' => 'role'],
        'statusFilter' => ['except' => '', 'as' => 'status'],
    ];

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    // --- VIEW COMPLETE USER ACTIVITY ---
    public function viewUserActivity($userId)
    {
        $this->selectedUser = User::findOrFail($userId);

        // 1. Properties
        $this->userProperties = Property::where('agent_id', $userId)
            ->latest()
            ->get();

        // 2. Towns / Housing Schemes
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

        $created = User::create([
            'name'        => $this->name,
            'email'       => $this->email,
            'phone'       => $this->phone,
            'role'        => $this->role,
            'password'    => Hash::make($this->password),
            'is_approved' => true,
        ]);

        AdminLog::record('user.created', $created, 'Created ' . $created->role . ' account: ' . $created->email);
        session()->flash('status', __('New user added successfully!'));
        $this->showCreateModal = false;
        $this->reset(['name', 'email', 'phone', 'role', 'password']);
    }

    // --- TOGGLE BLOCK / UNBLOCK ---
    public function toggleBlock($userId)
    {
        $user = User::findOrFail($userId);
        
        if ($user->id === auth()->id()) {
            session()->flash('error', __('You cannot block your own account!'));
            return;
        }

        $user->is_approved = !$user->is_approved;
        $user->save();

        AdminLog::record($user->is_approved ? 'user.unblocked' : 'user.blocked', $user, ($user->is_approved ? 'Unblocked ' : 'Blocked ') . $user->email);
        $statusMessage = $user->is_approved ? __('User unblocked successfully.') : __('User has been blocked.');
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

        AdminLog::record('user.password_changed', $user, 'Changed password for ' . $user->email);
        session()->flash('status', __('User password changed successfully.'));
        $this->showPasswordModal = false;
        $this->reset(['selectedUserId', 'new_password']);
    }

    // --- DELETE USER SAFELY ---
    public function deleteUser($userId)
    {
        if ((int) $userId === (int) auth()->id()) {
            session()->flash('error', __('You cannot delete your own admin account!'));
            return;
        }

        $user = User::findOrFail($userId);

        // Financial / customer records must never disappear with a click — block the account instead
        $blockers = array_filter([
            (float) $user->wallet_balance > 0 ? __('wallet balance Rs. :amount', ['amount' => number_format($user->wallet_balance)]) : null,
            DB::table('wallet_transactions')->where('user_id', $user->id)->where('status', 'approved')->where('type', 'deposit')->exists() ? __('approved top-up history') : null,
            Town::where('user_id', $user->id)->whereHas('plots', fn ($q) => $q->whereIn('status', ['booked', 'sold']))->exists() ? __('housing schemes with booked/sold plots') : null,
            DB::table('customers')->where('user_id', $user->id)->exists() ? __('customer records') : null,
            $user->role === 'admin' && User::where('role', 'admin')->count() <= 1 ? __('the last admin account') : null,
        ]);

        if ($blockers) {
            session()->flash('error', __('This account cannot be deleted because it has :items. Block it instead.', ['items' => implode(', ', $blockers)]));
            return;
        }

        AdminLog::record('user.deleted', $user, 'Deleted ' . $user->role . ' account: ' . $user->email, [
            'name' => $user->name, 'email' => $user->email, 'role' => $user->role,
        ]);

        try {
            DB::transaction(function () use ($user, $userId) {
                // 1. Leads delete/cleanup
                DB::table('leads')->where('agent_id', $userId)->delete();

                // 2. Property unlocks & wallet records
                DB::table('property_unlocks')->where('user_id', $userId)->delete();
                DB::table('wallet_transactions')->where('user_id', $userId)->delete();

                // 3. User ki Properties delete karein
                Property::where('agent_id', $userId)->delete();

                // 4. Towns aur unke plots delete karein
                $towns = Town::where('user_id', $userId)->get();
                foreach ($towns as $town) {
                    DB::table('plots')->where('town_id', $town->id)->delete();
                    $town->delete();
                }

                // 5. User account delete
                $user->delete();
            });

            session()->flash('status', __('User and all their data have been permanently deleted.'));
        } catch (\Exception $e) {
            report($e);
            session()->flash('error', __('Could not delete the user. Please try again.'));
        }
    }

    // --- WALLET ADJUSTMENT (manual credit / debit with a reason) ---
    public function openWalletModal($userId)
    {
        $this->walletUserId = User::findOrFail($userId)->id;
        $this->reset(['walletDirection', 'walletAmount', 'walletReason']);
        $this->resetValidation();
        $this->showWalletModal = true;
    }

    public function adjustWallet()
    {
        $this->validate([
            'walletDirection' => 'required|in:credit,debit',
            'walletAmount' => 'required|numeric|min:1|max:10000000',
            'walletReason' => 'required|string|min:5|max:255',
        ]);

        $amount = round((float) $this->walletAmount, 2);

        $result = DB::transaction(function () use ($amount) {
            $user = User::whereKey($this->walletUserId)->lockForUpdate()->firstOrFail();

            if ($this->walletDirection === 'debit' && (float) $user->wallet_balance < $amount) {
                return 'insufficient';
            }

            $this->walletDirection === 'credit'
                ? $user->increment('wallet_balance', $amount)
                : $user->decrement('wallet_balance', $amount);

            $txn = WalletTransaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => 'adjustment',
                'status' => 'approved',
                'description' => ($this->walletDirection === 'credit' ? 'Admin credit: ' : 'Admin debit: ') . $this->walletReason,
                'admin_note' => $this->walletDirection,
                'approved_at' => now(),
                'processed_by' => auth()->id(),
            ]);

            AdminLog::record('wallet.' . $this->walletDirection, $user, ucfirst($this->walletDirection) . ' Rs. ' . number_format($amount) . ' — ' . $this->walletReason, [
                'amount' => $amount,
                'balance_after' => (float) $user->fresh()->wallet_balance,
                'transaction_id' => $txn->id,
            ]);

            return 'ok';
        });

        if ($result === 'insufficient') {
            $this->addError('walletAmount', __('Debit is more than the wallet balance.'));
            return;
        }

        $this->showWalletModal = false;
        $this->reset(['walletUserId', 'walletAmount', 'walletReason']);
        session()->flash('status', __('Wallet updated successfully.'));
    }

    // --- EXPORT (current filters) ---
    public function exportCsv()
    {
        $query = $this->filteredQuery();
        AdminLog::record('users.exported', null, 'Exported users CSV', ['role' => $this->roleFilter, 'status' => $this->statusFilter, 'search' => $this->search]);

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['ID', 'Name', 'Email', 'Phone', 'Role', 'Approved', 'Wallet Balance', 'Joined']);
            $query->chunk(500, function ($users) use ($out) {
                foreach ($users as $u) {
                    fputcsv($out, [$u->id, $u->name, $u->email, $u->phone, $u->role, $u->is_approved ? 'Yes' : 'No', $u->wallet_balance, $u->created_at?->format('Y-m-d')]);
                }
            });
            fclose($out);
        }, 'users-' . now()->format('Y-m-d') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    protected function filteredQuery()
    {
        $search = trim($this->search);

        return User::query()
            ->when($search !== '', fn ($q) => $q->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            }))
            ->when(in_array($this->roleFilter, ['admin', 'user', 'town_owner', 'agent'], true), fn ($q) => $q->where('role', $this->roleFilter))
            ->when($this->statusFilter === 'approved', fn ($q) => $q->where('is_approved', true))
            ->when($this->statusFilter === 'blocked', fn ($q) => $q->where('is_approved', false))
            ->latest();
    }

    public function render()
    {
        return view('livewire.admin.manage-users', [
            'users' => $this->filteredQuery()->paginate(10),
            'roleCounts' => User::selectRaw('role, COUNT(*) AS n')->groupBy('role')->pluck('n', 'role'),
            'walletUser' => $this->walletUserId ? User::find($this->walletUserId) : null,
        ])->layout('layouts.admin-app');
    }
}