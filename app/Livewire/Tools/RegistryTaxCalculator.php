<?php

namespace App\Livewire\Tools;

use App\Support\Tools\ToolCatalog;
use Livewire\Component;

class RegistryTaxCalculator extends Component
{
    private const MAX_PARTIES = 10;

    // Property Details
    public $city = 'Rahim Yar Khan';
    public $property_type = 'residential'; // residential, commercial, agriculture
    public $area_unit = 'marla'; // marla, kanal, sqft
    public $area_size = 5;
    public $rate_per_unit = 1500000;
    public $is_within_tma = true;
    public $stamp_duty_pct = 1.0;

    // Constructed Parameters
    public $is_constructed = false;
    public $covered_area_sqft = 1125;
    public $structure_dc_rate_sqft = 1500;
    public $has_approved_map = true;

    public $buyers = [];
    public $sellers = [];

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
    public $mutation_comparative_fee = 0;

    // Person-by-Person Breakdown
    public $buyer_tax_details = [];
    public $seller_tax_details = [];
    public $fbr_buyer_tax_total = 0;
    public $fbr_seller_tax_total = 0;
    public $grand_total_taxes = 0;
    public $buyer_share_total = 100;
    public $seller_share_total = 100;

    public function mount()
    {
        $this->stamp_duty_pct = config('tools.registry.stamp_duty_pct');
        $this->buyers = [['name' => '', 'share' => 100, 'is_filer' => true]];
        $this->sellers = [['name' => '', 'share' => 100, 'is_filer' => true]];
        $this->calculateTaxes();
    }

    public function updated($propertyName)
    {
        $this->calculateTaxes();
    }

    // Dynamic Add/Remove Buyers
    public function addBuyer()
    {
        if (count($this->buyers) < self::MAX_PARTIES) {
            $this->buyers[] = ['name' => '', 'share' => 0, 'is_filer' => true];
            $this->calculateTaxes();
        }
    }

    public function removeBuyer($index)
    {
        if (count($this->buyers) > 1) {
            unset($this->buyers[$index]);
            $this->buyers = array_values($this->buyers);
            $this->calculateTaxes();
        }
    }

    // Dynamic Add/Remove Sellers
    public function addSeller()
    {
        if (count($this->sellers) < self::MAX_PARTIES) {
            $this->sellers[] = ['name' => '', 'share' => 0, 'is_filer' => true];
            $this->calculateTaxes();
        }
    }

    public function removeSeller($index)
    {
        if (count($this->sellers) > 1) {
            unset($this->sellers[$index]);
            $this->sellers = array_values($this->sellers);
            $this->calculateTaxes();
        }
    }

    public function calculateTaxes()
    {
        $rates = config('tools.registry');

        // 1. Land Value Calculation
        $this->land_value = round(max(0, (float) $this->area_size) * max(0, (float) $this->rate_per_unit));

        // 2. Constructed Area Value & Map Fee (zarai ke ilawa har qisam)
        if ($this->property_type !== 'agriculture' && $this->is_constructed) {
            $this->structure_value = round(max(0, (float) $this->covered_area_sqft) * max(0, (float) $this->structure_dc_rate_sqft));
            $this->map_approval_fee = $this->has_approved_map ? 0 : round($this->structure_value * $rates['map_fee_pct'] / 100);
        } else {
            $this->structure_value = 0;
            $this->map_approval_fee = 0;
        }

        $this->total_declared_value = $this->land_value + $this->structure_value;
        $value = $this->total_declared_value;

        // 3. Provincial Charges
        $this->stamp_duty_pct = min(10, max(0, (float) $this->stamp_duty_pct));
        $this->stamp_duty = round($value * $this->stamp_duty_pct / 100);
        $this->tma_tax = $this->is_within_tma ? round($value * $rates['tma_tax_pct'] / 100) : 0;
        $this->plra_fee = $value <= $rates['plra_flat_limit'] ? $rates['plra_flat_fee'] : round($value * $rates['plra_pct'] / 100);
        $this->sub_registrar_fee = $value <= $rates['registrar_small_limit'] ? $rates['registrar_small_fee'] : $rates['registrar_fee'];
        $this->mutation_comparative_fee = $rates['mutation_fee'];

        // 4. Person-by-Person FBR Withholding
        [$this->buyer_tax_details, $this->fbr_buyer_tax_total, $this->buyer_share_total] = $this->partyTaxes($this->buyers, $rates['buyer_236k'], $value);
        [$this->seller_tax_details, $this->fbr_seller_tax_total, $this->seller_share_total] = $this->partyTaxes($this->sellers, $rates['seller_236c'], $value);

        // 5. Grand Total All Taxes
        $this->grand_total_taxes = $this->stamp_duty
            + $this->tma_tax
            + $this->plra_fee
            + $this->sub_registrar_fee
            + $this->mutation_comparative_fee
            + $this->map_approval_fee
            + $this->fbr_buyer_tax_total
            + $this->fbr_seller_tax_total;
    }

    private function partyTaxes(array $parties, array $schedule, float $value): array
    {
        $details = [];
        $total = 0;
        $shareTotal = 0;

        foreach ($parties as $index => $party) {
            $share = max(0, min(100, (float) ($party['share'] ?? 0)));
            $isFiler = (bool) ($party['is_filer'] ?? false);
            $personValue = round($value * $share / 100);
            $pct = $this->slabRate($schedule[$isFiler ? 'filer' : 'non_filer'], $value);
            $tax = round($personValue * $pct / 100);

            $details[] = [
                'name' => trim((string) ($party['name'] ?? '')),
                'number' => $index + 1,
                'share' => $share,
                'valuation' => $personValue,
                'is_filer' => $isFiler,
                'rate' => $pct . '%',
                'tax' => $tax,
            ];
            $total += $tax;
            $shareTotal += $share;
        }

        return [$details, $total, $shareTotal];
    }

    private function slabRate(array $slabs, float $value): float
    {
        foreach ($slabs as [$limit, $pct]) {
            if ($limit === null || $value <= $limit) {
                return $pct;
            }
        }

        return end($slabs)[1];
    }

    public function render()
    {
        return view('livewire.tools.registry-tax-calculator')
            ->layout('layouts.front-app', ToolCatalog::meta('tax'));
    }
}
