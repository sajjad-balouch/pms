<?php

namespace App\Livewire\Agent;

use App\Models\Property;
use App\Models\Town;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ManageProperties extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $typeFilter = '';
    public $purposeFilter = '';

    // Form Fields
    public $property_id;
    public $town_id;
    public $title;
    public $property_type = 'house';
    public $purpose = 'for_sale';
    public $price;
    public $area_size;
    public $city = 'Faisalabad';
    public $location;
    public $google_map_url; // <--- ADDED
    public $description;
    public $status = 'available';
    public $new_images = [];
    public $existing_images = [];

    public $isModalOpen = false;

    protected $rules = [
        'title' => 'required|string|max:255',
        'property_type' => 'required|in:residential,commercial,agricultural,shop,house,apartment,plot',
        'purpose' => 'required|in:for_sale,for_rent',
        'price' => 'required|numeric|min:0',
        'area_size' => 'required|string|max:50',
        'city' => 'required|string|max:100',
        'location' => 'required|string|max:255',
        'google_map_url' => 'nullable|url|max:500', // <--- ADDED
        'description' => 'nullable|string',
        'status' => 'required|in:available,under_offer,sold,rented',
        'new_images.*' => 'nullable|image|max:2048',
    ];

    public function create()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    public function resetForm()
    {
        $this->property_id = null;
        $this->town_id = null;
        $this->title = '';
        $this->property_type = 'house';
        $this->purpose = 'for_sale';
        $this->price = '';
        $this->area_size = '';
        $this->city = 'Faisalabad';
        $this->location = '';
        $this->google_map_url = ''; // <--- RESET
        $this->description = '';
        $this->status = 'available';
        $this->new_images = [];
        $this->existing_images = [];
    }

    public function save()
    {
        $this->validate();

        $imagePaths = $this->existing_images;
        if (!empty($this->new_images)) {
            $destinationPath = public_path('property_images');

            foreach ($this->new_images as $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                copy($image->getRealPath(), $destinationPath . '/' . $imageName);
                $imagePaths[] = 'property_images/' . $imageName;
            }
        }
        // dd($this->google_map_url);
        Property::updateOrCreate(
            ['id' => $this->property_id],
            [
                'agent_id' => Auth::id(),
                'town_id' => $this->town_id ?: null,
                'title' => $this->title,
                'property_type' => $this->property_type,
                'purpose' => $this->purpose,
                'price' => $this->price,
                'area_size' => $this->area_size,
                'city' => $this->city,
                'location' => $this->location,
                'google_map_url' => $this->google_map_url, // <--- SAVED
                'description' => $this->description,
                'images' => $imagePaths,
                'status' => $this->status,
            ]
        );

        session()->flash('message', $this->property_id ? 'Property updated successfully.' : 'New Property listed successfully.');
        $this->isModalOpen = false;
        $this->resetForm();
    }

    public function edit($id)
    {
        $property = Property::where('agent_id', Auth::id())->findOrFail($id);

        $this->property_id = $property->id;
        $this->town_id = $property->town_id;
        $this->title = $property->title;
        $this->property_type = $property->property_type;
        $this->purpose = $property->purpose;
        $this->price = $property->price;
        $this->area_size = $property->area_size;
        $this->city = $property->city;
        $this->location = $property->location;
        $this->google_map_url = $property->google_map_url; // <--- POPULATE
        $this->description = $property->description;
        $this->status = $property->status;
        $this->existing_images = $property->images ?? [];

        $this->isModalOpen = true;
    }

    public function delete($id)
    {
        Property::where('agent_id', Auth::id())->findOrFail($id)->delete();
        session()->flash('message', 'Property deleted.');
    }

    public function render()
    {
        $query = Property::where('agent_id', Auth::id())->with('town');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('location', 'like', '%' . $this->search . '%')
                  ->orWhere('city', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->typeFilter) {
            $query->where('property_type', $this->typeFilter);
        }

        if ($this->purposeFilter) {
            $query->where('purpose', $this->purposeFilter);
        }

        return view('livewire.agent.manage-properties', [
            'properties' => $query->latest()->paginate(10),
            'towns' => Town::all(),
        ])->layout('layouts.app');
    }
}