<?php

namespace App\Livewire\TownOwner;

use App\Models\Plot;
use App\Models\Town;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ManagePlots extends Component
{
    use WithPagination;

    public $town;
    public $plot_number, $block_name, $type = 'residential', $size = '5 Marla';
    public $total_price, $down_payment, $total_installments = 36;

    public function mount($townId)
    {
        $this->town = Town::where('user_id', Auth::id())->findOrFail($townId);
    }

    protected $rules = [
        'plot_number' => 'required|string|max:50',
        'block_name' => 'nullable|string|max:100',
        'type' => 'required|in:residential,commercial',
        'size' => 'required|string|max:50',
        'total_price' => 'required|numeric|min:0',
        'down_payment' => 'required|numeric|min:0',
        'total_installments' => 'required|integer|min:1',
    ];

    public function addPlot()
    {
        $this->validate();

        Plot::create([
            'town_id' => $this->town->id,
            'plot_number' => $this->plot_number,
            'block_name' => $this->block_name,
            'type' => $this->type,
            'size' => $this->size,
            'total_price' => $this->total_price,
            'down_payment' => $this->down_payment,
            'total_installments' => $this->total_installments,
            'status' => 'available',
        ]);

        $this->reset(['plot_number', 'block_name', 'total_price', 'down_payment']);
        session()->flash('success', 'پلاٹ انوینٹری میں کامیابی سے شامل ہو گیا ہے۔');
    }

    public function updateStatus($plotId, $status)
    {
        $plot = Plot::where('town_id', $this->town->id)->findOrFail($plotId);
        $plot->update(['status' => $status]);
        session()->flash('success', 'پلاٹ کا اسٹیٹس تبدیل ہو گیا ہے۔');
    }

    public function render()
    {
        return view('livewire.town-owner.manage-plots', [
            'plots' => Plot::where('town_id', $this->town->id)->latest()->paginate(10),
        ])->layout('layouts.app');
    }
}