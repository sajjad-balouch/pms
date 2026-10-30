<?php

namespace App\Livewire\Admin;

use App\Models\AdminLog;
use App\Models\Installment;
use App\Models\Plot;
use App\Models\PlotBooking;
use App\Models\Property;
use App\Models\SystemSetting;
use App\Models\Town;
use App\Models\User;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AdminDashboard extends Component
{
    public $lead_unlock_fee_agent;
    public $lead_unlock_fee_town;
    public $town_owner_monthly_fee;
    public $agent_monthly_fee;
    public $require_provider_approval = false;

    public function mount()
    {
        $settings = SystemSetting::getSettings();
        $this->lead_unlock_fee_agent = $settings->lead_unlock_fee_agent;
        $this->lead_unlock_fee_town = $settings->lead_unlock_fee_town;
        $this->town_owner_monthly_fee = $settings->town_owner_monthly_fee;
        $this->agent_monthly_fee = $settings->agent_monthly_fee;
        $this->require_provider_approval = (bool) $settings->require_provider_approval;
    }

    // Toggle Approval Status for Agents or Town Owners
    public function toggleApproval($userId)
    {
        $user = User::whereIn('role', ['town_owner', 'agent'])->findOrFail($userId);
        $user->is_approved = ! $user->is_approved;
        $user->save();

        AdminLog::record($user->is_approved ? 'provider.approved' : 'provider.suspended', $user, ($user->is_approved ? 'Approved ' : 'Suspended ') . $user->role . ' ' . $user->email);
        session()->flash('status', __('User :name approval status updated.', ['name' => $user->name]));
    }

    // Save Fee & Subscription Settings
    public function updateSettings()
    {
        $this->validate([
            'lead_unlock_fee_agent' => 'required|numeric|min:0|max:100000',
            'lead_unlock_fee_town' => 'required|numeric|min:0|max:100000',
            'town_owner_monthly_fee' => 'required|numeric|min:0|max:10000000',
            'agent_monthly_fee' => 'required|numeric|min:0|max:10000000',
            'require_provider_approval' => 'boolean',
        ]);

        $settings = SystemSetting::getSettings();
        $before = $settings->only(['lead_unlock_fee_agent', 'lead_unlock_fee_town', 'town_owner_monthly_fee', 'agent_monthly_fee', 'require_provider_approval']);

        $settings->update([
            'lead_unlock_fee_agent' => $this->lead_unlock_fee_agent,
            'lead_unlock_fee_town' => $this->lead_unlock_fee_town,
            'town_owner_monthly_fee' => $this->town_owner_monthly_fee,
            'agent_monthly_fee' => $this->agent_monthly_fee,
            'require_provider_approval' => (bool) $this->require_provider_approval,
        ]);

        AdminLog::record('settings.updated', $settings, 'Updated platform fees / approval settings', [
            'before' => $before,
            'after' => $settings->fresh()->only(array_keys($before)),
        ]);

        session()->flash('settings_status', __('System fee settings updated successfully!'));
    }

    public function render()
    {
        $now = Carbon::now();
        $monthStart = $now->copy()->startOfMonth();
        $last30 = $now->copy()->subDays(30);

        // Money
        $revenue = WalletTransaction::where('type', 'unlock_fee')
            ->selectRaw('COALESCE(SUM(amount), 0) AS all_time, COALESCE(SUM(CASE WHEN created_at >= ? THEN amount ELSE 0 END), 0) AS month, COUNT(*) AS unlocks', [$monthStart])
            ->first();
        $deposits = WalletTransaction::where('type', 'deposit')
            ->selectRaw("COALESCE(SUM(CASE WHEN status = 'approved' AND approved_at >= ? THEN amount ELSE 0 END), 0) AS approved_month,
                         COALESCE(SUM(CASE WHEN status = 'pending' THEN amount ELSE 0 END), 0) AS pending_amount,
                         COALESCE(SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END), 0) AS pending_count", [$monthStart])
            ->first();
        $walletFloat = (float) User::sum('wallet_balance');

        // Platform activity
        $platform = [
            'towns' => Town::count(),
            'plots' => Plot::count(),
            'plots_sold' => Plot::whereIn('status', ['booked', 'sold'])->count(),
            'active_bookings' => PlotBooking::where('status', 'active')->count(),
            'installments_month' => (float) Installment::where('status', 'paid')->where('paid_date', '>=', $monthStart->toDateString())->sum('amount'),
            'listings' => Property::where('status', 'available')->count(),
            'leads_30' => DB::table('leads')->where('created_at', '>=', $last30)->count(),
        ];

        // Growth: signups per day (last 14 days)
        $signupRows = User::where('created_at', '>=', $now->copy()->subDays(13)->startOfDay())
            ->selectRaw('DATE(created_at) AS d, COUNT(*) AS n')
            ->groupBy('d')
            ->pluck('n', 'd');
        $signups = collect(range(13, 0))->map(fn ($ago) => [
            'label' => $now->copy()->subDays($ago)->format('d M'),
            'count' => (int) ($signupRows[$now->copy()->subDays($ago)->toDateString()] ?? 0),
        ])->all();

        $topAgents = User::where('role', 'agent')
            ->select('users.id', 'users.name', 'users.email')
            ->selectSub(
                DB::table('property_unlocks')->join('properties', 'properties.id', '=', 'property_unlocks.property_id')
                    ->whereColumn('properties.agent_id', 'users.id')
                    ->where('property_unlocks.created_at', '>=', $monthStart)
                    ->selectRaw('COUNT(*)'),
                'unlocks_month'
            )
            ->orderByDesc('unlocks_month')
            ->limit(5)
            ->get()
            ->filter(fn ($a) => $a->unlocks_month > 0);

        return view('livewire.admin.admin-dashboard', [
            'totalUsers' => User::where('role', 'user')->count(),
            'totalTownOwners' => User::where('role', 'town_owner')->whereNull('owner_id')->count(),
            'totalAgents' => User::where('role', 'agent')->count(),
            'newUsers30' => User::where('created_at', '>=', $last30)->count(),
            'pendingApprovals' => User::whereIn('role', ['town_owner', 'agent'])->where('is_approved', false)->get(),
            'allProviders' => User::whereIn('role', ['town_owner', 'agent'])->whereNull('owner_id')->latest()->paginate(10),
            'revenue' => $revenue,
            'deposits' => $deposits,
            'walletFloat' => $walletFloat,
            'platform' => $platform,
            'signups' => $signups,
            'signupMax' => max(1, collect($signups)->max('count')),
            'topAgents' => $topAgents,
            'recentLogs' => AdminLog::with('admin:id,name')->latest('id')->limit(8)->get(),
        ])->layout('layouts.admin-app');
    }
}
