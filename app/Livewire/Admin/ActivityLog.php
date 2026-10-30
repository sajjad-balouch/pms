<?php

namespace App\Livewire\Admin;

use App\Models\AdminLog;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Who did what in the admin panel: top-up approvals, wallet adjustments, blocks, deletions, settings.
 */
class ActivityLog extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public $search = '';

    #[Url(except: '')]
    public $action = '';

    public $expandedId = null;

    public function updating($property)
    {
        if (in_array($property, ['search', 'action'], true)) {
            $this->resetPage();
        }
    }

    public function toggle($id)
    {
        $this->expandedId = $this->expandedId == $id ? null : (int) $id;
    }

    public function render()
    {
        $search = trim($this->search);

        $logs = AdminLog::with('admin:id,name,email')
            ->when($this->action !== '', fn ($q) => $q->where('action', 'like', $this->action . '%'))
            ->when($search !== '', fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('summary', 'like', "%{$search}%")
                    ->orWhere('target_id', $search)
                    ->orWhereHas('admin', fn ($a) => $a->where('name', 'like', "%{$search}%"));
            }))
            ->latest('id')
            ->paginate(25);

        return view('livewire.admin.activity-log', [
            'logs' => $logs,
            'actions' => [
                'topup' => __('Top-ups'),
                'wallet' => __('Wallet adjustments'),
                'user' => __('Users'),
                'provider' => __('Approvals'),
                'settings' => __('Settings'),
            ],
        ])->layout('layouts.admin-app');
    }
}
