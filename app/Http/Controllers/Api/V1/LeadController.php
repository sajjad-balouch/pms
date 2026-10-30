<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Lead;
use App\Support\Tenant;
use App\Support\WhatsApp;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Leads CRM for agents and town owners (a town owner's team works on the owner's leads).
 */
class LeadController extends ApiController
{
    const STATUSES = ['new', 'contacted', 'site_visit', 'negotiation', 'closed_won', 'closed_lost'];

    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));
        $now = now();

        $page = $this->owned()
            ->with(['town:id,name', 'plot:id,plot_number', 'property:id,title'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->query('status')))
            ->when($request->filled('priority'), fn ($q) => $q->where('priority', $request->query('priority')))
            ->when($request->filled('source'), fn ($q) => $q->where('source', $request->query('source')))
            ->when($request->query('followup') === 'late', fn ($q) => $q->whereNotNull('next_followup_at')->where('next_followup_at', '<', $now))
            ->when($request->query('followup') === 'today', fn ($q) => $q->whereDate('next_followup_at', $now->toDateString()))
            ->when($request->query('followup') === 'upcoming', fn ($q) => $q->where('next_followup_at', '>', $now))
            ->when($search !== '', fn ($q) => $q->where(fn ($q) => $q->where('client_name', 'like', "%{$search}%")->orWhere('client_phone', 'like', "%{$search}%")))
            ->latest()
            ->paginate($this->perPage());

        $counts = $this->owned()->selectRaw('status, COUNT(*) AS n')->groupBy('status')->pluck('n', 'status');

        return $this->paginated($page, fn ($l) => Transform::lead($l), ['counts' => [
            'by_status' => collect(self::STATUSES)->mapWithKeys(fn ($s) => [$s => (int) ($counts[$s] ?? 0)]),
            'late_followups' => $this->owned()->whereNotIn('status', ['closed_won', 'closed_lost'])->whereNotNull('next_followup_at')->where('next_followup_at', '<', $now)->count(),
            'hot' => $this->owned()->where('priority', 'hot')->whereNotIn('status', ['closed_won', 'closed_lost'])->count(),
        ]]);
    }

    public function show(Lead $lead)
    {
        $this->authorizeLead($lead);
        $lead->load(['town:id,name', 'plot:id,plot_number', 'property:id,title']);

        return $this->ok(Transform::lead($lead) + ['whatsapp' => $this->whatsappTemplates($lead)]);
    }

    public function store(Request $request)
    {
        $lead = Lead::create($this->payload($request) + ['agent_id' => Tenant::ownerId()]);

        return $this->created(Transform::lead($lead->load(['town:id,name', 'plot:id,plot_number'])), __('New lead added successfully.'));
    }

    public function update(Request $request, Lead $lead)
    {
        $this->authorizeLead($lead);
        $lead->update($this->payload($request));

        return $this->ok(Transform::lead($lead->load(['town:id,name', 'plot:id,plot_number', 'property:id,title'])), __('Lead updated!'));
    }

    // Kanban drag / quick status change
    public function updateStatus(Request $request, Lead $lead)
    {
        $this->authorizeLead($lead);
        $data = $request->validate(['status' => ['required', Rule::in(self::STATUSES)]]);
        $lead->update($data);

        return $this->ok(Transform::lead($lead), __('Lead updated!'));
    }

    // "Done — follow up again in N days"
    public function snooze(Request $request, Lead $lead)
    {
        $this->authorizeLead($lead);
        $data = $request->validate(['days' => 'required|integer|in:1,3,7,14,30']);

        $lead->update([
            'status' => $lead->status === 'new' ? 'contacted' : $lead->status,
            'next_followup_at' => now()->addDays((int) $data['days'])->setTime(11, 0),
        ]);

        return $this->ok(Transform::lead($lead), __('Follow-up scheduled.'));
    }

    public function destroy(Lead $lead)
    {
        $this->authorizeLead($lead);
        $lead->delete();

        return $this->ok(null, __('Lead deleted.'));
    }

    protected function payload(Request $request): array
    {
        return $request->validate([
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:20',
            'client_email' => 'nullable|email',
            'town_id' => 'nullable|exists:towns,id',
            'plot_id' => 'nullable|exists:plots,id',
            'status' => ['required', Rule::in(self::STATUSES)],
            'priority' => 'required|in:hot,warm,cold',
            'source' => 'required|in:portal,whatsapp,call,walk_in,social_media,banner',
            'budget' => 'nullable|numeric|min:0',
            'next_followup_at' => 'nullable|date',
            'notes' => 'nullable|string|max:5000',
        ]);
    }

    // Ready-to-send WhatsApp messages (language follows Accept-Language)
    protected function whatsappTemplates(Lead $lead): array
    {
        $agent = request()->user()->name;
        $target = trim(($lead->town?->name ?? $lead->property?->title ?? __('Plot / Property')) . ' ' . ($lead->plot ? __('Plot #:number', ['number' => $lead->plot->plot_number]) : ''));
        $vars = ['client' => $lead->client_name, 'property' => $target, 'agent' => $agent];

        return collect([
            'intro' => __("Assalam o Alaikum :client!\n\nYou contacted us on AssanZameen.com about *:property*.\nI am your dealer :agent. Shall I send you its photos and rate plan on WhatsApp?", $vars),
            'site_visit' => __("Assalam o Alaikum :client!\n\nI am contacting you about a site visit for *:property*. Can you make time this evening or tomorrow to see it?\n\nContact: :agent", $vars),
            'negotiation' => __("Assalam o Alaikum :client!\n\nWe have discussed your offer with the owner. Can you visit the office today so we can finalise it face to face?\n\nContact: :agent", $vars),
            'token' => __("Assalam o Alaikum :client!\n\nMore buyers are coming for *:property*. If you want to finalise the deal, please contact us today for the token advance.\n\nContact: :agent", $vars),
        ])->map(fn ($text) => ['text' => $text, 'url' => WhatsApp::url($lead->client_phone, $text)])->all();
    }

    protected function owned()
    {
        return Lead::where('agent_id', Tenant::ownerId());
    }

    protected function authorizeLead(Lead $lead): void
    {
        abort_unless((int) $lead->agent_id === (int) Tenant::ownerId(), 404);
    }
}
