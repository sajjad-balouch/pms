<?php

namespace App\Livewire\Agent;

use App\Models\Lead;
use App\Models\Plot;
use App\Models\Town;
use Carbon\Carbon;
use App\Support\Tenant;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ManageLeads extends Component
{
    use WithPagination;

    // View Toggle
    public $viewMode = 'kanban'; // 'kanban' ya 'table'

    // Filters
    public $search = '';
    public $statusFilter = '';
    public $priorityFilter = '';
    public $followupFilter = ''; // 'overdue', 'today', 'upcoming'
    
    // Form Properties
    public $lead_id;
    public $town_id;
    public $plot_id;
    public $client_name;
    public $client_phone;
    public $client_email;
    public $status = 'new';
    public $priority = 'warm';
    public $source = 'portal';
    public $budget;
    public $next_followup_at;
    public $notes;

    public $isModalOpen = false;
    public $availablePlots = [];

    // WhatsApp Modal States
    public $isWhatsAppModalOpen = false;
    public $selectedLeadForWhatsApp = null;
    public $whatsappMessageType = 'intro';
    public $customMessageNote = '';

    protected $rules = [
        'client_name' => 'required|string|max:255',
        'client_phone' => 'required|string|max:20',
        'client_email' => 'nullable|email',
        'town_id' => 'nullable|exists:towns,id',
        'plot_id' => 'nullable|exists:plots,id',
        'status' => 'required|in:new,contacted,site_visit,negotiation,closed_won,closed_lost',
        'priority' => 'required|in:hot,warm,cold',
        'source' => 'required|in:portal,whatsapp,call,walk_in,social_media,banner',
        'budget' => 'nullable|numeric|min:0',
        'next_followup_at' => 'nullable|date',
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

    // Drag-and-Drop update handler
    public function updateLeadStatus($leadId, $newStatus)
    {
        $lead = $this->ownedQuery()->find($leadId);
        if ($lead && in_array($newStatus, ['new', 'contacted', 'site_visit', 'negotiation', 'closed_won', 'closed_lost'])) {
            $lead->update(['status' => $newStatus]);
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
        $this->priority = 'warm';
        $this->source = 'portal';
        $this->budget = null;
        $this->next_followup_at = null;
        $this->notes = '';
        $this->availablePlots = [];
        $this->resetValidation();
    }

    public function store()
    {
        $this->validate();

        // Edit sirf apni lead — pehle lead_id badal kar kisi aur agent ki lead apne naam ho sakti thi
        $lead = $this->lead_id ? $this->ownedQuery()->findOrFail($this->lead_id) : new Lead();

        $lead->fill(
            [
                'agent_id' => $this->ownerId(),
                'town_id' => $this->town_id ?: null,
                'plot_id' => $this->plot_id ?: null,
                'client_name' => $this->client_name,
                'client_phone' => $this->client_phone,
                'client_email' => $this->client_email,
                'status' => $this->status,
                'priority' => $this->priority,
                'source' => $this->source,
                'budget' => $this->budget,
                'next_followup_at' => $this->next_followup_at ?: null,
                'notes' => $this->notes,
            ]
        )->save();

        session()->flash('message', $this->lead_id ? __('Lead updated!') : __('New lead added successfully.'));
        $this->closeModal();
    }

    public function edit($id)
    {
        $lead = $this->ownedQuery()->findOrFail($id);

        $this->lead_id = $id;
        $this->town_id = $lead->town_id;
        $this->plot_id = $lead->plot_id;
        $this->client_name = $lead->client_name;
        $this->client_phone = $lead->client_phone;
        $this->client_email = $lead->client_email;
        $this->status = $lead->status;
        $this->priority = $lead->priority ?? 'warm';
        $this->source = $lead->source ?? 'portal';
        $this->budget = $lead->budget;
        $this->next_followup_at = $lead->next_followup_at ? Carbon::parse($lead->next_followup_at)->format('Y-m-d\TH:i') : null;
        $this->notes = $lead->notes;

        if ($this->town_id) {
            $this->availablePlots = Plot::where('town_id', $this->town_id)->get();
        }

        $this->openModal();
    }

    public function delete($id)
    {
        $this->ownedQuery()->findOrFail($id)->delete();
        session()->flash('message', __('Lead deleted.'));
    }

    public function openWhatsAppModal($leadId)
    {
        $this->selectedLeadForWhatsApp = $this->ownedQuery()->with(['town', 'plot'])->findOrFail($leadId);
        $this->whatsappMessageType = 'intro';
        $this->customMessageNote = '';
        $this->isWhatsAppModalOpen = true;
    }

    public function sendWhatsAppMessage()
    {
        $lead = $this->selectedLeadForWhatsApp;
        if (!$lead) return;

        $agentName = Auth::user()->name;
        $townName = $lead->town?->name ?? $lead->property?->title ?? __('Plot / Property');
        $plotInfo = $lead->plot ? __('Plot #:number', ['number' => $lead->plot->plot_number]) : '';
        $targetProperty = trim("{$townName} {$plotInfo}");

        // Message language follows the panel language (English default, Urdu when switched)
        $vars = ['client' => $lead->client_name, 'property' => $targetProperty, 'agent' => $agentName];

        $msg = match ($this->whatsappMessageType) {
            'site_visit' => __("Assalam o Alaikum :client!\n\nI am contacting you about a site visit for *:property*. Can you make time this evening or tomorrow to see it?\n\nContact: :agent", $vars),
            'negotiation' => __("Assalam o Alaikum :client!\n\nWe have discussed your offer with the owner. Can you visit the office today so we can finalise it face to face?\n\nContact: :agent", $vars),
            'token' => __("Assalam o Alaikum :client!\n\nMore buyers are coming for *:property*. If you want to finalise the deal, please contact us today for the token advance.\n\nContact: :agent", $vars),
            default => __("Assalam o Alaikum :client!\n\nYou contacted us on AssanZameen.com about *:property*.\nI am your dealer :agent. Shall I send you its photos and rate plan on WhatsApp?", $vars),
        };

        if (!empty($this->customMessageNote)) {
            $msg .= "\n\n📌 *" . __('Note') . ":* " . $this->customMessageNote;
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $lead->client_phone);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '92' . substr($cleanPhone, 1);
        }

        if ($lead->status === 'new') {
            $lead->update(['status' => 'contacted']);
        }

        $lead->update(['last_whatsapp_at' => now()]);

        $encoded = urlencode($msg);
        $this->isWhatsAppModalOpen = false;

        $this->dispatch('trigger-whatsapp', 
            appUrl: "whatsapp://send?phone={$cleanPhone}&text={$encoded}", 
            webUrl: "https://web.whatsapp.com/send?phone={$cleanPhone}&text={$encoded}"
        );
    }

    public function render()
    {
        $now = Carbon::now();

        $query = $this->ownedQuery()->with(['town', 'plot']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('client_name', 'like', '%' . $this->search . '%')
                  ->orWhere('client_phone', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->priorityFilter) {
            $query->where('priority', $this->priorityFilter);
        }

        // Follow-up Filters
        if ($this->followupFilter === 'overdue') {
            $query->whereNotNull('next_followup_at')->where('next_followup_at', '<', $now);
        } elseif ($this->followupFilter === 'today') {
            $query->whereDate('next_followup_at', $now->toDateString());
        } elseif ($this->followupFilter === 'upcoming') {
            $query->where('next_followup_at', '>', $now);
        }

        // Metrics for summary bar
        $overdueCount = $this->ownedQuery()->whereNotNull('next_followup_at')->where('next_followup_at', '<', $now)->count();
        $todayCount = $this->ownedQuery()->whereDate('next_followup_at', $now->toDateString())->count();
        $hotCount = $this->ownedQuery()->where('priority', 'hot')->count();

        // Agar Kanban view hai toh pagination ke baghair grouped records
        if ($this->viewMode === 'kanban') {
            $allLeads = (clone $query)->latest()->get();
            $kanbanColumns = [
                'new' => $allLeads->where('status', 'new'),
                'contacted' => $allLeads->where('status', 'contacted'),
                'site_visit' => $allLeads->where('status', 'site_visit'),
                'negotiation' => $allLeads->where('status', 'negotiation'),
                'closed_won' => $allLeads->where('status', 'closed_won'),
                'closed_lost' => $allLeads->where('status', 'closed_lost'),
            ];
            $paginatedLeads = null;
        } else {
            $kanbanColumns = [];
            $paginatedLeads = $query->latest()->paginate(10);
        }

        return view('livewire.agent.manage-leads', [
            'leads' => $paginatedLeads,
            'kanbanColumns' => $kanbanColumns,
            'overdueCount' => $overdueCount,
            'todayCount' => $todayCount,
            'hotCount' => $hotCount,
            // Town owner: sirf apni schemes. Agent: active schemes (sirf zaroori columns)
            'towns' => Auth::user()?->role === 'town_owner'
                ? Town::where('user_id', $this->ownerId())->orderBy('name')->get(['id', 'name'])
                : Town::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ])->layout('layouts.app');
    }

    // Agent: khud. Town owner ki team: owner ka account (leads owner ke naam par)
    protected function ownerId(): int
    {
        return (int) Tenant::ownerId();
    }

    protected function ownedQuery()
    {
        return Lead::where('agent_id', $this->ownerId());
    }
}