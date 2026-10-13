<?php

namespace App\Livewire\Agent;

use App\Models\City;
use App\Models\Property;
use App\Models\Town;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
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
        'new_images.*' => 'nullable|image|max:2048',
    ];

    // ==========================================
    // WHATSAPP ADVANCED MARKETING SUITE
    // ==========================================

    public function shareListingOnWhatsApp($propertyId)
    {
        $property = Property::with(['city', 'town'])->findOrFail($propertyId);
        $agentName = Auth::user()->name;
        $cityName = $property->city?->name ?? $property->town?->city?->name ?? 'Pakistan';
        $propertyUrl = url('/property/' . $property->id);
        $mapUrl = !empty($property->google_map_url) 
            ? $property->google_map_url 
            : 'https://www.google.com/maps/search/?api=1&query=' . urlencode($property->location . ', ' . $cityName);

        $text = "🏢 *{$property->title}*\n\n"
              . "🔹 *نوعیت:* " . ucfirst($property->property_type) . " (" . str_replace('_', ' ', $property->purpose) . ")\n"
              . "🔹 *رقبہ:* {$property->area_size}\n"
              . "🔹 *ڈیمانڈ قیمت:* PKR " . number_format($property->price) . "\n"
              . "📍 *لوکیشن:* {$property->location}، {$cityName}\n\n"
              . "🗺️ *گوگل میپ لوکیشن:*\n{$mapUrl}\n\n"
              . "📸 *تصاویر اور مکمل تفصیلات:*\n{$propertyUrl}\n\n"
              . "👤 *رابطہ برائے ڈیل:* {$agentName}\n"
              . "⚡ پیشکش بذریعہ AssanZameen.com";

        $encoded = urlencode($text);
        $this->dispatch('trigger-whatsapp', 
            appUrl: "whatsapp://send?text={$encoded}", 
            webUrl: "https://web.whatsapp.com/send?text={$encoded}"
        );
    }

    public function sendLocationPin($propertyId)
    {
        $property = Property::with(['city', 'town'])->findOrFail($propertyId);
        $cityName = $property->city?->name ?? $property->town?->city?->name ?? 'علاقہ';

        $mapUrl = !empty($property->google_map_url) 
            ? $property->google_map_url 
            : 'https://www.google.com/maps/search/?api=1&query=' . urlencode($property->location . ', ' . $cityName);

        $text = "📍 *پراپرٹی لوکیشن میپ (Google Maps Pin)*\n\n"
              . "🏢 *{$property->title}*\n"
              . "📐 رقبہ: {$property->area_size}\n"
              . "📌 پتہ: {$property->location}، {$cityName}\n\n"
              . "موقع پر پہنچنے کے لیے نیچے دیے گئے لنک پر کلک کریں:\n"
              . "🗺️ {$mapUrl}\n\n"
              . "رابطہ: " . Auth::user()->name;

        $encoded = urlencode($text);
        $this->dispatch('trigger-whatsapp', 
            appUrl: "whatsapp://send?text={$encoded}", 
            webUrl: "https://web.whatsapp.com/send?text={$encoded}"
        );
    }

    public function openFollowUpModal($propertyId)
    {
        $this->selectedPropertyForFollowup = Property::with(['city', 'town'])->findOrFail($propertyId);
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
        $agentName = Auth::user()->name;
        $cityName = $prop->city?->name ?? $prop->town?->city?->name ?? 'علاقہ';

        $msg = "";

        if ($this->followup_type === 'site_visit') {
            $msg = "السلام علیکم {$this->client_name} صاحب!\n\n"
                 . "امید ہے آپ خیریت سے ہوں گے۔ آپ نے پراپرٹی:\n"
                 . "🏢 *{$prop->title}* ({$prop->area_size})\n"
                 . "📍 لوکیشن: {$prop->location}، {$cityName}\n\n"
                 . "میں دلچسپی ظاہر کی تھی۔ کیا ہم آج شام یا کل کے لیے موقع وزٹ (Site Visit) شیڈول کر لیں تاکہ آپ خود آکر موقع دیکھ سکیں؟\n\n"
                 . "رابطہ: {$agentName}\nAssanZameen.com";
        } elseif ($this->followup_type === 'token_reminder') {
            $msg = "السلام علیکم {$this->client_name} صاحب!\n\n"
                 . "پراپرٹی *{$prop->title}* کے حوالے سے آپ کی آفر پر مالک سے بات ہوئی ہے۔\n"
                 . "چند مزید خریدار بھی اس پراپرٹی میں گہری دلچسپی لے رہے ہیں۔ اگر آپ ڈیل فائنل کرنا چاہتے ہیں تو برائے مہربانی ٹوکن ایڈوانس کے لیے آج رابطہ کریں۔\n\n"
                 . "ڈیمانڈ: PKR " . number_format($prop->price) . "\n"
                 . "رابطہ: {$agentName}";
        } elseif ($this->followup_type === 'price_offer') {
            $msg = "السلام علیکم {$this->client_name} صاحب!\n\n"
                 . "پراپرٹی *{$prop->title}* کی قیمت پر بات چیت کے لیے مالک (Owner) ملاقات پر آمادہ ہیں۔\n"
                 . "کیا آپ آج آفس تشریف لا سکتے ہیں تاکہ حتمی ڈیل کلوز کی جا سکے؟\n\n"
                 . "مقام: {$prop->location}\n"
                 . "رابطہ: {$agentName}";
        }

        if (!empty($this->custom_note)) {
            $msg .= "\n\n📌 *اضافی نوٹ:* " . $this->custom_note;
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

        $imagePaths = is_array($this->existing_images) ? $this->existing_images : [];

        if (!empty($this->new_images)) {
            $destinationPath = public_path('property_images');

            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            foreach ($this->new_images as $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $targetFile = $destinationPath . DIRECTORY_SEPARATOR . $imageName;

                copy($image->getRealPath(), $targetFile);
                $imagePaths[] = 'property_images/' . $imageName;
            }
        }

        Property::updateOrCreate(
            ['id' => $this->property_id],
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
        );

        session()->flash('message', $this->property_id ? 'پراپرٹی کامیابی کے ساتھ اپ ڈیٹ ہو گئی۔' : 'نئی پراپرٹی کامیابی سے لسٹ ہو گئی۔');
        $this->isModalOpen = false;
        $this->resetForm();
    }

    public function edit($id)
    {
        $property = Property::where('agent_id', Auth::id())->with(['city', 'town'])->findOrFail($id);

        $this->property_id = $property->id;
        $this->city_id = $property->city_id;
        $this->selected_city_name = $property->city ?? $property->town?->city ?? '';
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
        if (isset($this->existing_images[$index])) {
            $imageToDelete = $this->existing_images[$index];
            $fullPath = public_path($imageToDelete);

            if (File::exists($fullPath)) {
                File::delete($fullPath);
            }

            unset($this->existing_images[$index]);
            $this->existing_images = array_values($this->existing_images);

            if ($this->property_id) {
                Property::where('id', $this->property_id)->update([
                    'images' => $this->existing_images
                ]);
            }
        }
    }

    public function delete($id)
    {
        $property = Property::where('agent_id', Auth::id())->findOrFail($id);
        
        if (!empty($property->images) && is_array($property->images)) {
            foreach ($property->images as $imgPath) {
                $file = public_path($imgPath);
                if (File::exists($file)) {
                    File::delete($file);
                }
            }
        }

        $property->delete();
        session()->flash('message', 'پراپرٹی حذف کر دی گئی۔');
    }

    public function render()
    {
        $query = Property::where('agent_id', Auth::id())->with(['town', 'city']);

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
            'properties' => $query->latest()->paginate(10),
            'cities' => $citiesQuery->orderBy('name')->take(20)->get(),
            'towns' => $towns,
        ])->layout('layouts.app');
    }
}