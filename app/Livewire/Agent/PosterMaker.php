<?php

namespace App\Livewire\Agent;

use App\Models\Property;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PosterMaker extends Component
{
    public $properties = [];
    public $selectedPropertyId = null;

    // Editable Inputs on Poster
    public $title = '';
    public $price = '';
    public $areaSize = '';
    public $location = '';
    public $propertyType = '';
    public $imageUrl = '';
    
    // Extra Notes / Amenities
    public $customTag = 'شاندار موقع (HOT DEAL)';
    public $feature1 = 'گیس، بجلی اور پانی دستیاب';
    public $feature2 = 'مین روڈ سے نزدیک اور کشادہ راستہ';
    public $feature3 = 'فوری قبضہ اور رجسٹری انتقال';

    // Agent Details
    public $agentName = '';
    public $agencyName = '';
    public $agentPhone = '';

    public function mount($propertyId = null)
    {
        $user = Auth::user();
        $this->agentName = $user->name;
        $this->agencyName = $user->agency_name ?: ($user->name . ' Real Estate');
        $this->agentPhone = $user->phone ?: '';

        $this->properties = Property::where('agent_id', $user->id)
            ->where('status', 'available')
            ->latest()
            ->get();

        if ($propertyId) {
            $this->selectProperty($propertyId);
        } elseif ($this->properties->isNotEmpty()) {
            $this->selectProperty($this->properties->first()->id);
        }
    }

    public function selectProperty($id)
    {
        $prop = Property::where('agent_id', Auth::id())->with('city')->find($id);
        if (!$prop) return;

        $this->selectedPropertyId = $prop->id;
        $this->title = $prop->title;
        $this->price = number_format($prop->price);
        $this->areaSize = $prop->area_size;
        $this->location = $prop->location . ', ' . ($prop->city?->name ?? 'Pakistan');
        $this->propertyType = strtoupper(str_replace('_', ' ', $prop->property_type));

        if (!empty($prop->images) && is_array($prop->images) && count($prop->images) > 0) {
            $this->imageUrl = asset('public/' . $prop->images[0]);
        } else {
            $this->imageUrl = '';
        }

        // Send full dynamic payload to Alpine Canvas
        $this->dispatch('property-updated', data: [
            'title' => $this->title,
            'price' => $this->price,
            'areaSize' => $this->areaSize,
            'location' => $this->location,
            'propertyType' => $this->propertyType,
            'imageUrl' => $this->imageUrl,
        ]);
    }

    public function render()
    {
        return view('livewire.agent.poster-maker')->layout('layouts.app');
    }
}