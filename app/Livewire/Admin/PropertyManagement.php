<?php

namespace App\Livewire\Admin;

use App\Models\Property;
use Livewire\Component;
use Livewire\WithPagination;

class PropertyManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $typeFilter = '';
    public $statusFilter = '';
    public $selectedProperty = null;
    public $isDetailModalOpen = false;
    public $isBanModalOpen = false;
    public $banReason = '';
    public $propertyToBanId = null;

    protected $queryString = ['search' => ['except' => ''], 'statusFilter' => ['except' => '']];

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
        $property->is_active = $status; // active, pending, rejected, banned
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
            $query->where('status', $this->statusFilter);
        }

        return view('livewire.admin.property-management', [
            'properties' => $query->latest()->paginate(12),
        ])->layout('layouts.admin-app');
    }
}