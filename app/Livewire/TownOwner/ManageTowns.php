<?php

namespace App\Livewire\TownOwner;

use App\Models\Town;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ManageTowns extends Component
{
    use WithPagination;

    public $name, $location, $city, $total_area, $noc_number, $google_map_url;
    public $editingTownId = null;

    protected $rules = [
        'name' => 'required|string|max:255',
        'location' => 'required|string|max:255',
        'city' => 'required|string|max:255',
        'total_area' => 'nullable|string|max:100',
        'noc_number' => 'nullable|string|max:100',
        'google_map_url' => 'nullable|url|max:500',
    ];

    public function createTown()
    {
        $this->validate();

        if ($this->editingTownId) {
            // Update Existing Town
            $town = Town::where('user_id', Auth::id())->findOrFail($this->editingTownId);
            $town->update([
                'name' => $this->name,
                'location' => $this->location,
                'city' => $this->city,
                'google_map_url' => $this->google_map_url,
                'total_area' => $this->total_area,
                'noc_number' => $this->noc_number,
            ]);

            session()->flash('success', 'ٹاؤن کی تفصیلات کامیابی سے اپ ڈیٹ ہو گئی ہیں۔');
        } else {
            // Create New Town
            Town::create([
                'user_id' => Auth::id(),
                'name' => $this->name,
                'location' => $this->location,
                'city' => $this->city,
                'google_map_url' => $this->google_map_url,
                'total_area' => $this->total_area,
                'noc_number' => $this->noc_number,
            ]);

            session()->flash('success', 'نیا ٹاؤن/ہاؤسنگ سکیم کامیابی سے رجسٹر ہو گئی ہے۔');
        }

        $this->cancelEdit();
    }

    public function editTown($id)
    {
        $town = Town::where('user_id', Auth::id())->findOrFail($id);

        $this->editingTownId = $town->id;
        $this->name = $town->name;
        $this->location = $town->location;
        $this->city = $town->city;
        $this->google_map_url = $town->google_map_url;
        $this->total_area = $town->total_area;
        $this->noc_number = $town->noc_number;
    }

    public function cancelEdit()
    {
        $this->editingTownId = null;
        $this->reset(['name', 'location', 'city', 'google_map_url', 'total_area', 'noc_number']);
        $this->resetValidation();
    }

    public function deleteTown($id)
    {
        $town = Town::where('user_id', Auth::id())->findOrFail($id);
        
        if ($this->editingTownId === $id) {
            $this->cancelEdit();
        }

        $town->delete();
        session()->flash('success', 'ٹاؤن ریکارڈ ختم کر دیا گیا ہے۔');
    }

    public function render()
    {
        return view('livewire.town-owner.manage-towns', [
            'towns' => Town::where('user_id', Auth::id())->latest()->paginate(10),
        ])->layout('layouts.app');
    }
}