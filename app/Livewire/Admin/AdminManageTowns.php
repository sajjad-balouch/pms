<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Town;
use Illuminate\Support\Str;

class AdminManageTowns extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $isEditMode = false;
    public $townIdBeingEdited = null;

    // Form fields
    public $name = '';
    public $city = '';
    public $location = '';
    public $total_plots = '';
    public $description = '';
    public $is_active = true;

    protected $paginationTheme = 'bootstrap';

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'location' => 'nullable|string|max:255',
            'total_plots' => 'nullable|integer|min:0',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetErrorBag();
        $this->reset(['name', 'city', 'location', 'total_plots', 'description', 'townIdBeingEdited']);
        $this->is_active = true;
        $this->isEditMode = false;
        $this->showModal = true;
    }

    public function openEditModal($townId)
    {
        $this->resetErrorBag();
        $town = Town::findOrFail($townId);

        $this->townIdBeingEdited = $town->id;
        $this->name = $town->name;
        $this->city = $town->city;
        $this->location = $town->location;
        $this->total_plots = $town->total_plots;
        $this->description = $town->description;
        $this->is_active = (bool) $town->is_active;

        $this->isEditMode = true;
        $this->showModal = true;
    }

    public function saveTown()
    {
        $this->validate();

        if ($this->isEditMode) {
            $town = Town::findOrFail($this->townIdBeingEdited);
            $town->update([
                'name' => $this->name,
                'slug' => Str::slug($this->name),
                'city' => $this->city,
                'location' => $this->location,
                'total_plots' => $this->total_plots,
                'description' => $this->description,
                'is_active' => $this->is_active,
            ]);

            session()->flash('success', 'Housing Scheme updated successfully!');
        } else {
            Town::create([
                'name' => $this->name,
                'slug' => Str::slug($this->name),
                'city' => $this->city,
                'location' => $this->location,
                'total_plots' => $this->total_plots,
                'description' => $this->description,
                'is_active' => $this->is_active,
            ]);

            session()->flash('success', 'New Housing Scheme added successfully!');
        }

        $this->showModal = false;
        $this->reset(['name', 'city', 'location', 'total_plots', 'description', 'townIdBeingEdited']);
    }

    public function deleteTown($townId)
    {
        $town = Town::findOrFail($townId);
        $town->delete();

        session()->flash('success', 'Housing Scheme deleted successfully.');
    }

    public function render()
    {
        $towns = Town::query()
            ->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('city', 'like', '%' . $this->search . '%')
                  ->orWhere('location', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.manage-towns', [
            'towns' => $towns,
        ])->layout('layouts.admin-app');
    }
}