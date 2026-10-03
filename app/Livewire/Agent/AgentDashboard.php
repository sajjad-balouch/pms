<?php

namespace App\Livewire\Agent;

use App\Models\Lead;
use App\Models\Plot;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AgentDashboard extends Component
{
    public function render()
    {
        $agentId = Auth::id();

        // Agent Specific Metrics
        $totalLeads = Lead::where('agent_id', $agentId)->count();
        $activeLeads = Lead::where('agent_id', $agentId)->whereIn('status', ['new', 'contacted', 'site_visit', 'negotiation'])->count();
        $closedDeals = Lead::where('agent_id', $agentId)->where('status', 'closed_won')->count();
        
        // Recent Leads
        $recentLeads = Lead::where('agent_id', $agentId)
            ->with(['town', 'plot'])
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.agent.agent-dashboard', [
            'totalLeads' => $totalLeads,
            'activeLeads' => $activeLeads,
            'closedDeals' => $closedDeals,
            'recentLeads' => $recentLeads,
        ])->layout('layouts.app');
    }
}