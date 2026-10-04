<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Property;
use App\Models\City;
use App\Models\Town;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CreateProperty extends Component
{
    use WithFileUploads;

    public $title;
    public $property_type = 'plot'; // plot, house, shop, commercial, etc.
    public $purpose = 'for_sale';    // for_sale, for_rent
    public $price;
    public $area_size;
    public $city_id;
    public $town_id;
    public $location;
    public $google_map_url;
    public $description;
    public $agent_id; // Kis agent/owner ke naam lagani hai
    public $images = [];
    public $status = 'available';

    protected function rules()
    {
        return [
            'title' => 'nullable|string|max:255',
            'property_type' => 'required|string',
            'purpose' => 'required|string|in:for_sale,for_rent',
            'price' => 'required|numeric|min:0',
            'area_size' => 'required|string|max:100',
            'city_id' => 'required|exists:cities,id',
            'town_id' => 'nullable|exists:towns,id',
            'location' => 'required|string|max:255',
            'google_map_url' => 'nullable|url',
            'description' => 'nullable|string',
            'agent_id' => 'nullable|exists:users,id',
            'images.*' => 'nullable|image|max:3072', // Max 3MB per photo
            'status' => 'required|in:available,sold,rented',
        ];
    }

    public function updatedCityId($value)
    {
        // City change hone par town reset karein
        $this->town_id = null;
    }

    public function save()
    {
        $this->validate();

        $uploadedPaths = [];
        if (!empty($this->images)) {
            // Agar folder mojood na ho to auto create kar le
            $destinationPath = public_path('property_images');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            foreach ($this->images as $photo) {
                $filename = 'prop_' . time() . '_' . Str::random(8) . '.' . $photo->getClientOriginalExtension();
                
                // Image ko direct public/property_images folder mein move/copy karein
                $photo->storePubliclyAs('property_images', $filename, 'public'); 

                // Database mein path save karne ke liye
                $uploadedPaths[] = 'property_images/' . $filename;
            }
        }

        // Agar admin ne agent_id select nahi kiya toh Auth admin user_id set hoga
        $assignedAgentId = $this->agent_id ?: Auth::id();

        Property::create([
            'title' => $this->title ?: ucfirst($this->property_type) . ' in ' . City::find($this->city_id)?->name,
            'property_type' => $this->property_type,
            'purpose' => $this->purpose,
            'price' => $this->price,
            'area_size' => $this->area_size,
            'city_id' => $this->city_id,
            'town_id' => $this->town_id ?: null,
            'agent_id' => $assignedAgentId,
            'location' => $this->location,
            'google_map_url' => $this->google_map_url,
            'description' => $this->description,
            'images' => $uploadedPaths,
            'status' => $this->status,
        ]);

        session()->flash('success', 'Property listed successfully by Admin!');

        return redirect()->route('admin.properties'); // Aapke admin properties list route par redirect
    }

    public function render()
    {
        $cities = City::where('is_active', 1)->orderBy('name')->get();
        
        $towns = Town::when($this->city_id, function ($q) {
            $q->where('city_id', $this->city_id);
        })->orderBy('name')->get();

        // Agents & Town owners dropdown for assignment
        $agents = User::whereIn('role', ['agent', 'town_owner', 'admin'])
            ->orderBy('name')
            ->get();

        return view('livewire.admin.create-property', [
            'cities' => $cities,
            'towns' => $towns,
            'agents' => $agents,
        ])->layout('layouts.admin-app'); // Aapka admin dashboard layout
    }
}