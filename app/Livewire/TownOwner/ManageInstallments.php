<?php

namespace App\Livewire\TownOwner;

use App\Models\Installment;
use App\Models\Plot;
use Carbon\Carbon;
use Livewire\Component;

class ManageInstallments extends Component
{
    public $plot;
    public $buyer_name = '';
    public $buyer_phone = '';
    public $total_price = 0;

    public function mount($plotId)
    {
        $this->plot = Plot::with(['town', 'installments'])->findOrFail($plotId);
        
        $this->total_price = $this->plot->total_price ?? 0;

        // اگر پہلے سے کوئی انسٹالمنٹ موجود ہو تو براہِ راست buyer_name اور buyer_phone کالم سے ڈیٹا حاصل کریں
        $firstInstallment = $this->plot->installments->first();
        if ($firstInstallment) {
            $this->buyer_name = $firstInstallment->buyer_name ?? '';
            $this->buyer_phone = $firstInstallment->buyer_phone ?? '';
        }
    }

    /**
     * Generate Monthly Installment Plan
     */
    public function generateInstallmentPlan()
    {
        $this->validate([
            'buyer_name' => 'required|string|max:255',
            'buyer_phone' => 'required|string|max:20',
        ]);

        $this->plot->update([
            'status' => 'booked',
        ]);

        $plotPrice = $this->plot->total_price ?? 0;
        $downPayment = $this->plot->down_payment ?? 0;

        $remainingAmount = $plotPrice - $downPayment;
        $totalInstallments = $this->plot->total_installments ?? 1;
        $perInstallmentAmount = $remainingAmount > 0 ? ($remainingAmount / $totalInstallments) : 0;

        for ($i = 1; $i <= $totalInstallments; $i++) {
            Installment::create([
                'plot_id' => $this->plot->id,
                'buyer_name' => $this->buyer_name,
                'buyer_phone' => $this->buyer_phone,
                'installment_number' => $i,
                'amount' => $perInstallmentAmount,
                'due_date' => Carbon::now()->addMonths($i),
                'status' => 'pending',
                'remarks' => null,
            ]);
        }

        $this->plot->refresh();

        session()->flash('success', 'قسطوار شیڈول اور خریدار کی تفصیلات کامیابی سے درج کر لی گئی ہیں۔');
    }

    /**
     * Mark Whole Plot as Full Paid (Direct Sale with Buyer details saved in columns)
     */
    public function markAsFullPaid()
    {
        // 1. خریدار کا نام اور فون والڈیٹ کریں
        $this->validate([
            'buyer_name' => 'required|string|max:255',
            'buyer_phone' => 'required|string|max:20',
        ]);

        $plotPrice = $this->plot->total_price ?? $this->total_price ?? 0;

        // 2. تمام پینڈنگ اقساط کو ڈیلیٹ کریں
        Installment::where('plot_id', $this->plot->id)->where('status', 'pending')->delete();

        // 3. اگر پہلے سے انٹری ہے تو اپڈیٹ کریں ورنہ نئ انٹری بنائیں (buyer_name اور buyer_phone کے کالمز میں سیو کریں)
        $fullInstallment = Installment::where('plot_id', $this->plot->id)->first();

        if (!$fullInstallment) {
            Installment::create([
                'plot_id' => $this->plot->id,
                'buyer_name' => $this->buyer_name,
                'buyer_phone' => $this->buyer_phone,
                'installment_number' => 1,
                'amount' => $plotPrice,
                'due_date' => now(),
                'paid_date' => now(),
                'status' => 'paid',
                'remarks' => 'Direct Full Cash Payment',
            ]);
        } else {
            $fullInstallment->update([
                'buyer_name' => $this->buyer_name,
                'buyer_phone' => $this->buyer_phone,
                'amount' => $plotPrice,
                'paid_date' => now(),
                'status' => 'paid',
                'remarks' => 'Direct Full Cash Payment',
            ]);
        }

        // 4. پلاٹ کا سٹیٹس Sold کر دیں
        $this->plot->update([
            'status' => 'sold',
        ]);

        $this->plot->refresh();

        session()->flash('success', 'خریدار کی تفصیلات اور مکمل ادائیگی کامیابی سے کالمز میں محفوظ ہو گئی ہے۔');
    }

    /**
     * Mark a single installment as Paid
     */
    public function markAsPaid($installmentId)
    {
        $installment = Installment::where('plot_id', $this->plot->id)->findOrFail($installmentId);

        $installment->update([
            'status' => 'paid',
            'paid_date' => now(),
        ]);

        $pendingCount = Installment::where('plot_id', $this->plot->id)->where('status', 'pending')->count();
        if ($pendingCount === 0) {
            $this->plot->update(['status' => 'sold']);
        }

        $this->plot->refresh();

        session()->flash('success', "قسط #{$installment->installment_number} کی وصولی کامیابی سے درج ہو گئی۔");
    }

    public function render()
    {
        $installments = $this->plot->installments()->orderBy('installment_number')->get();
        $nextDue = $installments->where('status', 'pending')->sortBy('due_date')->first();

        return view('livewire.town-owner.manage-installments', [
            'installments' => $installments,
            'nextDue' => $nextDue,
        ])->layout('layouts.app');
    }
}