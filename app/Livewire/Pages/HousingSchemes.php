<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Town;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class HousingSchemes extends Component
{
    use WithPagination;

    public $search = '';

    public function boot()
    {
        $locale = Session::get('locale', config('app.locale', 'ur'));
        App::setLocale($locale);
    }

    public function mount()
    {
        $locale = Session::get('locale', config('app.locale', 'ur'));
        App::setLocale($locale);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // View render hone se theek pehle dobara locale confirm karein
        $locale = Session::get('locale', config('app.locale', 'ur'));
        App::setLocale($locale);

        $schemes = Town::withCount(['plots as available_plots_count' => function ($query) {
                $query->where('status', 'available');
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