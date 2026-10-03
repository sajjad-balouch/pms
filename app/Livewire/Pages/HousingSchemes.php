<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Town;

class HousingSchemes extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $schemes = Town::withCount(['plots as available_plots_count' => function ($query) {
                $query->where('status', 'available'); // ya aapka status column constraint
            }])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('location', 'like', '%' . $this->search . '%')
                      ->orWhere('city', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(9);

        return view('livewire.pages.housing-schemes', [
            'schemes' => $schemes
        ])->layout('layouts.front-app');
    }
}