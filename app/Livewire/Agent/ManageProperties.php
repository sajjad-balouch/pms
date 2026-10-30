<?php

namespace App\Livewire\Agent;

use App\Models\City;
use App\Models\Property;
use App\Models\Town;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ManageProperties extends Component
{
    use WithPagination, WithFileUploads;

    const MAX_IMAGES = 15;
    const STALE_DAYS = 30; // itne din se update na hui listing "purani" dikhegi

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

    // Dynamic City Selection Fields
    public $city_id; 
    public $city_search = '';
    public $selected_city_name = '';

    public $location;
    public $google_map_url;
    public $description;
    public $status = 'available';
    public $new_images = [];
    public $existing_images = [];

    public $isModalOpen = false;

    // WhatsApp Follow-Up Modal States
    public $isFollowUpModalOpen = false;
    public $selectedPropertyForFollowup = null;
    public $client_name = '';
    public $client_phone = '';
    public $followup_type = 'site_visit';
    public $custom_note = '';

    protected $rules = [
        'title' => 'required|string|max:255',
        'property_type' => 'required|in:residential,commercial,agricultural,shop,house,apartment,plot',
        'purpose' => 'required|in:for_sale,for_rent',
        'price' => 'required|numeric|min:0',
        'area_size' => 'required|string|max:50',
        'city_id' => 'required|exists:cities,id',
        'location' => 'required|string|max:255',
        'google_map_url' => 'nullable|url|max:500',
        'description' => 'nullable|string',
        'status' => 'required|in:available,under_offer,sold,rented',
        'new_images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ];

    // ==========================================
    // WHATSAPP ADVANCED MARKETING SUITE
    // ==========================================

    public function shareListingOnWhatsApp($propertyId)
    {
        $property = $this->ownedQuery()->with(['city', 'town'])->findOrFail($propertyId);
        $agentName = Auth::user()->name;
        $cityName = $property->city?->name ?? $property->town?->city ?? 'Pakistan';
        $propertyUrl = route('property.details', $property->id); // was /property/{id}, which returned 404
        $mapUrl = !empty($property->google_map_url) 
            ? $property->google_map_url 
            : 'https://www.google.com/maps/search/?api=1&query=' . urlencode($property->location . ', ' . $cityName);

        $text = "🏢 *{$property->title}*\n\n"
              . "🔹 *" . __('Type') . ":* " . __(ucfirst($property->property_type)) . " (" . __(ucwords(str_replace('_', ' ', $property->purpose))) . ")\n"
              . "🔹 *" . __('Area') . ":* {$property->area_size}\n"
              . "🔹 *" . __('Demand Price') . ":* PKR " . number_format($property->price) . "\n"
              . "📍 *" . __('Location') . ":* {$property->location}, {$cityName}\n\n"
              . "🗺️ *" . __('Google Maps Location') . ":*\n{$mapUrl}\n\n"
              . "📸 *" . __('Photos & Full Details') . ":*\n{$propertyUrl}\n\n"
              . "👤 *" . __('Contact for Deal') . ":* {$agentName}\n"
              . "⚡ " . __('Offered via AssanZameen.com');

        $encoded = urlencode($text);
        $this->dispatch('trigger-whatsapp', 
            appUrl: "whatsapp://send?text={$encoded}", 
            webUrl: "https://web.whatsapp.com/send?text={$encoded}"
        );
    }

    public function sendLocationPin($propertyId)
    {
        $property = $this->ownedQuery()->with(['city', 'town'])->findOrFail($propertyId);
        $cityName = $property->city?->name ?? $property->town?->city ?? '';

        $mapUrl = !empty($property->google_map_url)
            ? $property->google_map_url
            : 'https://www.google.com/maps/search/?api=1&query=' . urlencode($property->location . ', ' . $cityName);

        $text = "📍 *" . __('Property Location (Google Maps Pin)') . "*\n\n"
              . "🏢 *{$property->title}*\n"
              . "📐 " . __('Area') . ": {$property->area_size}\n"
              . "📌 " . __('Address') . ": {$property->location}" . ($cityName ? ", {$cityName}" : '') . "\n\n"
              . __('Tap the link below to reach the site:') . "\n"
              . "🗺️ {$mapUrl}\n\n"
              . __('Contact') . ": " . Auth::user()->name;

        $encoded = urlencode($text);
        $this->dispatch('trigger-whatsapp', 
            appUrl: "whatsapp://send?text={$encoded}", 
            webUrl: "https://web.whatsapp.com/send?text={$encoded}"
        );
    }

    public function openFollowUpModal($propertyId)
    {
        $this->selectedPropertyForFollowup = $this->ownedQuery()->with(['city', 'town'])->findOrFail($propertyId);
        $this->client_name = '';
        $this->client_phone = '';
        $this->followup_type = 'site_visit';
        $this->custom_note = '';
        $this->isFollowUpModalOpen = true;
    }

    public function sendFollowUp()
    {
        $this->validate([
            'client_phone' => 'required|min:10',
            'client_name' => 'required|string|max:50',
        ]);

        $prop = $this->selectedPropertyForFollowup;
        abort_unless($prop && (int) $prop->agent_id === (int) Auth::id(), 403);
        $agentName = Auth::user()->name;
        $cityName = $prop->city?->name ?? $prop->town?->city ?? '';

        // Message language follows the panel language (English default, Urdu when switched)
        $vars = [
            'client' => $this->client_name,
            'title' => $prop->title,
            'area' => $prop->area_size,
            'location' => trim($prop->location . ($cityName ? ', ' . $cityName : '')),
            'price' => number_format($prop->price),
            'agent' => $agentName,
        ];

        $msg = match ($this->followup_type) {
            'token_reminder' => __("Assalam o Alaikum :client!\n\nWe spoke to the owner about your offer on *:title*.\nA few more buyers are also very interested in this property. If you want to finalise the deal, please contact us today for the token advance.\n\nDemand: PKR :price\nContact: :agent", $vars),
            'price_offer' => __("Assalam o Alaikum :client!\n\nThe owner of *:title* is ready to meet to discuss the price.\nCan you visit the office today so we can close the deal?\n\nLocation: :location\nContact: :agent", $vars),
            default => __("Assalam o Alaikum :client!\n\nHope you are well. You showed interest in:\n🏢 *:title* (:area)\n📍 Location: :location\n\nShall we schedule a site visit this evening or tomorrow so you can see it yourself?\n\nContact: :agent\nAssanZameen.com", $vars),
        };

        if (!empty($this->custom_note)) {
            $msg .= "\n\n📌 *" . __('Note') . ":* " . $this->custom_note;
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $this->client_phone);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '92' . substr($cleanPhone, 1);
        }

        $encoded = urlencode($msg);
        $this->isFollowUpModalOpen = false;
        
        $this->dispatch('trigger-whatsapp', 
            appUrl: "whatsapp://send?phone={$cleanPhone}&text={$encoded}", 
            webUrl: "https://web.whatsapp.com/send?phone={$cleanPhone}&text={$encoded}"
        );
    }

    // ==========================================
    // CRUD OPERATIONS
    // ==========================================

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
        
        $this->city_id = null;
        $this->city_search = '';
        $this->selected_city_name = '';

        $this->location = '';
        $this->google_map_url = '';
        $this->description = '';
        $this->status = 'available';
        $this->new_images = [];
        $this->existing_images = [];
        $this->resetValidation();
    }

    public function selectCity($id, $name)
    {
        $this->city_id = $id;
        $this->selected_city_name = $name;
        $this->city_search = '';
        $this->town_id = null; 
    }

    public function save()
    {
        $this->validate();

        // Edit sirf apni listing ki — pehle property_id badal kar kisi aur ki listing apne naam ho sakti thi
        $property = $this->property_id ? $this->ownedQuery()->findOrFail($this->property_id) : new Property();

        // Existing images always from the DB, never from the browser
        $imagePaths = is_array($property->images) ? $property->images : [];

        if (count($imagePaths) + count($this->new_images) > self::MAX_IMAGES) {
            $this->addError('new_images', __('A listing can have at most :max photos.', ['max' => self::MAX_IMAGES]));
            return;
        }

        if (! empty($this->new_images)) {
            $destinationPath = public_path('property_images');
            File::ensureDirectoryExists($destinationPath, 0755);

            foreach ($this->new_images as $image) {
                // Extension from the real MIME type, never from the user's file name (blocks shell.php uploads)
                $imageName = time() . '_' . Str::random(12) . '.' . $image->extension();
                File::copy($image->getRealPath(), $destinationPath . DIRECTORY_SEPARATOR . $imageName);
                $imagePaths[] = 'property_images/' . $imageName;
            }
        }

        $property->fill(
            [
                'agent_id' => Auth::id(),
                'city_id' => $this->city_id,
                'town_id' => $this->town_id ?: null,
                'title' => $this->title,
                'property_type' => $this->property_type,
                'purpose' => $this->purpose,
                'price' => $this->price,
                'area_size' => $this->area_size,
                'location' => $this->location,
                'google_map_url' => $this->google_map_url,
                'description' => $this->description,
                'images' => $imagePaths,
                'status' => $this->status,
            ]
        )->save();

        session()->flash('message', $this->property_id ? __('Property updated successfully.') : __('New property listed successfully.'));
        $this->isModalOpen = false;
        $this->resetForm();
    }

    public function edit($id)
    {
        $property = Property::where('agent_id', Auth::id())->with(['city', 'town'])->findOrFail($id);

        $this->property_id = $property->id;
        $this->city_id = $property->city_id;
        $this->selected_city_name = $property->city?->name ?? $property->town?->city ?? '';
        $this->town_id = $property->town_id;
        $this->title = $property->title;
        $this->property_type = $property->property_type;
        $this->purpose = $property->purpose;
        $this->price = $property->price;
        $this->area_size = $property->area_size;
        $this->location = $property->location;
        $this->google_map_url = $property->google_map_url;
        $this->description = $property->description;
        $this->status = $property->status;
        $this->existing_images = is_array($property->images) ? $property->images : [];
        $this->new_images = [];

        $this->isModalOpen = true;
    }

    public function deleteImage($index)
    {
        if (! $this->property_id) {
            return;
        }

        // Image list from the DB — pehle browser ki bheji list se koi bhi file (../.env) mitwai ja sakti thi
        $property = $this->ownedQuery()->findOrFail($this->property_id);
        $images = is_array($property->images) ? $property->images : [];

        if (isset($images[$index])) {
            $this->deleteImageFile($images[$index]);
            unset($images[$index]);
            $images = array_values($images);

            $property->update(['images' => $images]);
            $this->existing_images = $images;
        }
    }

    // Listing ko "fresh" karna — list mein upar aati hai aur purani hone ka alert hat jata hai
    public function refreshListing($id)
    {
        $this->ownedQuery()->findOrFail($id)->touch();
        session()->flash('message', __('Listing refreshed.'));
    }

    public function delete($id)
    {
        $property = $this->ownedQuery()->findOrFail($id);

        foreach ((array) $property->images as $imgPath) {
            $this->deleteImageFile($imgPath);
        }

        $property->delete();
        session()->flash('message', __('Property deleted.'));
    }

    protected function ownedQuery()
    {
        return Property::where('agent_id', Auth::id());
    }

    protected function deleteImageFile($path): void
    {
        // Only files inside our own folder
        if (! is_string($path) || str_contains($path, '..') || ! Str::startsWith($path, 'property_images/')) {
            return;
        }

        if (File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }

    public function render()
    {
        // Listing analytics: views (properties.views_count), paid contact unlocks, and leads from the listing
        $query = $this->ownedQuery()
            ->with(['town', 'city'])
            ->addSelect(['properties.*'])
            ->selectSub(DB::table('property_unlocks')->selectRaw('COUNT(*)')->whereColumn('property_unlocks.property_id', 'properties.id'), 'unlocks_count')
            ->selectSub(DB::table('leads')->selectRaw('COUNT(*)')->whereColumn('leads.property_id', 'properties.id'), 'leads_count');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('location', 'like', '%' . $this->search . '%')
                  ->orWhereHas('city', function ($c) {
                      $c->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->typeFilter) {
            $query->where('property_type', $this->typeFilter);
        }

        if ($this->purposeFilter) {
            $query->where('purpose', $this->purposeFilter);
        }

        $citiesQuery = City::query()->where('is_active', 1);
        if ($this->city_search) {
            $citiesQuery->where('name', 'like', '%' . $this->city_search . '%');
        }

        $towns = $this->city_id 
            ? Town::where([['city_id', $this->city_id], ['is_active', 1]])->get() 
            : collect();

        return view('livewire.agent.manage-properties', [
            'properties' => $query->latest('updated_at')->paginate(10),
            'staleDays' => self::STALE_DAYS,
            'cities' => $citiesQuery->orderBy('name')->take(20)->get(),
            'towns' => $towns,
        ])->layout('layouts.app');
    }
}