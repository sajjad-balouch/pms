<?php

namespace App\Livewire\Agent;

use App\Models\DealLedger;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class CommissionLedger extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';

    // Modal Form States
    public $isModalOpen = false;
    public $deal_id;
    public $property_id;
    public $deal_title;
    public $client_name;
    public $client_phone;
    public $deal_date;
    public $deal_value = '';
    public $total_commission = '';
    public $received_amount = '';
    public $partner_name = '';
    public $partner_share = 0;
    public $notes = '';

    protected $rules = [
        'deal_title' => 'required|string|max:200',
        'client_name' => 'required|string|max:100',
        'client_phone' => 'required|string|max:20',
        'deal_date' => 'required|date',
        'deal_value' => 'required|numeric|min:0',
        'total_commission' => 'required|numeric|min:0',
        'received_amount' => 'required|numeric|min:0',
        'partner_share' => 'nullable|numeric|min:0',
        'notes' => 'nullable|string',
    ];

    public function createDeal()
    {
        $this->resetForm();
        $this->deal_date = date('Y-m-d');
        $this->isModalOpen = true;
    }

    public function resetForm()
    {
        $this->deal_id = null;
        $this->property_id = null;
        $this->deal_title = '';
        $this->client_name = '';
        $this->client_phone = '';
        $this->deal_date = date('Y-m-d');
        $this->deal_value = '';
        $this->total_commission = '';
        $this->received_amount = '';
        $this->partner_name = '';
        $this->partner_share = 0;
        $this->notes = '';
        $this->resetValidation();
    }

    public function saveDeal()
    {
        $this->validate();

        $totComm = floatval($this->total_commission);
        $rec = floatval($this->received_amount);
        $pending = max(0, $totComm - $rec);

        $status = 'pending';
        if ($rec >= $totComm && $totComm > 0) {
            $status = 'paid';
        } elseif ($rec > 0) {
            $status = 'partial';
        }

        DealLedger::updateOrCreate(
            ['id' => $this->deal_id, 'agent_id' => Auth::id()],
            [
                'property_id' => $this->property_id ?: null,
                'deal_title' => $this->deal_title,
                'client_name' => $this->client_name,
                'client_phone' => $this->client_phone,
                'deal_date' => $this->deal_date,
                'deal_value' => $this->deal_value,
                'total_commission' => $totComm,
                'received_amount' => $rec,
                'pending_amount' => $pending,
                'partner_name' => $this->partner_name,
                'partner_share' => $this->partner_share ?: 0,
                'status' => $status,
                'notes' => $this->notes,
            ]
        );

        session()->flash('message', $this->deal_id ? 'Deal khata update ho gaya.' : 'Nayi deal khate mein darj ho gayi!');
        $this->isModalOpen = false;
        $this->resetForm();
    }

    public function editDeal($id)
    {
        $deal = DealLedger::where('agent_id', Auth::id())->findOrFail($id);

        $this->deal_id = $deal->id;
        $this->property_id = $deal->property_id;
        $this->deal_title = $deal->deal_title;
        $this->client_name = $deal->client_name;
        $this->client_phone = $deal->client_phone;
        $this->deal_date = $deal->deal_date;
        $this->deal_value = $deal->deal_value;
        $this->total_commission = $deal->total_commission;
        $this->received_amount = $deal->received_amount;
        $this->partner_name = $deal->partner_name;
        $this->partner_share = $deal->partner_share;
        $this->notes = $deal->notes;

        $this->isModalOpen = true;
    }

    public function deleteDeal($id)
    {
        DealLedger::where('agent_id', Auth::id())->findOrFail($id)->delete();
        session()->flash('message', 'Deal record delete kar diya gaya.');
    }

    public function sendPaymentReminder($id)
    {
        $deal = DealLedger::where('agent_id', Auth::id())->findOrFail($id);
        
        $agentName = Auth::user()->name;
        $agency = Auth::user()->agency_name ?: 'Real Estate';
        $pending = number_format($deal->pending_amount);

        $msg = "Assalam-o-Alaikum {$deal->client_name} Sahab!\n\n"
             . "Umeed hai aap khairiyat se honge. Deal: *{$deal->deal_title}* ke hawale se rabta kiya ja raha hai.\n"
             . "Aapki taraf baki baqaya commission / fees raqam: *PKR {$pending}* wajib-ul-ada hai.\n\n"
             . "Barah-e-karam jald az jald iski adayegi ya intizam farma dein. Shukriya!\n\n"
             . "Talib-e-Dua: {$agentName}\n({$agency})";

        $cleanPhone = preg_replace('/[^0-9]/', '', $deal->client_phone);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '92' . substr($cleanPhone, 1);
        }

        $encoded = urlencode($msg);

        $this->dispatch('trigger-whatsapp', 
            appUrl: "whatsapp://send?phone={$cleanPhone}&text={$encoded}",
            webUrl: "https://web.whatsapp.com/send?phone={$cleanPhone}&text={$encoded}"
        );
    }

    public function render()
    {
        $query = DealLedger::where('agent_id', Auth::id());

        if ($this->search) {
            $query->where(function($q) {
                $q->where('deal_title', 'like', '%' . $this->search . '%')
                  ->orWhere('client_name', 'like', '%' . $this->search . '%')
                  ->orWhere('client_phone', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        // Calculations for Top Cards
        $totalEarned = DealLedger::where('agent_id', Auth::id())->sum('total_commission');
        $totalReceived = DealLedger::where('agent_id', Auth::id())->sum('received_amount');
        $totalPending = DealLedger::where('agent_id', Auth::id())->sum('pending_amount');
        $totalPartnerPaid = DealLedger::where('agent_id', Auth::id())->sum('partner_share');

        return view('livewire.agent.commission-ledger', [
            'deals' => $query->latest('deal_date')->paginate(10),
            'properties' => Property::where('agent_id', Auth::id())->get(),
            'totalEarned' => $totalEarned,
            'totalReceived' => $totalReceived,
            'totalPending' => $totalPending,
            'totalPartnerPaid' => $totalPartnerPaid,
        ])->layout('layouts.app');
    }
}