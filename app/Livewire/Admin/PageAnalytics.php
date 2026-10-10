<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PageView;
use Illuminate\Support\Facades\DB;

class PageAnalytics extends Component
{
    use WithPagination;

    public $date_filter;
    public $search_page = '';
    public $activeTab = 'logs'; // 'logs' or 'pages'

    public function mount()
    {
        $this->date_filter = now()->toDateString();
    }

    public function updatedDateFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        // 1. Detailed Visit Logs
        $logsQuery = PageView::with('user')
            ->when($this->date_filter, fn($q) => $q->where('view_date', $this->date_filter))
            ->when($this->search_page, fn($q) => $q->where('url', 'like', '%' . $this->search_page . '%'))
            ->latest('viewed_at');

        // 2. Summary per Page (Daily aggregated views + unique users count)
        $pageSummary = PageView::select(
                'url',
                'page_title',
                DB::raw('count(*) as total_views'),
                DB::raw('count(DISTINCT ip_address) as unique_visitors'),
                DB::raw('count(DISTINCT user_id) as logged_in_users')
            )
            ->when($this->date_filter, fn($q) => $q->where('view_date', $this->date_filter))
            ->groupBy('url', 'page_title')
            ->orderByDesc('total_views')
            ->get();

        return view('livewire.admin.page-analytics', [
            'logs' => $logsQuery->paginate(20),
            'pageSummary' => $pageSummary,
            'totalViewsToday' => PageView::where('view_date', $this->date_filter)->count(),
            'uniqueVisitorsToday' => PageView::where('view_date', $this->date_filter)->distinct('ip_address')->count(),
        ])->layout('layouts.admin-app'); // Aapka admin layout
    }
}