<?php

namespace App\Livewire\Agent;

use App\Models\Lead;
use App\Models\Plot;
use App\Models\Town;
use Carbon\Carbon;
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
        $lead = Lead::where('agent_id', Auth::id())->find($leadId);
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
                'priority' => $this->priority,
                'source' => $this->source,
                'budget' => $this->budget,
                'next_followup_at' => $this->next_followup_at ?: null,
                'notes' => $this->notes,
            ]
        );

        session()->flash('message', $this->lead_id ? 'لیڈ اپ ڈیٹ ہو گئی!' : 'نئی لیڈ کامیابی سے شامل کر دی گئی۔');
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
        Lead::where('agent_id', Auth::id())->findOrFail($id)->delete();
        session()->flash('message', 'لیڈ ڈیلیٹ کر دی گئی۔');
    }

    public function openWhatsAppModal($leadId)
    {
        $this->selectedLeadForWhatsApp = Lead::where('agent_id', Auth::id())->with(['town', 'plot'])->findOrFail($leadId);
        $this->whatsappMessageType = 'intro';
        $this->customMessageNote = '';
        $this->isWhatsAppModalOpen = true;
    }

    public function sendWhatsAppMessage()
    {
        $lead = $this->selectedLeadForWhatsApp;
        if (!$lead) return;

        $agentName = Auth::user()->name;
        $townName = $lead->town?->name ?? 'پلاٹ / پراپرٹی';
        $plotInfo = $lead->plot ? "پلاٹ # {$lead->plot->plot_number}" : '';
        $targetProperty = trim("{$townName} {$plotInfo}");

        $msg = "";
        if ($this->whatsappMessageType === 'intro') {
            $msg = "السلام علیکم {$lead->client_name} صاحب!\n\n"
                 . "آپ نے آسان زمین (AssanZameen.com) پر *{$targetProperty}* کے بارے میں معلومات کے لیے رابطہ کیا تھا۔\n"
                 . "میں آپ کا ڈیلر {$agentName} حاضر ہوں۔ کیا میں آپ کو اس کی تصاویر اور ریٹ پلان واٹس ایپ کر دوں؟";
        } elseif ($this->whatsappMessageType === 'site_visit') {
            $msg = "السلام علیکم {$lead->client_name} صاحب!\n\n"
                 . "پراپرٹی *{$targetProperty}* کے موقع وزٹ کے لیے رابطہ کیا جا رہا ہے۔ کیا آپ آج شام یا کل موقع دیکھنے کے لیے وقت نکال سکتے ہیں؟\n\n"
                 . "رابطہ: {$agentName}";
        } elseif ($this->whatsappMessageType === 'negotiation') {
            $msg = "السلام علیکم {$lead->client_name} صاحب!\n\n"
                 . "آپ کی پیشکش پر مالک سے بات چیت ہوئی ہے۔ کیا آپ آج آفس تشریف لا سکتے ہیں تاکہ روبرو بات فائنل کی جا سکے؟\n\n"
                 . "رابطہ: {$agentName}";
        } elseif ($this->whatsappMessageType === 'token') {
            $msg = "السلام علیکم {$lead->client_name} صاحب!\n\n"
                 . "پراپرٹی *{$targetProperty}* پر مزید خریدار بھی آ رہے ہیں۔ اگر آپ ڈیل فائنل کرنا چاہتے ہیں تو برائے مہربانی ٹوکن ایڈوانس کے لیے آج رابطہ کریں۔\n\n"
                 . "رابطہ: {$agentName}";
        }

        if (!empty($this->customMessageNote)) {
            $msg .= "\n\n📌 *نوٹ:* " . $this->customMessageNote;
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

        $query = Lead::where('agent_id', Auth::id())->with(['town', 'plot']);

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
        $overdueCount = Lead::where('agent_id', Auth::id())->whereNotNull('next_followup_at')->where('next_followup_at', '<', $now)->count();
        $todayCount = Lead::where('agent_id', Auth::id())->whereDate('next_followup_at', $now->toDateString())->count();
        $hotCount = Lead::where('agent_id', Auth::id())->where('priority', 'hot')->count();

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
            'towns' => Town::all(),
        ])->layout('layouts.app');
    }
}