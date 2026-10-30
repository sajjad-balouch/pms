<?php

namespace App\Livewire\Agent;

use App\Models\AgentDemand;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class DealerExchange extends Component
{
    use WithPagination;

    // Filter states
    public $activeTab = 'all'; // 'all', 'my_demands'
    public $search = '';
    public $filterCity = '';
    public $filterType = '';

    // Modal Form States
    public $isModalOpen = false;
    public $demand_id;
    public $title;
    public $property_type = 'plot';
    public $city;
    public $target_location;
    public $area_size;
    public $budget_min;
    public $budget_max;
    public $urgency = 'urgent';
    public $details;

    protected $rules = [
        'title' => 'required|string|max:200',
        'property_type' => 'required|in:house,plot,commercial,agricultural,apartment',
        'city' => 'required|string|max:60',
        'target_location' => 'required|string|max:150',
        'area_size' => 'required|string|max:50',
        'budget_max' => 'required|numeric|min:0',
        'budget_min' => 'nullable|numeric|lte:budget_max',
        'urgency' => 'required|in:urgent,standard,flexible',
        'details' => 'nullable|string',
    ];

    public function createDemand()
    {
        $this->resetForm();
        $this->city = Auth::user()->city_name ?? '';
        $this->isModalOpen = true;
    }

    public function resetForm()
    {
        $this->demand_id = null;
        $this->title = '';
        $this->property_type = 'plot';
        $this->city = '';
        $this->target_location = '';
        $this->area_size = '';
        $this->budget_min = null;
        $this->budget_max = null;
        $this->urgency = 'urgent';
        $this->details = '';
        $this->resetValidation();
    }

    public function saveDemand()
    {
        $this->validate();

        AgentDemand::updateOrCreate(
            ['id' => $this->demand_id, 'agent_id' => Auth::id()],
            [
                'title' => $this->title,
                'property_type' => $this->property_type,
                'city' => $this->city,
                'target_location' => $this->target_location,
                'area_size' => $this->area_size,
                'budget_min' => $this->budget_min,
                'budget_max' => $this->budget_max,
                'urgency' => $this->urgency,
                'details' => $this->details,
                'status' => 'open',
            ]
        );

        session()->flash('message', $this->demand_id ? __('Demand updated.') : __('New demand is now live on the network!'));
        $this->isModalOpen = false;
        $this->resetForm();
    }

    public function editDemand($id)
    {
        $demand = AgentDemand::where('agent_id', Auth::id())->findOrFail($id);

        $this->demand_id = $demand->id;
        $this->title = $demand->title;
        $this->property_type = $demand->property_type;
        $this->city = $demand->city;
        $this->target_location = $demand->target_location;
        $this->area_size = $demand->area_size;
        $this->budget_min = $demand->budget_min;
        $this->budget_max = $demand->budget_max;
        $this->urgency = $demand->urgency;
        $this->details = $demand->details;

        $this->isModalOpen = true;
    }

    public function markClosed($id)
    {
        $demand = AgentDemand::where('agent_id', Auth::id())->findOrFail($id);
        $demand->update(['status' => 'closed']);
        session()->flash('message', __('Demand closed.'));
    }

    public function connectDealer($demandId)
    {
        $demand = AgentDemand::with('agent')->findOrFail($demandId);
        $poster = $demand->agent;

        if (!$poster || empty($poster->phone)) {
            $this->dispatch('notify-error', message: __('This dealer has no phone number in their profile.'));
            return;
        }

        $myAgentName = Auth::user()->name;
        $myAgency = Auth::user()->agency_name ?: __('Agent');
        $budgetDisplay = $demand->budget_max ? 'PKR ' . number_format($demand->budget_max) : __('As per budget');

        // Message language follows the panel language (English default, Urdu when switched)
        $msg = __("Assalam o Alaikum :poster!\n\nI saw your post on the AssanZameen.com B2B dealer network:\n📢 *:title*\n📍 Location: :location (:city)\n📐 Size: :size | Budget: :budget\n\nI have a client/property that matches this demand exactly. Can we close this deal on 50/50 sharing?\n\nDealer: :agent (:agency)", [
            'poster' => $poster->name,
            'title' => $demand->title,
            'location' => $demand->target_location,
            'city' => $demand->city,
            'size' => $demand->area_size,
            'budget' => $budgetDisplay,
            'agent' => $myAgentName,
            'agency' => $myAgency,
        ]);

        // Phone cleaning: 0300... -> 92300...
        $cleanPhone = preg_replace('/[^0-9]/', '', $poster->phone);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '92' . substr($cleanPhone, 1);
        }

        $encoded = urlencode($msg);

        // Native App protocol aur Direct Web protocol
        $this->dispatch('trigger-whatsapp', 
            appUrl: "whatsapp://send?phone={$cleanPhone}&text={$encoded}",
            webUrl: "https://web.whatsapp.com/send?phone={$cleanPhone}&text={$encoded}"
        );
    }

    public function render()
    {
        $query = AgentDemand::with('agent')->where('status', 'open');

        if ($this->activeTab === 'my_demands') {
            $query->where('agent_id', Auth::id());
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('target_location', 'like', '%' . $this->search . '%')
                  ->orWhere('city', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterCity) {
            $query->where('city', 'like', '%' . $this->filterCity . '%');
        }

        if ($this->filterType) {
            $query->where('property_type', $this->filterType);
        }

        return view('livewire.agent.dealer-exchange', [
            'demands' => $query->latest()->paginate(9),
            'totalOpen' => AgentDemand::where('status', 'open')->count(),
            'myDemandsCount' => AgentDemand::where('agent_id', Auth::id())->where('status', 'open')->count(),
        ])->layout('layouts.app');
    }
}