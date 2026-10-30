<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;

/**
 * Town owner "account". A team member (staff) works inside the owner's account:
 * all data is scoped by ownerId(), and what they can open is limited by permissions.
 */
class Tenant
{
    // Sections a team member can be given
    const SECTIONS = [
        'dashboard' => 'Dashboard & Profit',
        'towns' => 'Schemes (add/edit)',
        'plots' => 'Plots & Booking',
        'installments' => 'Installments & Receipts',
        'recovery' => 'Recovery Desk',
        'customers' => 'Customers & Documents',
        'employees' => 'Employees & Salary',
        'expenses' => 'Expenses',
        'leads' => 'Leads',
    ];

    const PRESETS = [
        'recovery_officer' => ['label' => 'Recovery Officer', 'permissions' => ['recovery', 'installments', 'customers']],
        'sales_officer' => ['label' => 'Sales Officer', 'permissions' => ['plots', 'installments', 'customers', 'leads']],
        'accountant' => ['label' => 'Accountant', 'permissions' => ['dashboard', 'installments', 'recovery', 'customers', 'employees', 'expenses']],
        'manager' => ['label' => 'Manager (sab kuch)', 'permissions' => ['dashboard', 'towns', 'plots', 'installments', 'recovery', 'customers', 'employees', 'expenses', 'leads']],
    ];

    // Route name => section
    const ROUTE_SECTIONS = [
        'town_owner.dashboard' => 'dashboard',
        'town_owner.towns' => 'towns',
        'town_owner.plots' => 'plots',
        'town_owner.installments' => 'installments',
        'town_owner.receipt.print' => 'installments',
        'town_owner.allotment.letter' => 'installments',
        'town_owner.recovery' => 'recovery',
        'town_owner.customers' => 'customers',
        'town_owner.customer.document' => 'customers',
        'town_owner.employees' => 'employees',
        'town_owner.expenses' => 'expenses',
        'town_owner.leads' => 'leads',
        'town_owner.team' => 'team', // owner only
    ];

    public static function ownerId(): ?int
    {
        $user = Auth::user();

        return $user ? (int) ($user->owner_id ?: $user->id) : null;
    }

    public static function isOwner(): bool
    {
        $user = Auth::user();

        return $user && $user->role === 'town_owner' && ! $user->owner_id;
    }

    public static function can(string $section): bool
    {
        $user = Auth::user();

        if (! $user || $user->role !== 'town_owner') {
            return false;
        }
        if (! $user->owner_id) {
            return true; // the owner can do everything
        }
        if ($section === 'team') {
            return false;
        }

        return in_array($section, (array) $user->permissions, true);
    }

    // First page a team member is allowed to open (used after login / when a page is not allowed)
    public static function homeRoute(): ?string
    {
        foreach (['dashboard', 'recovery', 'installments', 'customers', 'expenses', 'employees', 'leads', 'towns'] as $section) {
            if (self::can($section)) {
                return array_search($section, self::ROUTE_SECTIONS, true) ?: null;
            }
        }

        return null;
    }
}
