<?php

namespace App\Livewire\Tools;

use App\Models\BayanaDeed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class BayanaDeedGenerator extends Component
{
    // Wizard State: 'form' ya 'preview'
    public $viewMode = 'form'; // 'form' or 'preview'
    public $leave_stamp_space = true; // e-Stamp paper ke liye upar 4.5 inch jagah chhorna

    // Seller Info
    public $seller_name = 'محمد اکرم';
    public $seller_father = 'چوہدری عبداللہ';
    public $seller_cnic = '31301-1234567-1';
    public $seller_phone = '0300-1234567';
    public $seller_address = 'چک نمبر 123، تحصیل خان پور، ضلع رحیم یار خان';

    // Buyer Info
    public $buyer_name = 'محمد سجاد';
    public $buyer_father = 'شمس الدین';
    public $buyer_cnic = '31301-9876543-1';
    public $buyer_phone = '0301-7654321';
    public $buyer_address = 'محلہ گلشنِ اقبال، رحیم یار خان';

    // Property Details
    public $property_title = 'رہائشی پلاٹ نمبر 45، گلی نمبر 3';
    public $city = 'Rahim Yar Khan';
    public $location_mouza = 'موضع میاں والی، نزد چوک بہادر پور';
    public $khasra_khewat = 'کھیوٹ نمبر 150، خسرہ نمبر 273/12';
    public $area_size = '5 مرلہ (1,125 مربع فٹ)';

    // Financials
    public $total_amount = 3500000; // 35 Lac
    public $bayana_advance_amount = 500000; // 5 Lac
    public $remaining_amount = 3000000;
    public $payment_method = 'pay_order'; // cash, pay_order, cheque, online
    public $payment_reference = 'Pay Order No: PO-889012 (HBL)';
    public $deed_date = '';
    public $target_registry_date = '';

    // Witnesses
    public $witness1_name = 'طارق محمود ولد بشیر احمد';
    public $witness1_cnic = '31301-4455667-3';
    public $witness2_name = 'عبدالرحمان ولد غلام سرور';
    public $witness2_cnic = '31301-9988776-5';

    public $saved_deed_uuid = null;

    protected $rules = [
        'seller_name' => 'required|string|max:100',
        'seller_father' => 'required|string|max:100',
        'seller_cnic' => 'required|string|min:13|max:15',
        'seller_address' => 'required|string',
        'buyer_name' => 'required|string|max:100',
        'buyer_father' => 'required|string|max:100',
        'buyer_cnic' => 'required|string|min:13|max:15',
        'buyer_address' => 'required|string',
        'property_title' => 'required|string',
        'city' => 'required|string',
        'location_mouza' => 'required|string',
        'area_size' => 'required|string',
        'total_amount' => 'required|numeric|min:1000',
        'bayana_advance_amount' => 'required|numeric|min:100',
        'deed_date' => 'required|date',
        'target_registry_date' => 'required|date|after_or_equal:deed_date',
    ];

    public function mount()
    {
        $this->deed_date = date('Y-m-d');
        $this->target_registry_date = date('Y-m-d', strtotime('+45 days'));
        $this->calculateRemaining();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['total_amount', 'bayana_advance_amount'])) {
            $this->calculateRemaining();
        }
    }

    public function calculateRemaining()
    {
        $tot = max(0, (float) $this->total_amount);
        $adv = max(0, (float) $this->bayana_advance_amount);
        $this->remaining_amount = max(0, $tot - $adv);
    }

    public function generateDeed()
    {
        $this->validate();
        $this->calculateRemaining();

        $deed = BayanaDeed::create([
            'deed_uuid' => (string) Str::uuid(),
            'user_id' => Auth::id(),
            'seller_name' => $this->seller_name,
            'seller_father' => $this->seller_father,
            'seller_cnic' => $this->seller_cnic,
            'seller_phone' => $this->seller_phone,
            'seller_address' => $this->seller_address,
            'buyer_name' => $this->buyer_name,
            'buyer_father' => $this->buyer_father,
            'buyer_cnic' => $this->buyer_cnic,
            'buyer_phone' => $this->buyer_phone,
            'buyer_address' => $this->buyer_address,
            'property_title' => $this->property_title,
            'city' => $this->city,
            'location_mouza' => $this->location_mouza,
            'khasra_khewat' => $this->khasra_khewat,
            'area_size' => $this->area_size,
            'total_amount' => $this->total_amount,
            'bayana_advance_amount' => $this->bayana_advance_amount,
            'remaining_amount' => $this->remaining_amount,
            'payment_method' => $this->payment_method,
            'payment_reference' => $this->payment_reference,
            'deed_date' => $this->deed_date,
            'target_registry_date' => $this->target_registry_date,
            'witness1_name' => $this->witness1_name,
            'witness1_cnic' => $this->witness1_cnic,
            'witness2_name' => $this->witness2_name,
            'witness2_cnic' => $this->witness2_cnic,
            'ip_address' => request()->ip(),
        ]);

        $this->saved_deed_uuid = $deed->deed_uuid;
        $this->viewMode = 'preview';
    }

    public function editForm()
    {
        $this->viewMode = 'form';
    }

    public function render()
    {
        return view('livewire.tools.bayana-deed-generator')
            ->layout('layouts.front-app', ['title' => 'خودکار اقرار نامہ بیعانہ میکر و اسٹامپ بلڈر | AssanZameen']);
    }
}