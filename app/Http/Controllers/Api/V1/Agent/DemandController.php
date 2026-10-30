<?php

namespace App\Http\Controllers\Api\V1\Agent;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Controllers\Api\V1\Transform;
use App\Models\AgentDemand;
use App\Support\WhatsApp;
use Illuminate\Http\Request;

// B2B dealer exchange: agents post client demands, other agents offer matching properties
class DemandController extends ApiController
{
    public function index(Request $request)
    {
        $me = $request->user()->id;
        $search = trim((string) $request->query('search'));

        $page = AgentDemand::with('agent:id,name,agency_name')
            ->when($request->boolean('mine'), fn ($q) => $q->where('agent_id', $me), fn ($q) => $q->where('status', 'open'))
            ->when($request->filled('type'), fn ($q) => $q->where('property_type', $request->query('type')))
            ->when($request->filled('city'), fn ($q) => $q->where('city', $request->query('city')))
            ->when($search !== '', fn ($q) => $q->where(fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('target_location', 'like', "%{$search}%")))
            ->latest()
            ->paginate($this->perPage());

        return $this->paginated($page, fn ($d) => $this->present($d, $me));
    }

    public function store(Request $request)
    {
        $demand = AgentDemand::create($this->payload($request) + ['agent_id' => $request->user()->id, 'status' => 'open']);

        return $this->created($this->present($demand->load('agent:id,name,agency_name'), $request->user()->id), __('New demand is now live on the network!'));
    }

    public function update(Request $request, AgentDemand $demand)
    {
        abort_unless((int) $demand->agent_id === (int) $request->user()->id, 404);
        $demand->update($this->payload($request));

        return $this->ok($this->present($demand->load('agent:id,name,agency_name'), $request->user()->id), __('Demand updated.'));
    }

    public function close(Request $request, AgentDemand $demand)
    {
        abort_unless((int) $demand->agent_id === (int) $request->user()->id, 404);
        $demand->update(['status' => 'closed']);

        return $this->ok(null, __('Demand closed.'));
    }

    // Contact the agent who posted the demand (ready WhatsApp message)
    public function contact(Request $request, AgentDemand $demand)
    {
        $demand->load('agent:id,name,phone,agency_name');
        abort_unless($demand->status === 'open', 404);

        if (! $demand->agent?->phone) {
            return $this->fail(__('This dealer has no phone number in their profile.'), 422);
        }

        $me = $request->user();
        $message = __("Assalam o Alaikum :poster!\n\nI saw your post on the AssanZameen.com B2B dealer network:\n📢 *:title*\n📍 Location: :location (:city)\n📐 Size: :size | Budget: :budget\n\nI have a client/property that matches this demand exactly. Can we close this deal on 50/50 sharing?\n\nDealer: :agent (:agency)", [
            'poster' => $demand->agent->name,
            'title' => $demand->title,
            'location' => $demand->target_location,
            'city' => $demand->city,
            'size' => $demand->area_size,
            'budget' => $demand->budget_max ? 'PKR ' . number_format($demand->budget_max) : __('As per budget'),
            'agent' => $me->name,
            'agency' => $me->agency_name ?: __('Agent'),
        ]);

        return $this->ok([
            'name' => $demand->agent->name,
            'phone' => $demand->agent->phone,
            'whatsapp_url' => WhatsApp::url($demand->agent->phone, $message),
        ]);
    }

    protected function payload(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'property_type' => 'required|in:house,plot,commercial,agricultural,apartment',
            'city' => 'required|string|max:100',
            'target_location' => 'required|string|max:255',
            'area_size' => 'required|string|max:100',
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0|gte:budget_min',
            'urgency' => 'required|in:urgent,standard,flexible',
            'details' => 'nullable|string|max:2000',
        ]);
    }

    protected function present(AgentDemand $d, int $me): array
    {
        return [
            'id' => $d->id,
            'title' => $d->title,
            'property_type' => $d->property_type,
            'city' => $d->city,
            'target_location' => $d->target_location,
            'area_size' => $d->area_size,
            'budget_min' => $d->budget_min !== null ? (float) $d->budget_min : null,
            'budget_max' => $d->budget_max !== null ? (float) $d->budget_max : null,
            'urgency' => $d->urgency,
            'details' => $d->details,
            'status' => $d->status,
            'is_mine' => (int) $d->agent_id === $me,
            'posted_by' => $d->relationLoaded('agent') && $d->agent ? ['id' => $d->agent->id, 'name' => $d->agent->name, 'agency_name' => $d->agent->agency_name] : null,
            'created_at' => Transform::time($d->created_at),
        ];
    }
}
