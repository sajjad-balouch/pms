<?php

namespace App\Livewire\Tools;

use App\Models\RegistryTaxEstimate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class RegistryTaxCalculator extends Component
{
    // Property Details
    public $city = 'Rahim Yar Khan';
    public $property_type = 'commercial'; // residential, commercial, agriculture
    public $area_unit = 'marla'; // marla, kanal, sqft
    public $area_size = 5;
    public $rate_per_unit = 1500000;
    public $is_within_tma = true;

    // Constructed & Commercial Parameters
    public $is_constructed = true;
    public $covered_area_sqft = 1125;
    public $structure_dc_rate_sqft = 1500;
    public $has_approved_map = false;

    // Dynamic Multi-Buyers (خریداران / مشتریان)
    public $buyers = [
        ['name' => 'خریدار نمبر 1', 'share' => 100, 'is_filer' => true],
    ];

    // Dynamic Multi-Sellers (فروخت کنندگان / بائعان)
    public $sellers = [
        ['name' => 'فروخت کنندہ 1', 'share' => 100, 'is_filer' => true],
    ];

    // Valuations
    public $land_value = 0;
    public $structure_value = 0;
    public $total_declared_value = 0;

    // Taxes Breakdown
    public $stamp_duty = 0;
    public $map_approval_fee = 0;
    public $tma_tax = 0;
    public $plra_fee = 0;
    public $sub_registrar_fee = 0;
    public $mutation_comparative_fee = 700;

    // Person-by-Person Breakdown
    public $buyer_tax_details = [];
    public $seller_tax_details = [];
    public $fbr_buyer_tax_total = 0;
    public $fbr_seller_tax_total = 0;
    public $grand_total_taxes = 0;

    public function mount()
    {
        $this->calculateTaxes();
    }

    public function updated($propertyName)
    {
        $this->calculateTaxes();
    }

    // Dynamic Add/Remove Buyers
    public function addBuyer()
    {
        $count = count($this->buyers) + 1;
        $this->buyers[] = ['name' => "خریدار نمبر {$count}", 'share' => 0, 'is_filer' => true];
        $this->calculateTaxes();
    }

    public function removeBuyer($index)
    {
        if (count($this->buyers) > 1) {
            unset($this->buyers[$index]);$this->buyers = array_values($this->buyers);$this->calculateTaxes();
        }
    }

    // Dynamic Add/Remove Sellers
    public function addSeller()
    {
        $count = count($this->sellers) + 1;
        $this->sellers[] = ['name' => "فروخت کنندہ {$count}", 'share' => 0, 'is_filer' => true];
        $this->calculateTaxes();
    }

    public function removeSeller($index)
    {
        if (count($this->sellers) > 1) {
            unset($this->sellers[$index]);$this->sellers = array_values($this->sellers);$this->calculateTaxes();
        }
    }

    public function calculateTaxes()
    {
        // 1. Land Value Calculation
        $size = max(0.01, (float) $this->area_size);$rate = max(1000, (float) $this->rate_per_unit);$this->land_value = round($size * $rate);

        // 2. Constructed Area Value & 2% Map Fee
        if ($this->property_type === 'commercial' && $this->is_constructed) {$covArea = max(0, (float) $this->covered_area_sqft);$dcRate = max(0, (float) $this->structure_dc_rate_sqft);$this->structure_value = round($covArea * $dcRate);

            if (!$this->has_approved_map) {
                $this->map_approval_fee = round($this->structure_value * 0.02);
            } else {
                $this->map_approval_fee = 0; } } else {$this->structure_value = 0;
            $this->map_approval_fee = 0;
        }

        // Total Declared Value
        $this->total_declared_value = $this->land_value +$this->structure_value;

        // 3. Uniform Provincial Charges
        $this->stamp_duty = round($this->total_declared_value * 0.01);$this->tma_tax = $this->is_within_tma ? round($this->total_declared_value * 0.01) : 0;
        
        $this->plra_fee = ($this->total_declared_value <= 3000000) ? 3300 : round($this->total_declared_value * 0.001);
        $this->sub_registrar_fee = ($this->total_declared_value <= 500000) ? 500 : 1000;

        // 4. Person-by-Person Buyer FBR Withholding (Section 236-K)
        $this->buyer_tax_details = [];$this->fbr_buyer_tax_total = 0;

        foreach ($this->buyers as$index => $buyer) {$sharePercentage = max(0, (float) ($buyer['share'] ?? 0));$personValuation = round($this->total_declared_value * ($sharePercentage / 100));

            if ($buyer['is_filer']) {
                $rateStr = '1.25\%';$rateDec = 0.0125;
            } else {
                if ($this->total_declared_value <= 50000000) {$rateStr = '10.5%';
                    $rateDec = 0.105; } elseif ($this->total_declared_value <= 100000000) {
                    $rateStr = '14.5\%';$rateDec = 0.145;
                } else {
                    $rateStr = '18.5\%';$rateDec = 0.185;
                }
            }

            $personTax = round($personValuation * $rateDec);
            $this->fbr_buyer_tax_total +=$personTax;

            $this->buyer_tax_details[] = [
                'name' => $buyer['name'] ?: "خریدار #" . ($index + 1), 'share' =>$sharePercentage,
                'valuation' => $personValuation,
                'is_filer' => (bool)$buyer['is_filer'],
                'rate' => $rateStr,
                'tax' => $personTax,
            ];
        }

        // 5. Person-by-Person Seller FBR Withholding (Section 236-C)
        $this->seller_tax_details = [];$this->fbr_seller_tax_total = 0;

        foreach ($this->sellers as$index => $seller) {$sharePercentage = max(0, (float) ($seller['share'] ?? 0));$personValuation = round($this->total_declared_value * ($sharePercentage / 100));

            if ($seller['is_filer']) {
                $rateStr = '2.75\%';$rateDec = 0.0275;
            } else {
                $rateStr = '11.5\%';$rateDec = 0.115;
            }

            $personTax = round($personValuation * $rateDec);
            $this->fbr_seller_tax_total +=$personTax;

            $this->seller_tax_details[] = [
                'name' => $seller['name'] ?: "فروخت کنندہ #" . ($index + 1), 'share' =>$sharePercentage,
                'valuation' => $personValuation,
                'is_filer' => (bool)$seller['is_filer'],
                'rate' => $rateStr,
                'tax' => $personTax,
            ];
        }

        // 6. Grand Total All Taxes
        $this->grand_total_taxes =$this->stamp_duty 
            + $this->tma_tax 
            + $this->plra_fee 
            + $this->sub_registrar_fee 
            + $this->mutation_comparative_fee 
            + $this->map_approval_fee 
            + $this->fbr_buyer_tax_total 
            + $this->fbr_seller_tax_total;
    }

    public function render()
    {
        return view('livewire.tools.registry-tax-calculator')
            ->layout('layouts.front-app', ['title' => 'پنجاب رجسٹری و FBR ٹیکس کیلکولیٹر (ملٹی فائلر سپورٹ) | AssanZameen']);
    }
}