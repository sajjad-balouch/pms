<?php

namespace App\Livewire\Agent;

use Livewire\Component;

class RegistryTaxCalculator extends Component
{
    // Property Valuation & Type
    public $propertyValue = 5000000; // DC Rate / Declared Value in PKR
    public $propertyType = 'urban_residential'; // 'urban_residential', 'rural_zari', 'mukhtarnama_blood', 'mukhtarnama_general'
    public $isMapUnapproved = false; // Agar naqsha pass nahi hai to Stamp Duty 2% lagay gi
    public $hasTmaTax = true; // TMA Tax (1%)

    // Filer Status
    public $buyerFilerStatus = 'filer'; // 'filer', 'non_filer'
    public $sellerFilerStatus = 'filer'; // 'filer', 'non_filer'

    // Extra / Custom Expenses (User Input)
    public $extraExpensesName = 'Kachehri / Vakeel / Stamp Vendor Charges';
    public $extraExpensesAmount = 0;

    // Calculated Fee Outputs
    public $stampDuty = 0;
    public $stampDutyPercent = 1.0;
    public $tmaTax = 0;
    public $buyerFbrTax = 0;
    public $buyerFbrPercent = 1.25;
    public $sellerFbrTax = 0;
    public $sellerFbrPercent = 2.75;
    public $plraCharges = 0;
    public $registrationCharges = 0;
    public $muqablaFee = 100;
    public $inteqalFee = 600;

    // Totals
    public $totalBuyerExpenses = 0;
    public $totalSellerExpenses = 0;
    public $grandTotalDealExpenses = 0;

    public function mount()
    {
        $this->recalculateTaxes();
    }

    public function updated($propertyName)
    {
        $this->recalculateTaxes();
    }

    public function recalculateTaxes()
    {
        $val = floatval($this->propertyValue);
        if ($val <= 0) {
            $this->resetCalculations();
            return;
        }

        // 1. Stamp Duty Calculation
        if ($this->propertyType === 'mukhtarnama_blood') {
            $this->stampDuty = 3025; // Flat Fee
            $this->stampDutyPercent = 0;
        } elseif ($this->propertyType === 'mukhtarnama_general') {
            $this->stampDuty = round(($val * 2) / 100); // 2% on Valuation Table
            $this->stampDutyPercent = 2.0;
        } elseif ($this->isMapUnapproved) {
            // Makaan/Dukaan ka naqsha pass na hone par 2% Stamp Duty
            $this->stampDutyPercent = 3.0;
            $this->stampDuty = round(($val * 3) / 100);
        } else {
            // Urban Residential ya Rural Zari: 1%
            $this->stampDutyPercent = 1.0;
            $this->stampDuty = round(($val * 1) / 100);
        }

        // 2. TMA Tax (1%)
        if ($this->hasTmaTax && !in_array($this->propertyType, ['mukhtarnama_blood', 'mukhtarnama_general'])) {
            $this->tmaTax = round(($val * 1) / 100);
        } else {
            $this->tmaTax = 0;
        }

        // 3. FBR Section 236-K (Buyer Tax)
        if ($this->buyerFilerStatus === 'filer') {
            $this->buyerFbrPercent = 1.25;
            $this->buyerFbrTax = round(($val * 1.25) / 100);
        } else {
            if ($val <= 50000000) {
                // Upto 50M (5 Crore) -> 10.5%
                $this->buyerFbrPercent = 10.5;
            } elseif ($val <= 100000000) {
                // 5 Crore se 10 Crore -> 14.5%
                $this->buyerFbrPercent = 14.5;
            } else {
                // 10 Crore se Zaid -> 18.5%
                $this->buyerFbrPercent = 18.5;
            }
            $this->buyerFbrTax = round(($val * $this->buyerFbrPercent) / 100);
        }

        // 4. FBR Section 236-C (Seller Tax)
        if ($this->sellerFilerStatus === 'filer') {
            $this->sellerFbrPercent = 2.75;
            $this->sellerFbrTax = round(($val * 2.75) / 100);
        } else {
            $this->sellerFbrPercent = 11.5;
            $this->sellerFbrTax = round(($val * 11.5) / 100);
        }

        // 5. PLRA Charges
        // 30 Lakh tak: Rs. 3,300, 30 Lakh se zaid par: 0.1%
        if ($val <= 3000000) {
            $this->plraCharges = 3300;
        } else {
            $this->plraCharges = round(($val * 0.1) / 100);
        }

        // 6. Registration Charges
        // 5 Lakh tak: Rs. 500, 5 Lakh se zaid par: Rs. 1,000
        if ($val <= 500000) {
            $this->registrationCharges = 500;
        } else {
            $this->registrationCharges = 1000;
        }

        // 7. Muqabla & Inteqal Fees
        $this->muqablaFee = 100;
        $this->inteqalFee = 600;

        // Extra Custom Expenses
        $customExtra = max(0, floatval($this->extraExpensesAmount));

        // Total Buyer Expenses
        $this->totalBuyerExpenses = $this->stampDuty 
                                  + $this->tmaTax 
                                  + $this->buyerFbrTax 
                                  + $this->plraCharges 
                                  + $this->registrationCharges 
                                  + $this->muqablaFee 
                                  + $this->inteqalFee
                                  + $customExtra;

        // Total Seller Expenses (FBR 236-C)
        $this->totalSellerExpenses = $this->sellerFbrTax;

        // Combined Grand Total (Buyer + Seller)
        $this->grandTotalDealExpenses = $this->totalBuyerExpenses + $this->totalSellerExpenses;
    }

    private function resetCalculations()
    {
        $this->stampDuty = $this->tmaTax = $this->buyerFbrTax = $this->sellerFbrTax = $this->plraCharges = $this->registrationCharges = $this->totalBuyerExpenses = $this->totalSellerExpenses = $this->grandTotalDealExpenses = 0;
    }

    public function render()
    {
        return view('livewire.agent.registry-tax-calculator')->layout('layouts.app');
    }
}