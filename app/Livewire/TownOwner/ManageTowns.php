<?php

namespace App\Livewire\TownOwner;

use App\Models\City;
use App\Models\Town;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ManageTowns extends Component
{
    use WithPagination, WithFileUploads;

    public $name, $location, $total_area, $noc_number, $google_map_url;
    public $editingTownId = null;

    // Searchable City Properties
    public $city_id = null;
    public $city_search = '';
    public $selected_city_name = '';

    // New Features: Map & Gallery Uploads
    public $new_master_plan_map;
    public $existing_master_plan_map = null;
    public $new_gallery_images = [];
    public $existing_gallery_images = [];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
            'total_area' => 'nullable|string|max:100',
            'noc_number' => 'nullable|string|max:100',
            'google_map_url' => 'nullable|url|max:500',
            'new_master_plan_map' => 'nullable|image|max:5120', // Max 5MB
            'new_gallery_images.*' => 'nullable|image|max:3072', // Max 3MB each
        ];
    }

    protected $messages = [
        'city_id.required' => 'City select karna zaroori hai.',
        'new_master_plan_map.max' => 'Town map ka size 5MB se zyada na ho.',
        'new_gallery_images.*.max' => 'Gallery image ka size 3MB se zyada na ho.',
    ];

    public function selectCity($id, $name)
    {
        $this->city_id = $id;
        $this->selected_city_name = $name;
        $this->city_search = '';
        $this->resetValidation('city_id');
    }

    public function createTown()
    {
        $this->validate();

        $cityName = $this->selected_city_name ?: City::find($this->city_id)?->name;

        if ($this->editingTownId) {
            $town = Town::where('user_id', Auth::id())->findOrFail($this->editingTownId);

            // 1. Handle Master Plan Map Upload
            $mapPath = $this->existing_master_plan_map;
            if ($this->new_master_plan_map) {
                if ($mapPath && File::exists(public_path($mapPath))) {
                    File::delete(public_path($mapPath));
                }

                $targetDir = public_path('town_maps');
                if (!File::isDirectory($targetDir)) {
                    File::makeDirectory($targetDir, 0755, true, true);
                }

                $mapName = 'map_' . time() . '_' . uniqid() . '.' . $this->new_master_plan_map->getClientOriginalExtension();
                $destination = $targetDir . DIRECTORY_SEPARATOR . $mapName;

                copy($this->new_master_plan_map->getRealPath(), $destination);
                
                $mapPath = 'town_maps/' . $mapName;
            }

            // 2. Handle Gallery Images Upload
            $galleryPaths = $this->existing_gallery_images ?? [];
            if (!empty($this->new_gallery_images)) {
                $galleryDir = public_path('town_galleries');
                if (!File::isDirectory($galleryDir)) {
                    File::makeDirectory($galleryDir, 0755, true, true);
                }

                foreach ($this->new_gallery_images as $image) {
                    $imgName = 'gallery_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $destination = $galleryDir . DIRECTORY_SEPARATOR . $imgName;

                    copy($image->getRealPath(), $destination);
                    $galleryPaths[] = 'town_galleries/' . $imgName;
                }
            }

            $town->update([
                'name' => $this->name,
                'location' => $this->location,
                'city_id' => $this->city_id,
                'city' => $cityName,
                'google_map_url' => $this->google_map_url,
                'master_plan_map' => $mapPath,
                'gallery_images' => $galleryPaths,
                'total_area' => $this->total_area,
                'noc_number' => $this->noc_number,
            ]);

            session()->flash('success', 'ٹاؤن کی تمام تفصیلات، میپ اور گیلری اپ ڈیٹ ہو گئی ہیں۔');
        } else {
            Town::create([
                'user_id' => Auth::id(),
                'name' => $this->name,
                'location' => $this->location,
                'city_id' => $this->city_id,
                'city' => $cityName,
                'google_map_url' => $this->google_map_url,
                'total_area' => $this->total_area,
                'noc_number' => $this->noc_number,
            ]);

            session()->flash('success', 'نیا ٹاؤن رجسٹر ہو گیا ہے۔ میپ اور گیلری ایڈٹ سیکشن سے شامل کریں۔');
        }

        $this->cancelEdit();
    }

    public function editTown($id)
    {
        $town = Town::where('user_id', Auth::id())->with('city')->findOrFail($id);

        $this->editingTownId = $town->id;
        $this->name = $town->name;
        $this->location = $town->location;
        $this->google_map_url = $town->google_map_url;
        $this->total_area = $town->total_area;
        $this->noc_number = $town->noc_number;

        $this->city_id = $town->city_id;
        $this->selected_city_name = $town->city?->name ?? $town->city ?? '';
        $this->city_search = '';

        // Load Existing Media
        $this->existing_master_plan_map = $town->master_plan_map;
        $this->existing_gallery_images = is_array($town->gallery_images) ? $town->gallery_images : [];
        $this->new_master_plan_map = null;
        $this->new_gallery_images = [];
    }

    public function deleteMasterPlanMap()
    {
        if ($this->editingTownId && $this->existing_master_plan_map) {
            if (File::exists(public_path($this->existing_master_plan_map))) {
                File::delete(public_path($this->existing_master_plan_map));
            }

            Town::where('id', $this->editingTownId)->update(['master_plan_map' => null]);
            $this->existing_master_plan_map = null;
            session()->flash('success', 'Town master plan map delete kar diya gaya.');
        }
    }

    public function deleteGalleryImage($index)
    {
        if (isset($this->existing_gallery_images[$index])) {
            $fileToDelete = $this->existing_gallery_images[$index];

            if (File::exists(public_path($fileToDelete))) {
                File::delete(public_path($fileToDelete));
            }

            unset($this->existing_gallery_images[$index]);
            $this->existing_gallery_images = array_values($this->existing_gallery_images);

            if ($this->editingTownId) {
                Town::where('id', $this->editingTownId)->update([
                    'gallery_images' => $this->existing_gallery_images,
                ]);
            }

            session()->flash('success', 'Gallery image delete kar di gayi.');
        }
    }

    // New: Remove All Gallery Images like Town Map
    public function deleteAllGalleryImages()
    {
        if ($this->editingTownId && !empty($this->existing_gallery_images)) {
            foreach ($this->existing_gallery_images as $file) {
                if (File::exists(public_path($file))) {
                    File::delete(public_path($file));
                }
            }

            Town::where('id', $this->editingTownId)->update(['gallery_images' => null]);
            $this->existing_gallery_images = [];
            session()->flash('success', 'Tamam gallery images remove kar di gayi hain.');
        }
    }

    public function cancelEdit()
    {
        $this->editingTownId = null;
        $this->reset([
            'name', 'location', 'city_id', 'city_search', 'selected_city_name',
            'google_map_url', 'total_area', 'noc_number',
            'new_master_plan_map', 'existing_master_plan_map',
            'new_gallery_images', 'existing_gallery_images'
        ]);
        $this->resetValidation();
    }

    public function deleteTown($id)
    {
        $town = Town::where('user_id', Auth::id())->findOrFail($id);

        if ($this->editingTownId === $id) {
            $this->cancelEdit();
        }

        // Clean up stored files
        if ($town->master_plan_map && File::exists(public_path($town->master_plan_map))) {
            File::delete(public_path($town->master_plan_map));
        }

        if (is_array($town->gallery_images)) {
            foreach ($town->gallery_images as $img) {
                if (File::exists(public_path($img))) {
                    File::delete(public_path($img));
                }
            }
        }

        $town->delete();
        session()->flash('success', 'ٹاؤن اور تمام میڈیا ریکارڈ ختم کر دیا گیا ہے۔');
    }

    public function render()
    {
        $citiesQuery = City::query()->where('is_active', 1);

        if ($this->city_search) {
            $citiesQuery->where('name', 'like', '%' . $this->city_search . '%');
        }

        return view('livewire.town-owner.manage-towns', [
            'towns' => Town::where('user_id', Auth::id())->with('city')->latest()->paginate(10),
            'cities' => $citiesQuery->orderBy('name')->take(25)->get(),
        ])->layout('layouts.app');
    }
}