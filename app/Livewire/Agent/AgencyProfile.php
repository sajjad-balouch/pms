<?php

namespace App\Livewire\Agent;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class AgencyProfile extends Component
{
    use WithFileUploads;

    public $name;
    public $username;
    public $agency_name;
    public $agency_tagline;
    public $phone;
    public $city_name;
    public $office_address;
    public $google_map_link;
    public $about_agency;
    public $new_logo;
    public $existing_logo;

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->username = $user->id;
        $this->agency_name = $user->agency_name ?: $user->name . ' Real Estate';
        $this->agency_tagline = $user->agency_tagline ?: __('Your trusted real estate partner');
        $this->phone = $user->phone;
        $this->city_name = $user->city_name;
        $this->office_address = $user->office_address;
        $this->google_map_link = $user->google_map_link;
        $this->about_agency = $user->about_agency;
        $this->existing_logo = $user->agency_logo;
    }

    public function saveProfile()
    {
        $user = Auth::user();

        $this->validate([
            'username' => 'required|alpha_dash|unique:users,username,' . $user->id,
            'agency_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'city_name' => 'nullable|string|max:50',
            'new_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Current logo from the DB, not from the browser
        $logoPath = $user->agency_logo;

        if ($this->new_logo) {
            $destination = public_path('agency_logos');
            if (!File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }

            // Extension from the real MIME type, never from the user's file name (blocks shell.php uploads)
            $filename = time() . '_' . uniqid() . '.' . $this->new_logo->extension();
            copy($this->new_logo->getRealPath(), $destination . DIRECTORY_SEPARATOR . $filename);
            $logoPath = 'agency_logos/' . $filename;
        }

        $user->update([
            'username' => strtolower($this->username),
            'agency_name' => $this->agency_name,
            'agency_tagline' => $this->agency_tagline,
            'phone' => $this->phone,
            'city_name' => $this->city_name,
            'office_address' => $this->office_address,
            'google_map_link' => $this->google_map_link,
            'about_agency' => $this->about_agency,
            'agency_logo' => $logoPath,
        ]);

        session()->flash('message', __('Your digital card and profile have been saved!'));
        $this->existing_logo = $logoPath;
        $this->new_logo = null;
    }

    public function render()
    {
        return view('livewire.agent.agency-profile')->layout('layouts.app');
    }
}