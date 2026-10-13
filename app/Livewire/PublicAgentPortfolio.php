<?php

namespace App\Livewire;

use App\Models\Property;
use App\Models\User;
use Livewire\Component;

class PublicAgentPortfolio extends Component
{
    public $agent;

    public function mount($username)
    {
        // dd($username);
        $this->agent = User::where('id', $username)->firstOrFail();
    }

    public function downloadVCard()
    {
        $agent = $this->agent;
        $name = $agent->agency_name ?: $agent->name;
        $phone = $agent->phone;
        $email = $agent->email;
        $address = $agent->office_address ?: 'Pakistan';

        $vcard = "BEGIN:VCARD\r\n"
               . "VERSION:3.0\r\n"
               . "FN:{$name}\r\n"
               . "ORG:{$agent->agency_name}\r\n"
               . "TEL;TYPE=CELL:{$phone}\r\n"
               . "EMAIL:{$email}\r\n"
               . "ADR;TYPE=WORK:;;{$address};;;;\r\n"
               . "URL:" . url('/agent/' . $agent->username) . "\r\n"
               . "END:VCARD\r\n";

        return response($vcard, 200, [
            'Content-Type' => 'text/vcard',
            'Content-Disposition' => 'attachment; filename="' . ($agent->username ?: 'agent') . '.vcf"',
        ]);
    }

    public function render()
    {

        $properties = Property::where('agent_id', $this->agent->id)
            ->where('status', 'available')
            ->with(['city', 'town'])
            ->latest()
            ->get();

        return view('livewire.public-agent-portfolio', [
            'properties' => $properties
        ])->layout('layouts.front-app');
    }
}