<?php

namespace App\Livewire\TownOwner;

use App\Models\EmployeeSalary;
use App\Models\Installment;
use App\Models\Plot;
use App\Models\Town;
use App\Models\TownExpense;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TownOwnerDashboard extends Component
{
    public function render()
    {
        $townIds = Town::where('user_id', Auth::id())->pluck('id');
        $plotIds = Plot::whereIn('town_id', $townIds)->pluck('id');

        // Inventory Metrics
        $totalPlots = Plot::whereIn('town_id', $townIds)->count();
        $availablePlots = Plot::whereIn('town_id', $townIds)->where('status', 'available')->count();
        $bookedPlots = Plot::whereIn('town_id', $townIds)->whereIn('status', ['booked', 'sold'])->count();

        // Financial Metrics - Income
        $totalCollected = Installment::whereIn('plot_id', $plotIds)->where('status', 'paid')->sum('amount');
        $dueThisMonth = Installment::whereIn('plot_id', $plotIds)
            ->where('status', 'pending')
            ->whereMonth('due_date', Carbon::now()->month)
            ->whereYear('due_date', Carbon::now()->year)
            ->sum('amount');

        $overdueInstallments = Installment::whereIn('plot_id', $plotIds)
            ->where('status', 'pending')
            ->where('due_date', '<', Carbon::now()->format('Y-m-d'))
            ->get();

        // Financial Metrics - Expenses & Salaries
        $totalExpenses = TownExpense::whereIn('town_id', $townIds)->sum('amount');
        
        $totalSalariesPaid = EmployeeSalary::whereHas('employee', function ($q) use ($townIds) {
            $q->whereIn('town_id', $townIds);
        })->sum('amount');

        $totalOutflow = $totalExpenses + $totalSalariesPaid;

        // Net Profit Calculation
        $netProfit = $totalCollected - $totalOutflow;

        return view('livewire.town-owner.town-owner-dashboard', [
            'towns' => Town::where('user_id', Auth::id())->withCount('plots')->get(),
            'totalPlots' => $totalPlots,
            'availablePlots' => $availablePlots,
            'bookedPlots' => $bookedPlots,
            'totalCollected' => $totalCollected,
            'dueThisMonth' => $dueThisMonth,
            'totalExpenses' => $totalExpenses,
            'totalSalariesPaid' => $totalSalariesPaid,
            'totalOutflow' => $totalOutflow,
            'netProfit' => $netProfit,
            'overdueInstallments' => $overdueInstallments,
        ])->layout('layouts.app');
    }
}