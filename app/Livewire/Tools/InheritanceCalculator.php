<?php

namespace App\Livewire\Tools;

use Livewire\Component;

class InheritanceCalculator extends Component
{
    // Deceased Details
    public $deceased_gender = 'male'; // 'male' or 'female'
    public $total_kanals = 4;
    public $total_marlas = 0;
    public $cash_estate = 5000000; // Kul Maal / Naqad
    public $debts_funeral = 200000; // Qarz aur Tajheez o Takfeen

    // Heirs Input
    public $wives_count = 1; // Agar mard fout hua
    public $has_husband = false; // Agar aurat fout hui
    public $has_father = true;
    public $has_mother = true;
    public $sons_count = 2;
    public $daughters_count = 3;

    public $heirs_shares = [];
    public $net_cash = 0;
    public $net_marlas = 0;

    public function mount()
    {
        $this->calculateInheritance();
    }

    public function updated($propertyName)
    {
        if ($this->deceased_gender === 'female') {
            $this->wives_count = 0;
        } else {
            $this->has_husband = false;
        }

        $this->calculateInheritance();
    }

    public function calculateInheritance()
    {
        // 1. Total Distributable Assets
        $this->net_cash = max(0, (float)$this->cash_estate - (float)$this->debts_funeral);
        $this->net_marlas = ((float)$this->total_kanals * 20.0) + (float)$this->total_marlas;

        $hasChildren = ($this->sons_count > 0 || $this->daughters_count > 0);
        $shares = [];
        $allocatedPercentage = 0;

        // --- A. Spouse Share (Zawj / Zawjah) ---
        if ($this->deceased_gender === 'male' && $this->wives_count > 0) {
            // Biwi: 1/8 (12.5%) agar aulad ho, warna 1/4 (25%)
            $pct = $hasChildren ? 12.5 : 25.0;
            $perWifePct = $pct / max(1, $this->wives_count);
            for ($i = 1; $i <= $this->wives_count; $i++) {
                $shares[] = [
                    'relation' => 'بیوہ (زوجہ ' . ($this->wives_count > 1 ? "#$i" : '') . ')',
                    'quranic_fraction' => $hasChildren ? '1/8' : '1/4',
                    'percentage' => $perWifePct,
                    'is_asabah' => false,
                ];
                $allocatedPercentage += $perWifePct;
            }
        } elseif ($this->deceased_gender === 'female' && $this->has_husband) {
            // Shauhar: 1/4 (25%) agar aulad ho, warna 1/2 (50%)
            $pct = $hasChildren ? 25.0 : 50.0;
            $shares[] = [
                'relation' => 'شوہر (زوج)',
                'quranic_fraction' => $hasChildren ? '1/4' : '1/2',
                'percentage' => $pct,
                'is_asabah' => false,
            ];
            $allocatedPercentage += $pct;
        }

        // --- B. Mother (Walida) ---
        if ($this->has_mother) {
            // Walida: 1/6 (16.666%) agar aulad ya 2+ behan bhai hon, warna 1/3 (33.333%)
            $pct = $hasChildren ? (100.0 / 6.0) : (100.0 / 3.0);
            $shares[] = [
                'relation' => 'والدہ',
                'quranic_fraction' => $hasChildren ? '1/6' : '1/3',
                'percentage' => $pct,
                'is_asabah' => false,
            ];
            $allocatedPercentage += $pct;
        }

        // --- C. Father (Walid) ---
        if ($this->has_father) {
            // Walid: 1/6 fix agar aulad ho
            $pct = $hasChildren ? (100.0 / 6.0) : 0; // Agar aulad na ho to asabah banega
            if ($pct > 0) {
                $shares[] = [
                    'relation' => 'والد',
                    'quranic_fraction' => '1/6',
                    'percentage' => $pct,
                    'is_asabah' => false,
                ];
                $allocatedPercentage += $pct;
            }
        }

        // --- D. Children (Asabah Bil Ghair: Beta 2 Hisse, Beti 1 Hissa) ---
        $remainingPercentage = max(0, 100.0 - $allocatedPercentage);

        if ($this->sons_count > 0 || $this->daughters_count > 0) {
            if ($this->sons_count > 0) {
                // Beta mojud hai: Residuary (عصبہ) ratio (2:1)
                $totalUnits = ($this->sons_count * 2) + $this->daughters_count;
                $perUnitPct = $remainingPercentage / max(1, $totalUnits);

                for ($s = 1; $s <= $this->sons_count; $s++) {
                    $shares[] = [
                        'relation' => 'بیٹا (فرزند ' . ($this->sons_count > 1 ? "#$s" : '') . ')',
                        'quranic_fraction' => 'عصبہ (2 حصص)',
                        'percentage' => $perUnitPct * 2,
                        'is_asabah' => true,
                    ];
                }

                for ($d = 1; $d <= $this->daughters_count; $d++) {
                    $shares[] = [
                        'relation' => 'بیٹی (دختر ' . ($this->daughters_count > 1 ? "#$d" : '') . ')',
                        'quranic_fraction' => 'عصبہ (1 حصہ)',
                        'percentage' => $perUnitPct,
                        'is_asabah' => true,
                    ];
                }
            } else {
                // Sirf Betiyan hain (Koi beta nahi): 1 beti = 1/2, 2 ya zyada = 2/3
                $daughtersPct = ($this->daughters_count === 1) ? 50.0 : (200.0 / 3.0);
                $perDaughterPct = $daughtersPct / $this->daughters_count;

                for ($d = 1; $d <= $this->daughters_count; $d++) {
                    $shares[] = [
                        'relation' => 'بیٹی (دختر ' . ($this->daughters_count > 1 ? "#$d" : '') . ')',
                        'quranic_fraction' => ($this->daughters_count === 1) ? '1/2' : '2/3 مشترکہ',
                        'percentage' => $perDaughterPct,
                        'is_asabah' => false,
                    ];
                }

                // Agar walid zinda hai to baqi bacha hissa walid ko asabah ke tor par milega
                if ($this->has_father) {
                    $walidAsabahPct = max(0, $remainingPercentage - $daughtersPct);
                    $shares[] = [
                        'relation' => 'والد (بطور عصبہ بقیہ مال)',
                        'quranic_fraction' => 'عصبہ',
                        'percentage' => $walidAsabahPct,
                        'is_asabah' => true,
                    ];
                }
            }
        } elseif ($this->has_father) {
            // Koi aulad nahi: Pura bacha hua hissa walid ko
            $shares[] = [
                'relation' => 'والد (عصبہ بنفسہ)',
                'quranic_fraction' => 'تمام بقیہ ترکہ',
                'percentage' => $remainingPercentage,
                'is_asabah' => true,
            ];
        }

        // Calculate exact Marlas, Kanals and Cash values
        $this->heirs_shares = [];
        foreach ($shares as $item) {
            $marlas = ($this->net_marlas * $item['percentage']) / 100.0;
            $cash = ($this->net_cash * $item['percentage']) / 100.0;

            $kanals = floor($marlas / 20.0);
            $remMarlas = fmod($marlas, 20.0);

            $this->heirs_shares[] = [
                'relation' => $item['relation'],
                'fraction' => $item['quranic_fraction'],
                'percentage' => round($item['percentage'], 2),
                'total_marlas' => round($marlas, 2),
                'formatted_land' => "{$kanals} کنال - " . round($remMarlas, 1) . " مرلہ",
                'cash_amount' => round($cash),
            ];
        }
    }

    public function render()
    {
        return view('livewire.tools.inheritance-calculator')
            ->layout('layouts.front-app', ['title' => 'اسلامی شرعی وراثت و تقسیمِ اراضی کیلکولیٹر | AssanZameen']);
    }
}