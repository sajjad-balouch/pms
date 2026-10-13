<?php

namespace App\Livewire\Agent;

use App\Models\Property;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BayanaGenerator extends Component
{
    // Mode: 'create' or 'preview'
    public $isPreview = false;

    // Agreement Details
    public $agreementDate;
    public $agreementType = 'bayana'; // 'bayana' (بیعانہ) or 'token' (ٹوکن سر دستی)

    // Seller (فروخت کنندہ / مالک)
    public $sellerName = '';
    public $sellerCnic = '';
    public $sellerPhone = '';
    public $sellerAddress = '';

    // Buyer (خریدار)
    public $buyerName = '';
    public $buyerCnic = '';
    public $buyerPhone = '';
    public $buyerAddress = '';

    // Property Details
    public $propertyTitle = '';
    public $plotNumber = '';
    public $schemeName = '';
    public $areaSize = '';
    public $city = '';

    // Financials
    public $totalDealAmount = 0;
    public $bayanaAmount = 0;
    public $remainingAmount = 0;
    public $paymentMethod = 'cash'; // 'cash', 'cheque', 'online'
    public $chequeDetails = '';
    public $finalPaymentDate = '';

    // Witness Details (گواہان)
    public $witness1Name = '';
    public $witness1Cnic = '';
    public $witness2Name = '';
    public $witness2Cnic = '';

    public function mount()
    {
        $this->agreementDate = date('Y-m-d');
        $this->finalPaymentDate = date('Y-m-d', strtotime('+30 days'));
    }

    public function updated($field)
    {
        if (in_array($field, ['totalDealAmount', 'bayanaAmount'])) {
            $this->calculateRemaining();
        }
    }

    public function calculateRemaining()
    {
        $total = floatval($this->totalDealAmount);
        $bayana = floatval($this->bayanaAmount);
        $this->remainingAmount = max(0, $total - $bayana);
    }

    public function generateReceipt()
    {
        $this->validate([
            'sellerName' => 'required|string|max:100',
            'buyerName' => 'required|string|max:100',
            'propertyTitle' => 'required|string|max:150',
            'areaSize' => 'required|string|max:50',
            'totalDealAmount' => 'required|numeric|min:1',
            'bayanaAmount' => 'required|numeric|min:1',
            'finalPaymentDate' => 'required|date',
        ]);

        $this->calculateRemaining();
        $this->isPreview = true;
    }

    public function editAgreement()
    {
        $this->isPreview = false;
    }

    public function render()
    {
        return view('livewire.agent.bayana-generator')->layout('layouts.app');
    }
}