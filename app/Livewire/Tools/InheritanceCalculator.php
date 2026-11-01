<?php

namespace App\Livewire\Tools;

use App\Support\Tools\InheritanceEngine;
use App\Support\Tools\ToolCatalog;
use Livewire\Component;

class InheritanceCalculator extends Component
{
    // Deceased Details
    public $deceased_gender = 'male'; // 'male' or 'female'
    public $total_kanals = 4;
    public $total_marlas = 0;
    public $cash_estate = 5000000; // Kul Maal / Naqad
    public $debts_funeral = 200000; // Qarz aur Tajheez o Takfeen
    public $will_amount = 0; // Wasiyat: ghair-waris ke liye, zyada se zyada 1/3

    // Heirs Input
    public $wives_count = 1; // Agar mard fout hua
    public $has_husband = false; // Agar aurat fout hui
    public $has_father = true;
    public $has_mother = true;
    public $sons_count = 2;
    public $daughters_count = 3;
    public $brothers_count = 0;
    public $sisters_count = 0;

    public $heirs_shares = [];
    public $net_cash = 0;
    public $net_marlas = 0;
    public $debt_shortfall = 0;
    public $will_capped = false;
    public $has_awl = false;
    public $has_radd = false;
    public $unassigned_pct = 0;

    public function mount()
    {
        $this->calculateInheritance();
    }

    public function updated($propertyName)
    {
        $this->calculateInheritance();
    }

    public function calculateInheritance()
    {
        // Form se aane wali values strings hoti hain, aur inhein had mein rakhna zaroori hai
        $this->deceased_gender = $this->deceased_gender === 'female' ? 'female' : 'male';
        $this->wives_count = $this->count($this->wives_count, 4);
        $this->sons_count = $this->count($this->sons_count);
        $this->daughters_count = $this->count($this->daughters_count);
        $this->brothers_count = $this->count($this->brothers_count);
        $this->sisters_count = $this->count($this->sisters_count);

        // Tarteeb: pehle qarz aur kafan dafan, phir wasiyat (1/3 tak), phir warasat
        $cash = max(0, (float) $this->cash_estate);
        $debts = max(0, (float) $this->debts_funeral);
        $afterDebts = max(0, $cash - $debts);
        $this->debt_shortfall = max(0, $debts - $cash);

        $will = max(0, (float) $this->will_amount);
        $maxWill = $afterDebts / 3;
        $this->will_capped = $will > $maxWill;

        $this->net_cash = $afterDebts - min($will, $maxWill);
        $this->net_marlas = max(0, (float) $this->total_kanals) * 20.0 + max(0, (float) $this->total_marlas);

        $result = InheritanceEngine::calculate([
            'gender' => $this->deceased_gender,
            'wives' => $this->wives_count,
            'husband' => (bool) $this->has_husband,
            'father' => (bool) $this->has_father,
            'mother' => (bool) $this->has_mother,
            'sons' => $this->sons_count,
            'daughters' => $this->daughters_count,
            'brothers' => $this->brothers_count,
            'sisters' => $this->sisters_count,
        ]);

        $this->has_awl = $result['awl'];
        $this->has_radd = $result['radd'];
        $this->unassigned_pct = round($result['unassigned'] * 100, 2);

        // Har group ki ek row: hissa fi kas dikhaya jata hai
        $this->heirs_shares = array_map(function ($g) {
            $each = $g['share'] / $g['count'];
            $marlas = $this->net_marlas * $each;

            return [
                'key' => $g['key'],
                'count' => $g['count'],
                'basis' => $g['basis'],
                'percentage' => round($each * 100, 2),
                'group_percentage' => round($g['share'] * 100, 2),
                'kanals' => (int) floor($marlas / 20.0),
                'marlas' => round(fmod($marlas, 20.0), 2),
                'cash_amount' => round($this->net_cash * $each),
            ];
        }, $result['groups']);
    }

    private function count($value, int $max = 50): int
    {
        return min($max, max(0, (int) $value));
    }

    public function render()
    {
        return view('livewire.tools.inheritance-calculator')
            ->layout('layouts.front-app', ToolCatalog::meta('inheritance'));
    }
}
