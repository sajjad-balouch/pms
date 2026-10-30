<?php

namespace App\Livewire\TownOwner;

use App\Support\Tenant;
use App\Models\City;
use App\Models\Town;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ManageTowns extends Component
{
    use WithPagination, WithFileUploads;

    // Shared hosting par jahan URL mein /public lagta hai wahan 'public/' set karein, warna ''
    const MEDIA_URL_PREFIX = 'public/';

    const MAX_GALLERY_IMAGES = 20;

    public $name, $location, $total_area, $noc_number, $google_map_url;
    public $late_fee_percent = 0;
    public $editingTownId = null;

    // Searchable City Properties
    public $city_id = null;
    public $city_search = '';
    public $selected_city_name = '';

    // Map & Gallery Uploads (existing media is always read from the DB, never from these props)
    public $new_master_plan_map;
    public $new_gallery_images = [];
    public $uploadIteration = 0;

    // Table search
    #[Url(except: '')]
    public $search = '';

    protected function rules()
    {
        return [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('towns', 'name')->where('user_id', Tenant::ownerId())->ignore($this->editingTownId),
            ],
            'location' => 'required|string|max:255',
            'city_id' => ['required', Rule::exists('cities', 'id')->where('is_active', 1)],
            'total_area' => 'nullable|string|max:100',
            'noc_number' => 'nullable|string|max:100',
            'late_fee_percent' => 'nullable|numeric|min:0|max:20',
            'google_map_url' => 'nullable|url|max:500',
            'new_master_plan_map' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120', // Max 5MB
            'new_gallery_images' => 'array|max:10',
            'new_gallery_images.*' => 'image|mimes:jpg,jpeg,png,webp|max:3072', // Max 3MB each
        ];
    }

    protected $messages = [
        'name.unique' => 'Is naam ki scheme pehle se maujood hai.',
        'city_id.required' => 'City select karna zaroori hai.',
        'city_id.exists' => 'Select ki gayi city active nahi hai.',
        'new_master_plan_map.max' => 'Town map ka size 5MB se zyada na ho.',
        'new_master_plan_map.mimes' => 'Map sirf JPG, PNG ya WEBP format mein ho.',
        'new_gallery_images.max' => 'Ek waqt mein zyada se zyada 10 images upload karein.',
        'new_gallery_images.*.max' => 'Gallery image ka size 3MB se zyada na ho.',
        'new_gallery_images.*.mimes' => 'Gallery images sirf JPG, PNG ya WEBP format mein hon.',
    ];

    // Validate files as soon as they are picked, not only on submit
    public function updatedNewMasterPlanMap()
    {
        $this->validate(Arr::only($this->rules(), ['new_master_plan_map']));
    }

    public function updatedNewGalleryImages()
    {
        $this->validate(Arr::only($this->rules(), ['new_gallery_images', 'new_gallery_images.*']));
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function selectCity($id)
    {
        // Name comes from the DB, not from the browser
        $city = City::where('is_active', 1)->find($id);
        if (! $city) {
            return;
        }

        $this->city_id = $city->id;
        $this->selected_city_name = $city->name;
        $this->city_search = '';
        $this->resetValidation('city_id');
    }

    public function removeNewGalleryImage($index)
    {
        $images = $this->new_gallery_images;
        unset($images[$index]);
        $this->new_gallery_images = array_values($images);
    }

    public function saveTown()
    {
        $this->validate();

        $town = $this->editingTownId
            ? $this->ownedTown($this->editingTownId)
            : new Town(['user_id' => Tenant::ownerId()]);

        $oldMap = $town->master_plan_map;
        $gallery = is_array($town->gallery_images) ? $town->gallery_images : [];

        if (count($gallery) + count($this->new_gallery_images) > self::MAX_GALLERY_IMAGES) {
            $this->addError('new_gallery_images', 'Ek scheme mein zyada se zyada ' . self::MAX_GALLERY_IMAGES . ' gallery images ho sakti hain.');
            return;
        }

        $newMap = $this->new_master_plan_map
            ? $this->storeImage($this->new_master_plan_map, 'town_maps', 'map')
            : null;

        foreach ($this->new_gallery_images as $image) {
            $gallery[] = $this->storeImage($image, 'town_galleries', 'gallery');
        }

        $isNew = ! $town->exists;

        $town->fill([
            'name' => $this->name,
            'location' => $this->location,
            'city_id' => $this->city_id,
            'city' => City::find($this->city_id)?->name,
            'google_map_url' => $this->google_map_url,
            'master_plan_map' => $newMap ?? $oldMap,
            'gallery_images' => $gallery ?: null,
            'total_area' => $this->total_area,
            'noc_number' => $this->noc_number,
            'late_fee_percent' => (float) ($this->late_fee_percent ?: 0),
        ])->save();

        // Remove the old map only after the new one is saved successfully
        if ($newMap && $oldMap) {
            $this->deleteFile($oldMap);
        }

        session()->flash('success', $isNew
            ? 'نیا ٹاؤن رجسٹر ہو گیا ہے۔'
            : 'ٹاؤن کی تمام تفصیلات، میپ اور گیلری اپ ڈیٹ ہو گئی ہیں۔');

        $this->cancelEdit();
    }

    public function editTown($id)
    {
        $town = $this->ownedTown($id);

        $this->resetValidation();
        $this->editingTownId = $town->id;
        $this->name = $town->name;
        $this->location = $town->location;
        $this->google_map_url = $town->google_map_url;
        $this->total_area = $town->total_area;
        $this->noc_number = $town->noc_number;
        $this->late_fee_percent = (float) $town->late_fee_percent;

        $this->city_id = $town->city_id;
        $this->selected_city_name = City::find($town->city_id)?->name ?? $town->city ?? '';
        $this->city_search = '';

        $this->new_master_plan_map = null;
        $this->new_gallery_images = [];
        $this->uploadIteration++;

        $this->dispatch('town-form-opened');
    }

    public function deleteMasterPlanMap()
    {
        if (! $this->editingTownId) {
            return;
        }

        $town = $this->ownedTown($this->editingTownId);
        if ($town->master_plan_map) {
            $this->deleteFile($town->master_plan_map);
            $town->update(['master_plan_map' => null]);
            session()->flash('success', 'Town master plan map delete kar diya gaya.');
        }
    }

    public function deleteGalleryImage($index)
    {
        if (! $this->editingTownId) {
            return;
        }

        $town = $this->ownedTown($this->editingTownId);
        $gallery = is_array($town->gallery_images) ? $town->gallery_images : [];

        if (isset($gallery[$index])) {
            $this->deleteFile($gallery[$index]);
            unset($gallery[$index]);
            $gallery = array_values($gallery);

            $town->update(['gallery_images' => $gallery ?: null]);
            session()->flash('success', 'Gallery image delete kar di gayi.');
        }
    }

    public function deleteAllGalleryImages()
    {
        if (! $this->editingTownId) {
            return;
        }

        $town = $this->ownedTown($this->editingTownId);
        foreach ((array) $town->gallery_images as $file) {
            $this->deleteFile($file);
        }

        $town->update(['gallery_images' => null]);
        session()->flash('success', 'Tamam gallery images remove kar di gayi hain.');
    }

    public function cancelEdit()
    {
        $this->reset([
            'editingTownId', 'name', 'location', 'city_id', 'city_search', 'selected_city_name',
            'google_map_url', 'total_area', 'noc_number', 'late_fee_percent',
            'new_master_plan_map', 'new_gallery_images',
        ]);
        $this->uploadIteration++;
        $this->resetValidation();
    }

    public function deleteTown($id)
    {
        $town = $this->ownedTown($id);

        // Booked/sold plots ke saath scheme delete nahi ho sakti (customers ka record khatam ho jayega)
        if ($town->plots()->whereIn('status', ['booked', 'sold'])->exists()) {
            session()->flash('error', 'Is scheme mein booked/sold plots hain, is liye ye delete nahi ho sakti.');
            return;
        }

        if ($this->editingTownId == $id) {
            $this->cancelEdit();
        }

        $files = array_filter(array_merge([$town->master_plan_map], (array) $town->gallery_images));

        DB::transaction(function () use ($town) {
            $town->plots()->delete();
            $town->delete();
        });

        // Files are removed only after the DB delete succeeded
        foreach ($files as $file) {
            $this->deleteFile($file);
        }

        session()->flash('success', 'ٹاؤن اور تمام میڈیا ریکارڈ ختم کر دیا گیا ہے۔');
    }

    public function render()
    {
        $editingTown = $this->editingTownId
            ? Town::where('user_id', Tenant::ownerId())->find($this->editingTownId)
            : null;

        if ($this->editingTownId && ! $editingTown) {
            $this->cancelEdit();
        }

        $search = trim($this->search);

        $towns = Town::where('user_id', Tenant::ownerId())
            ->withCount([
                'plots',
                'plots as sold_plots_count' => fn ($q) => $q->whereIn('status', ['booked', 'sold']),
            ])
            ->when($search !== '', fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('noc_number', 'like', "%{$search}%");
            }))
            ->latest()
            ->paginate(10);

        $cities = City::where('is_active', 1)
            ->when($this->city_search, fn ($q) => $q->where('name', 'like', '%' . $this->city_search . '%'))
            ->orderBy('name')
            ->take(25)
            ->get(['id', 'name']);

        return view('livewire.town-owner.manage-towns', [
            'towns' => $towns,
            'cities' => $cities,
            'editingTown' => $editingTown,
            'existingGallery' => is_array($editingTown?->gallery_images) ? $editingTown->gallery_images : [],
            'maxGallery' => self::MAX_GALLERY_IMAGES,
            'mediaUrl' => fn ($path) => asset(self::MEDIA_URL_PREFIX . $path),
        ])->layout('layouts.app');
    }

    protected function ownedTown($id): Town
    {
        return Town::where('user_id', Tenant::ownerId())->findOrFail($id);
    }

    protected function storeImage($file, string $dir, string $prefix): string
    {
        $targetDir = public_path($dir);
        File::ensureDirectoryExists($targetDir, 0755);

        // Extension comes from the real MIME type, never from the user's file name (blocks shell.php uploads)
        $fileName = $prefix . '_' . now()->format('YmdHis') . '_' . Str::random(10) . '.' . $file->extension();
        File::copy($file->getRealPath(), $targetDir . DIRECTORY_SEPARATOR . $fileName);

        return $dir . '/' . $fileName;
    }

    protected function deleteFile(?string $path): void
    {
        // Only touch files inside our own media folders
        if (! $path || str_contains($path, '..') || ! Str::startsWith($path, ['town_maps/', 'town_galleries/'])) {
            return;
        }

        if (File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }
}
