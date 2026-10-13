<?php

namespace App\Livewire\Agent;

use Livewire\Component;

class LandCalculator extends Component
{
    // Land Conversion Settings
    public $marlaStandard = 225; // 225 (Society Standard) ya 272.25 (Revenue/Patwari Standard)
    public $inputUnit = 'marla';
    public $inputValue = 5;

    // Converted Values
    public $sqFt = 0;
    public $sqYards = 0;
    public $karam = 0;
    public $sarsahi = 0;
    public $marla = 0;
    public $kanal = 0;
    public $murabba = 0;

    // Installment Calculator
    public $totalPrice = 5000000;
    public $downPaymentPercent = 20;
    public $durationYears = 3;
    public $installmentFrequency = 3; // Har 3 mah baad (Quarterly)
    
    // Installment Results
    public $downPaymentAmount = 0;
    public $remainingBalance = 0;
    public $totalInstallments = 0;
    public $installmentAmount = 0;

    public function mount()
    {
        $this->calculateLand();
        $this->calculateInstallments();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['marlaStandard', 'inputUnit', 'inputValue'])) {
            $this->calculateLand();
        }

        if (in_array($propertyName, ['totalPrice', 'downPaymentPercent', 'durationYears', 'installmentFrequency'])) {
            $this->calculateInstallments();
        }
    }

    public function calculateLand()
    {
        $val = floatval($this->inputValue);
        if ($val <= 0) {
            $this->resetCalculatedUnits();
            return;
        }

        $standard = floatval($this->marlaStandard); // 225 ya 272.25
        $sqFtBase = 0;

        // Base unit: Square Feet
        switch ($this->inputUnit) {
            case 'sq_ft':
                $sqFtBase = $val;
                break;
            case 'sq_yards':
                $sqFtBase = $val * 9;
                break;
            case 'marla':
                $sqFtBase = $val * $standard;
                break;
            case 'kanal':
                $sqFtBase = $val * ($standard * 20);
                break;
            case 'murabba':
                $sqFtBase = $val * ($standard * 20 * 25);
                break;
            case 'sarsahi':
                $sqFtBase = $val * ($standard / 9);
                break;
            case 'karam':
                // 1 Karam = 5.5 ft -> 1 Sq Karam = 30.25 Sq Ft
                $sqFtBase = $val * 30.25;
                break;
        }

        $this->sqFt = round($sqFtBase, 2);
        $this->sqYards = round($sqFtBase / 9, 2);
        $this->marla = round($sqFtBase / $standard, 3);
        $this->kanal = round($sqFtBase / ($standard * 20), 4);
        $this->murabba = round($sqFtBase / ($standard * 20 * 25), 5);
        $this->sarsahi = round($sqFtBase / ($standard / 9), 2);
        $this->karam = round($sqFtBase / 30.25, 2);
    }

    private function resetCalculatedUnits()
    {
        $this->sqFt = $this->sqYards = $this->marla = $this->kanal = $this->murabba = $this->sarsahi = $this->karam = 0;
    }

    public function calculateInstallments()
    {
        $price = floatval($this->totalPrice);
        $dpPercent = floatval($this->downPaymentPercent);
        $years = floatval($this->durationYears);
        $freq = intval($this->installmentFrequency);

        if ($price <= 0 || $years <= 0 || $freq <= 0) return;

        $this->downPaymentAmount = round(($price * $dpPercent) / 100);
        $this->remainingBalance = $price - $this->downPaymentAmount;

        $totalMonths = $years * 12;
        $this->totalInstallments = ceil($totalMonths / $freq);

        if ($this->totalInstallments > 0) {
            $this->installmentAmount = round($this->remainingBalance / $this->totalInstallments);
        }
    }

    public function render()
    {
        return view('livewire.agent.land-calculator')->layout('layouts.app');
    }
}