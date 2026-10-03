<?php

namespace App\Livewire\Agent;

use App\Models\Lead;
use App\Models\Plot;
use App\Models\Town;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ManageLeads extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    
    // Form Properties
    public $lead_id;
    public $town_id;
    public $plot_id;
    public $client_name;
    public $client_phone;
    public $client_email;
    public $status = 'new';
    public $budget;
    public $notes;

    public $isModalOpen = false;
    public $availablePlots = [];

    protected $rules = [
        'client_name' => 'required|string|max:255',
        'client_phone' => 'required|string|max:20',
        'client_email' => 'nullable|email',
        'town_id' => 'nullable|exists:towns,id',
        'plot_id' => 'nullable|exists:plots,id',
        'status' => 'required|in:new,contacted,site_visit,negotiation,closed_won,closed_lost',
        'budget' => 'nullable|numeric|min:0',
        'notes' => 'nullable|string',
    ];

    public function updatedTownId($value)
    {
        if ($value) {
            $this->availablePlots = Plot::where('town_id', $value)->where('status', 'available')->get();
        } else {
            $this->availablePlots = [];
        }
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->lead_id = null;
        $this->town_id = null;
        $this->plot_id = null;
        $this->client_name = '';
        $this->client_phone = '';
        $this->client_email = '';
        $this->status = 'new';
        $this->budget = null;
        $this->notes = '';
        $this->availablePlots = [];
    }

    public function store()
    {
        $this->validate();

        Lead::updateOrCreate(
            ['id' => $this->lead_id],
            [
                'agent_id' => Auth::id(),
                'town_id' => $this->town_id ?: null,
                'plot_id' => $this->plot_id ?: null,
                'client_name' => $this->client_name,
                'client_phone' => $this->client_phone,
                'client_email' => $this->client_email,
                'status' => $this->status,
                'budget' => $this->budget,
                'notes' => $this->notes,
            ]
        );

        session()->flash('message', $this->lead_id ? 'Lead updated successfully.' : 'New Lead created successfully.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $lead = Lead::where('agent_id', Auth::id())->findOrFail($id);

        $this->lead_id = $id;
        $this->town_id = $lead->town_id;
        $this->plot_id = $lead->plot_id;
        $this->client_name = $lead->client_name;
        $this->client_phone = $lead->client_phone;
        $this->client_email = $lead->client_email;
        $this->status = $lead->status;
        $this->budget = $lead->budget;
        $this->notes = $lead->notes;

        if ($this->town_id) {
            $this->availablePlots = Plot::where('town_id', $this->town_id)->get();
        }

        $this->openModal();
    }

    public function delete($id)
    {
        Lead::where('agent_id', Auth::id())->findOrFail($id)->delete();
        session()->flash('message', 'Lead deleted successfully.');
    }

    public function render()
    {
        $query = Lead::where('agent_id', Auth::id())
            ->with(['town', 'plot']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('client_name', 'like', '%' . $this->search . '%')
                  ->orWhere('client_phone', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        return view('livewire.agent.manage-leads', [
            'leads' => $query->latest()->paginate(10),
            'towns' => Town::all(),
        ])->layout('layouts.app');
    }
}