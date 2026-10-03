<?php

namespace App\Livewire\Admin;

use App\Models\City;
use Livewire\Component;
use Livewire\WithPagination;

class CityManagement extends Component
{
    use WithPagination;

    // Component Properties
    public $search = '';
    public $provinceFilter = '';
    public $statusFilter = '';
    public $perPage = 10;

    // Form Modal Properties
    public $isModalOpen = false;
    public $isDeleteModalOpen = false;
    public $cityIdBeingEdited = null;
    public $cityIdBeingDeleted = null;

    // Form Inputs
    public $name = '';
    public $province = 'Punjab';
    public $is_active = true;
    public $is_featured = false;
    public $sort_order = 0;

    protected $queryString = [
        'search' => ['except' => ''],
        'provinceFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:cities,name,' . $this->cityIdBeingEdited,
            'province' => 'required|string|max:255',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'required|integer|min:0',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingProvinceFilter()
    {
        $this->resetPage();
    }

    public function createCity()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    public function editCity($id)
    {
        $this->resetForm();
        $city = City::findOrFail($id);
        
        $this->cityIdBeingEdited = $city->id;
        $this->name = $city->name;
        $this->province = $city->province;
        $this->is_active = $city->is_active;
        $this->is_featured = $city->is_featured;
        $this->sort_order = $city->sort_order;

        $this->isModalOpen = true;
    }

    public function saveCity()
    {
        $validatedData = $this->validate();

        City::updateOrCreate(
            ['id' => $this->cityIdBeingEdited],
            $validatedData
        );

        session()->flash('status', $this->cityIdBeingEdited ? 'City updated successfully.' : 'New city added successfully.');
        
        $this->closeModal();
    }

    public function toggleStatus($id)
    {
        $city = City::findOrFail($id);
        $city->is_active = !$city->is_active;
        $city->save();

        session()->flash('status', 'City status toggled successfully.');
    }

    public function toggleFeatured($id)
    {
        $city = City::findOrFail($id);
        $city->is_featured = !$city->is_featured;
        $city->save();

        session()->flash('status', 'City featured state updated.');
    }

    public function confirmDelete($id)
    {
        $this->cityIdBeingDeleted = $id;
        $this->isDeleteModalOpen = true;
    }

    public function deleteCity()
    {
        if ($this->cityIdBeingDeleted) {
            City::findOrFail($this->cityIdBeingDeleted)->delete();
            session()->flash('status', 'City deleted successfully.');
        }

        $this->isDeleteModalOpen = false;
        $this->cityIdBeingDeleted = null;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->cityIdBeingEdited = null;
        $this->name = '';
        $this->province = 'Punjab';
        $this->is_active = true;
        $this->is_featured = false;
        $this->sort_order = 0;
        $this->resetValidation();
    }

    public function render()
    {
        $query = City::query();

        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . trim($this->search) . '%');
        }

        if (!empty($this->provinceFilter)) {
            $query->where('province', $this->provinceFilter);
        }

        if ($this->statusFilter !== '') {
            $query->where('is_active', $this->statusFilter == '1');
        }

        $cities = $query->orderBy('sort_order', 'asc')
                        ->orderBy('name', 'asc')
                        ->paginate($this->perPage);

        return view('livewire.admin.city-management', [
            'cities' => $cities,
            'provinces' => [
                'Islamabad Capital Territory',
                'Punjab',
                'Sindh',
                'Khyber Pakhtunkhwa',
                'Balochistan',
                'Azad Jammu & Kashmir',
                'Gilgit-Baltistan'
            ]
        ])->layout('layouts.admin-app'); // Adjust layout name if required
    }
}