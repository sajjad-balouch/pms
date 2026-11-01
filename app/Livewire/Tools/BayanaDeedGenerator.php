<?php

namespace App\Livewire\Tools;

use App\Support\Tools\DeedHelper;
use App\Support\Tools\ToolCatalog;
use Livewire\Component;

class BayanaDeedGenerator extends Component
{
    public $leave_stamp_space = true; // e-Stamp paper ke liye upar 4.5 inch jagah chhorna

    // Seller Info
    public $seller_name = '';
    public $seller_father = '';
    public $seller_cnic = '';
    public $seller_phone = '';
    public $seller_address = '';

    // Buyer Info
    public $buyer_name = '';
    public $buyer_father = '';
    public $buyer_cnic = '';
    public $buyer_phone = '';
    public $buyer_address = '';

    // Property Details
    public $property_title = '';
    public $city = '';
    public $location_mouza = '';
    public $khasra_khewat = '';
    public $area_size = '';

    // Financials
    public $total_amount = 0;
    public $bayana_advance_amount = 0;
    public $remaining_amount = 0;
    public $payment_method = 'pay_order'; // cash, pay_order, cheque, online
    public $payment_reference = '';
    public $deed_date = '';
    public $target_registry_date = '';

    // Witnesses
    public $witness1_name = '';
    public $witness1_cnic = '';
    public $witness2_name = '';
    public $witness2_cnic = '';

    public function mount()
    {
        $this->deed_date = date('Y-m-d');
        $this->target_registry_date = date('Y-m-d', strtotime('+45 days'));
    }

    public function updated($propertyName)
    {
        $this->total_amount = max(0, (float) $this->total_amount);
        $this->bayana_advance_amount = max(0, (float) $this->bayana_advance_amount);
        $this->remaining_amount = max(0, $this->total_amount - $this->bayana_advance_amount);
    }

    /** Print se pehle dikhne wale masle. Khali ya ghalat deed print na ho. */
    private function issues(): array
    {
        $issues = DeedHelper::missing([
            'Seller name' => $this->seller_name, 'Seller father name' => $this->seller_father, 'Seller address' => $this->seller_address,
            'Buyer name' => $this->buyer_name, 'Buyer father name' => $this->buyer_father, 'Buyer address' => $this->buyer_address,
            'Property details' => $this->property_title, 'Location' => $this->location_mouza, 'City' => $this->city, 'Area' => $this->area_size,
            'Witness 1' => $this->witness1_name, 'Witness 2' => $this->witness2_name,
        ]);

        foreach (['Seller' => $this->seller_cnic, 'Buyer' => $this->buyer_cnic, 'Witness 1' => $this->witness1_cnic, 'Witness 2' => $this->witness2_cnic] as $who => $cnic) {
            if (! DeedHelper::validCnic($cnic)) {
                $issues[] = __(':who CNIC must have 13 digits', ['who' => __($who)]);
            }
        }

        if ($this->total_amount <= 0) {
            $issues[] = __('Enter the total price');
        }
        if ($this->bayana_advance_amount <= 0 || $this->bayana_advance_amount > $this->total_amount) {
            $issues[] = __('Advance must be more than zero and not above the total price');
        }
        if (! DeedHelper::date($this->deed_date) || ! DeedHelper::date($this->target_registry_date)) {
            $issues[] = __('Enter both dates');
        } elseif ($this->target_registry_date < $this->deed_date) {
            $issues[] = __('Registry date cannot be before the agreement date');
        }

        return $issues;
    }

    public function render()
    {
        return view('livewire.tools.bayana-deed-generator', ['issues' => $this->issues()])
            ->layout('layouts.front-app', ToolCatalog::meta('bayana'));
    }
}
