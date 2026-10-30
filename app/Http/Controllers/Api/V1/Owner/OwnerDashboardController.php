<?php

namespace App\Http\Controllers\Api\V1\Owner;

use App\Http\Controllers\Api\V1\Transform;
use App\Models\EmployeeSalary;
use App\Models\Installment;
use App\Models\TownExpense;
use App\Services\BookingService;
use App\Support\Tenant;
use Illuminate\Http\Request;

class OwnerDashboardController extends OwnerController
{
    // What the app should show in its menu for this user
    public function permissions(Request $request)
    {
        return $this->ok([
            'is_owner' => Tenant::isOwner(),
            'sections' => collect(array_keys(Tenant::SECTIONS))->filter(fn ($s) => Tenant::can($s))->values(),
            'all_sections' => Tenant::SECTIONS,
        ]);
    }

    public function show(Request $request)
    {
        $today = today();
        $todayStr = $today->toDateString();
        $monthStart = $today->copy()->startOfMonth()->toDateString();
        $monthEnd = $today->copy()->endOfMonth()->toDateString();
        $weekEnd = $today->copy()->addDays(7)->toDateString();

        $townIds = $this->ownerTowns()->pluck('id');
        $plotIds = $this->ownerPlots()->select('id');
        $unpaid = BookingService::UNPAID;

        $plots = $this->ownerPlots()->selectRaw("COUNT(*) AS total,
            COALESCE(SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END), 0) AS available,
            COALESCE(SUM(CASE WHEN status = 'booked' THEN 1 ELSE 0 END), 0) AS booked,
            COALESCE(SUM(CASE WHEN status = 'sold' THEN 1 ELSE 0 END), 0) AS sold")->first();

        $inst = Installment::whereIn('plot_id', $plotIds)->toBase()->selectRaw("
            COALESCE(SUM(CASE WHEN status = 'paid' AND paid_date BETWEEN ? AND ? THEN amount + COALESCE(late_fee, 0) ELSE 0 END), 0) AS collected_month,
            COALESCE(SUM(CASE WHEN status = 'paid' AND paid_date = ? THEN amount + COALESCE(late_fee, 0) ELSE 0 END), 0) AS collected_today,
            COALESCE(SUM(CASE WHEN status IN ('pending','overdue') AND due_date BETWEEN ? AND ? THEN amount ELSE 0 END), 0) AS due_month,
            COALESCE(SUM(CASE WHEN status IN ('pending','overdue') AND due_date = ? THEN amount ELSE 0 END), 0) AS due_today,
            COALESCE(SUM(CASE WHEN status IN ('pending','overdue') AND due_date < ? THEN amount ELSE 0 END), 0) AS overdue_amount,
            COALESCE(SUM(CASE WHEN status IN ('pending','overdue') AND due_date < ? THEN 1 ELSE 0 END), 0) AS overdue_count,
            COALESCE(SUM(CASE WHEN status IN ('pending','overdue') AND due_date > ? AND due_date <= ? THEN amount ELSE 0 END), 0) AS upcoming_week,
            COALESCE(SUM(CASE WHEN status IN ('pending','overdue') THEN amount ELSE 0 END), 0) AS receivable
        ", [$monthStart, $monthEnd, $todayStr, $monthStart, $monthEnd, $todayStr, $todayStr, $todayStr, $todayStr, $weekEnd])->first();

        $expensesMonth = (float) TownExpense::whereIn('town_id', $townIds)->whereBetween('expense_date', [$monthStart, $monthEnd])->sum('amount');
        $salariesMonth = (float) EmployeeSalary::whereHas('employee', fn ($q) => $q->whereIn('town_id', $townIds))->whereBetween('payment_date', [$monthStart, $monthEnd])->sum('amount');

        return $this->ok([
            'towns_count' => $townIds->count(),
            'plots' => [
                'total' => (int) $plots->total,
                'available' => (int) $plots->available,
                'booked' => (int) $plots->booked,
                'sold' => (int) $plots->sold,
            ],
            'today' => [
                'collected' => (float) $inst->collected_today,
                'due' => (float) $inst->due_today,
            ],
            'month' => [
                'collected' => (float) $inst->collected_month,
                'pending_due' => (float) $inst->due_month,
                'expenses' => $expensesMonth,
                'salaries' => $salariesMonth,
                'net' => (float) $inst->collected_month - $expensesMonth - $salariesMonth,
            ],
            'receivable' => (float) $inst->receivable,
            'overdue' => ['amount' => (float) $inst->overdue_amount, 'installments' => (int) $inst->overdue_count],
            'upcoming_7_days' => (float) $inst->upcoming_week,
            'recent_payments' => Installment::with(['plot:id,plot_number,town_id', 'receivedBy:id,name'])
                ->whereIn('plot_id', $plotIds)->whereIn('status', ['paid'])->whereNotNull('paid_date')
                ->latest('paid_date')->latest('id')->limit(8)->get()
                ->map(fn ($i) => Transform::installment($i) + ['plot_number' => $i->plot?->plot_number]),
        ]);
    }
}
