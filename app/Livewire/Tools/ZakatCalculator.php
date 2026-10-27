<?php

namespace App\Livewire\Tools;

use Livewire\Component;

class ZakatCalculator extends Component
{
    // Configuration & Nisab
    public $nisab_standard = 'silver'; // 'silver' (52.5 Tola) or 'gold' (7.5 Tola)
    public $gold_rate_per_tola = 295000;  // Market rate benchmark
    public $silver_rate_per_tola = 3300; // Market rate benchmark

    // Assets Category 1: Gold & Silver
    public $gold_tola = 0;
    public $silver_tola = 0;
    public $jewelry_cash_value = 0;

    // Assets Category 2: Cash & Bank Accounts
    public $cash_in_hand = 150000;
    public $bank_savings = 850000;
    public $foreign_currency_pkr = 0;
    public $prize_bonds = 0;

    // Assets Category 3: Real Estate & Investment (شرعی تفریق)
    public $trading_plots_files_value = 0; // پلاٹ / فائلیں جو بیچنے کی نیت سے خریدی گئیں
    public $rental_income_retained = 0;    // کرائے کی بچت جو اکاؤنٹ میں موجود ہے
    public $business_inventory = 0;        // دکان یا کاروبار کا مالِ تجارت
    public $good_loans_receivable = 0;     // وہ رقم جو کسی کو قرض دی اور واپسی یقینی ہے

    // Liabilities (کٹوتی / واجب الادا قرض)
    public $immediate_debt_payable = 0;    // فوری ادا کرنے والا قرض
    public $unpaid_bills_salaries = 0;     // واجب الادا بلز و ملازمین کی تنخواہیں

    // Calculation Results
    public $total_zakatable_assets = 0;
    public $total_deductible_liabilities = 0;
    public $net_wealth = 0;
    public $nisab_threshold_value = 0;
    public $is_nisab_reached = false;
    public $zakat_due = 0;

    public function mount()
    {
        $this->calculateZakat();
    }

    public function updated($propertyName)
    {
        $this->calculateZakat();
    }

    public function calculateZakat()
    {
        // 1. Calculate Nisab Threshold
        if ($this->nisab_standard === 'silver') {
            // 52.5 Tola Chandi (Standard for mixed wealth & currency)
            $this->nisab_threshold_value = 52.5 * (float)$this->silver_rate_per_tola;
        } else {
            // 7.5 Tola Sona
            $this->nisab_threshold_value = 7.5 * (float)$this->gold_rate_per_tola;
        }

        // 2. Metals Value
        $goldValue = ((float)$this->gold_tola * (float)$this->gold_rate_per_tola);
        $silverValue = ((float)$this->silver_tola * (float)$this->silver_rate_per_tola);
        $totalMetals = $goldValue + $silverValue + (float)$this->jewelry_cash_value;

        // 3. Cash & Liquid Assets
        $totalCash = (float)$this->cash_in_hand 
                   + (float)$this->bank_savings 
                   + (float)$this->foreign_currency_pkr 
                   + (float)$this->prize_bonds;

        // 4. Real Estate & Business Assets (Only trading wealth, personal house/car exempt)
        $totalBusinessProperty = (float)$this->trading_plots_files_value 
                               + (float)$this->rental_income_retained 
                               + (float)$this->business_inventory 
                               + (float)$this->good_loans_receivable;

        $this->total_zakatable_assets = $totalMetals + $totalCash + $totalBusinessProperty;

        // 5. Liabilities
        $this->total_deductible_liabilities = (float)$this->immediate_debt_payable 
                                            + (float)$this->unpaid_bills_salaries;

        // 6. Net Wealth Subject to Zakat
        $this->net_wealth = max(0, $this->total_zakatable_assets - $this->total_deductible_liabilities);

        // 7. Nisab Check & 2.5% Calculation (Rub' al-Ushr)
        if ($this->net_wealth >= $this->nisab_threshold_value && $this->net_wealth > 0) {
            $this->is_nisab_reached = true;
            $this->zakat_due = round($this->net_wealth * 0.025); // 2.5%
        } else {
            $this->is_nisab_reached = false;
            $this->zakat_due = 0;
        }
    }

    public function render()
    {
        return view('livewire.tools.zakat-calculator')
            ->layout('layouts.front-app', ['title' => 'آسان شرعی زکوٰۃ کیلکولیٹر و رئیل اسٹیٹ زکوٰۃ شیڈول | AssanZameen']);
    }
}