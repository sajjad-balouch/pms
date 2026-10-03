<?php

namespace App\Livewire\Admin;

use App\Models\Town; // Or Society model
use Livewire\Component;
use Livewire\WithPagination;

class TownSchemeManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';

    public function toggleBan($id)
    {
        $town = Town::findOrFail($id);
        $town->is_active = !$town->is_active;
        $town->save();

        session()->flash('status', "Town Scheme '{$town->name}' status updated.");
    }

    public function render()
    {
        $query = Town::with(['user', 'city'])->withCount('properties');

        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhereHas('user', function ($q) {
                      $q->where('name', 'like', '%' . $this->search . '%');
                  });
        }

        return view('livewire.admin.town-scheme-management', [
            'towns' => $query->latest()->paginate(10),
        ])->layout('layouts.admin-app');
    }
}