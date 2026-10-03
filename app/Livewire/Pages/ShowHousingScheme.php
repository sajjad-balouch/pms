<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use App\Models\Town;
use Livewire\WithPagination;

class ShowHousingScheme extends Component
{
    use WithPagination;

    public $townId;
    public $searchPlot = '';
    public $selectedType = '';

    public function mount($id)
    {
        $this->townId = $id;
    }

    public function render()
    {
        $scheme = Town::withCount(['plots as available_plots_count' => function ($q) {
            $q->where('status', 'available');
        }])->findOrFail($this->townId);

        $plots = $scheme->plots()
            ->when($this->searchPlot, function ($q) {
                $q->where('plot_number', 'like', '%' . $this->searchPlot . '%');
            })
            ->when($this->selectedType, function ($q) {
                $q->where('type', $this->selectedType);
            })
            ->latest()
            ->paginate(12);

        return view('livewire.pages.show-housing-scheme', [
            'scheme' => $scheme,
            'plots' => $plots
        ])->layout('layouts.front-app');
    }
}