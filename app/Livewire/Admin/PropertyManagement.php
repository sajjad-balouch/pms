<?php

namespace App\Livewire\Admin;

use App\Models\City;
use App\Models\Property;
use App\Models\Town;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class PropertyManagement extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $statusFilter = '';
    public $selectedProperty = null;
    public $isDetailModalOpen = false;
    public $isBanModalOpen = false;
    public $banReason = '';
    public $propertyToBanId = null;

    // Edit Modal Properties
    public $isEditModalOpen = false;
    public $editPropertyId;
    public $edit_title;
    public $edit_property_type;
    public $edit_purpose;
    public $edit_price;
    public $edit_area_size;
    public $edit_city_id;
    public $edit_town_id;
    public $edit_location;
    public $edit_google_map_url;
    public $edit_description;
    public $edit_status;
    public $edit_is_active;

    // Image Management Properties
    public $existing_images = [];
    public $new_images = [];

    protected $queryString = [
        'search' => ['except' => ''], 
        'statusFilter' => ['except' => '']
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function viewDetails($id)
    {
        $this->selectedProperty = Property::with(['user', 'city', 'town'])->findOrFail($id);
        $this->isDetailModalOpen = true;
    }

    public function updateStatus($id, $status)
    {
        $property = Property::findOrFail($id);
        $property->is_active = $status;
        $property->save();

        session()->flash('status', "Property #{$property->id} status updated to {$status}.");
    }

    public function openBanModal($id)
    {
        $this->propertyToBanId = $id;
        $this->banReason = '';
        $this->isBanModalOpen = true;
    }

    public function banProperty()
    {
        $this->validate([
            'banReason' => 'required|string|min:5|max:255',
        ]);

        $property = Property::findOrFail($this->propertyToBanId);
        $property->is_active = 'banned';
        $property->rejection_reason = $this->banReason;
        $property->save();

        $this->isBanModalOpen = false;
        $this->propertyToBanId = null;
        session()->flash('status', "Property #{$property->id} has been banned.");
    }

    public function openEditModal($id)
    {
        $property = Property::findOrFail($id);

        $this->editPropertyId = $property->id;
        $this->edit_title = $property->title;
        $this->edit_property_type = $property->property_type ?? $property->type;
        $this->edit_purpose = $property->purpose ?? 'for_sale';
        $this->edit_price = $property->price;
        $this->edit_area_size = $property->area_size;
        $this->edit_city_id = $property->city_id;
        $this->edit_town_id = $property->town_id;
        $this->edit_location = $property->location;
        $this->edit_google_map_url = $property->google_map_url;
        $this->edit_description = $property->description;
        $this->edit_status = $property->status ?? 'available';
        $this->edit_is_active = $property->is_active ?? 'active';

        // Load existing images safely
        $this->existing_images = is_array($property->images) 
            ? array_values(array_filter($property->images)) 
            : [];
        $this->new_images = [];

        $this->isEditModalOpen = true;
    }

    public function removeExistingImage($index)
    {
        if (isset($this->existing_images[$index])) {
            unset($this->existing_images[$index]);
            $this->existing_images = array_values($this->existing_images);
        }
    }

    public function updatedEditCityId()
    {
        $this->edit_town_id = null;
    }

    public function updateProperty()
    {
        $this->validate([
            'edit_title' => 'required|string|max:255',
            'edit_property_type' => 'required|string',
            'edit_purpose' => 'required|string',
            'edit_price' => 'required|numeric|min:0',
            'edit_area_size' => 'required|string|max:100',
            'edit_city_id' => 'required|exists:cities,id',
            'edit_town_id' => 'nullable|exists:towns,id',
            'edit_location' => 'required|string|max:255',
            'edit_google_map_url' => 'nullable|url',
            'edit_description' => 'nullable|string',
            'edit_status' => 'required|string',
            'edit_is_active' => 'required|string',
            'new_images.*' => 'nullable|image|max:3072',
        ]);

        $property = Property::findOrFail($this->editPropertyId);

        // Process newly uploaded images directly to public/property_images without storage link
        $uploadedPaths = [];
        if (!empty($this->new_images)) {
            $destinationPath = public_path('property_images');
            
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0775, true);
            }

            foreach ($this->new_images as $photo) {
                $filename = 'prop_' . time() . '_' . Str::random(8) . '.' . $photo->getClientOriginalExtension();
                
                // Direct public folder copy/move
                copy($photo->getRealPath(), $destinationPath . DIRECTORY_SEPARATOR . $filename);
                
                $uploadedPaths[] = 'property_images/' . $filename;
            }
        }

        // Merge existing retained images with new images
        $finalImages = array_values(array_merge($this->existing_images, $uploadedPaths));

        $property->update([
            'title' => $this->edit_title,
            'property_type' => $this->edit_property_type,
            'purpose' => $this->edit_purpose,
            'price' => $this->edit_price,
            'area_size' => $this->edit_area_size,
            'city_id' => $this->edit_city_id,
            'town_id' => $this->edit_town_id ?: null,
            'location' => $this->edit_location,
            'google_map_url' => $this->edit_google_map_url,
            'description' => $this->edit_description,
            'images' => $finalImages,
            'status' => $this->edit_status,
            'is_active' => $this->edit_is_active,
        ]);

        $this->isEditModalOpen = false;
        $this->new_images = [];
        session()->flash('status', "Property #{$property->id} updated successfully!");
    }

    public function render()
    {
        $query = Property::with(['user', 'city', 'town']);

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('id', $this->search)
                  ->orWhereHas('user', function ($u) {
                      $u->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if (!empty($this->statusFilter)) {
            $query->where('is_active', $this->statusFilter);
        }

        $cities = City::where('is_active', 1)->orderBy('name')->get();
        $towns = Town::when($this->edit_city_id, function ($q) {
            $q->where('city_id', $this->edit_city_id);
        })->orderBy('name')->get();

        return view('livewire.admin.property-management', [
            'properties' => $query->latest()->paginate(12),
            'cities' => $cities,
            'towns' => $towns,
        ])->layout('layouts.admin-app');
    }
}