<?php

namespace App\Livewire\Tools;

use App\Models\HousingSocietyVerification;
use Livewire\Component;
use Livewire\WithPagination;

class SocietyNocDirectory extends Component
{
    use WithPagination;

    public $search = '';
    public $filterCity = '';
    public $filterStatus = ''; // approved, under_process, illegal
    public $selectedSociety = null; // Detail Modal View

    protected $queryString = [
        'search' => ['except' => ''],
        'filterCity' => ['except' => ''],
        'filterStatus' => ['except' => ''],
    ];

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterCity() { $this->resetPage(); }
    public function updatingFilterStatus() { $this->resetPage(); }

    public function viewDetails($id)
    {
        $this->selectedSociety = HousingSocietyVerification::findOrFail($id);
    }

    public function closeDetails()
    {
        $this->selectedSociety = null;
    }

    public function render()
    {
        $query = HousingSocietyVerification::query();

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('noc_number', 'like', '%' . $this->search . '%')
                  ->orWhere('developer_name', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->filterCity)) {
            $query->where('city', $this->filterCity);
        }

        if (!empty($this->filterStatus)) {
            $query->where('status', $this->filterStatus);
        }

        $societies = $query->orderBy('is_featured', 'desc')
                           ->orderBy('status', 'asc')
                           ->paginate(12);

        $cities = HousingSocietyVerification::select('city')->distinct()->pluck('city');

        return view('livewire.tools.society-noc-directory', [
            'societies' => $societies,
            'cities' => $cities,
        ])->layout('layouts.front-app', ['title' => 'پنجاب ہاؤسنگ سوسائٹی NOC و قانونی اسٹیٹس ڈائریکٹری | AssanZameen']);
    }
}