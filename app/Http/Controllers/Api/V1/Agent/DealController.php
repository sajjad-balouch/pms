<?php

namespace App\Http\Controllers\Api\V1\Agent;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Controllers\Api\V1\Transform;
use App\Models\DealLedger;
use App\Support\WhatsApp;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

// Commission ledger
class DealController extends ApiController
{
    public function index(Request $request)
    {
        $agentId = $request->user()->id;
        $search = trim((string) $request->query('search'));

        $page = DealLedger::where('agent_id', $agentId)
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->query('status')))
            ->when($search !== '', fn ($q) => $q->where(fn ($q) => $q->where('deal_title', 'like', "%{$search}%")->orWhere('client_phone', 'like', "%{$search}%")->orWhere('client_name', 'like', "%{$search}%")))
            ->latest('deal_date')->latest('id')
            ->paginate($this->perPage());

        $totals = DealLedger::where('agent_id', $agentId)
            ->selectRaw('COALESCE(SUM(total_commission),0) AS total, COALESCE(SUM(received_amount),0) AS received, COALESCE(SUM(pending_amount),0) AS pending, COALESCE(SUM(partner_share),0) AS partner')
            ->first();

        return $this->paginated($page, fn ($d) => $this->present($d), ['totals' => [
            'total_commission' => (float) $totals->total,
            'received' => (float) $totals->received,
            'pending' => (float) $totals->pending,
            'partner_share' => (float) $totals->partner,
        ]]);
    }

    public function store(Request $request)
    {
        $deal = DealLedger::create($this->payload($request) + ['agent_id' => $request->user()->id]);

        return $this->created($this->present($deal), __('Deal saved.'));
    }

    public function update(Request $request, DealLedger $deal)
    {
        abort_unless((int) $deal->agent_id === (int) $request->user()->id, 404);
        $deal->update($this->payload($request));

        return $this->ok($this->present($deal), __('Deal updated.'));
    }

    public function destroy(Request $request, DealLedger $deal)
    {
        abort_unless((int) $deal->agent_id === (int) $request->user()->id, 404);
        $deal->delete();

        return $this->ok(null, __('Deal deleted.'));
    }

    protected function payload(Request $request): array
    {
        $data = $request->validate([
            'property_id' => ['nullable', Rule::exists('properties', 'id')->where('agent_id', $request->user()->id)],
            'deal_title' => 'required|string|max:255',
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:20',
            'deal_date' => 'required|date',
            'deal_value' => 'required|numeric|min:0',
            'total_commission' => 'required|numeric|min:0',
            'received_amount' => 'required|numeric|min:0',
            'partner_name' => 'nullable|string|max:255',
            'partner_share' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:2000',
        ]);

        $total = (float) $data['total_commission'];
        $received = (float) $data['received_amount'];

        return $data + [
            'pending_amount' => max(0, $total - $received),
            'partner_share' => $data['partner_share'] ?? 0,
            'status' => $received >= $total && $total > 0 ? 'paid' : ($received > 0 ? 'partial' : 'pending'),
        ];
    }

    protected function present(DealLedger $d): array
    {
        return [
            'id' => $d->id,
            'property_id' => $d->property_id,
            'deal_title' => $d->deal_title,
            'client_name' => $d->client_name,
            'client_phone' => $d->client_phone,
            'deal_date' => Transform::date($d->deal_date),
            'deal_value' => (float) $d->deal_value,
            'total_commission' => (float) $d->total_commission,
            'received_amount' => (float) $d->received_amount,
            'pending_amount' => (float) $d->pending_amount,
            'partner_name' => $d->partner_name,
            'partner_share' => (float) $d->partner_share,
            'status' => $d->status, // paid | partial | pending
            'notes' => $d->notes,
            'reminder_whatsapp_url' => $d->pending_amount > 0
                ? WhatsApp::url($d->client_phone, __('Assalam o Alaikum :client, a reminder about the pending commission of Rs. :amount for :deal. Thank you.', [
                    'client' => $d->client_name, 'amount' => number_format((float) $d->pending_amount), 'deal' => $d->deal_title,
                ]))
                : null,
        ];
    }
}
