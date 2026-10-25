<?php

namespace App\Livewire\Tools;

use App\Models\RentAgreement;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class RentAgreementGenerator extends Component
{
    // Print Controls
    public $leave_stamp_space = true; // Top 4.5 inch blank space for e-Stamp Paper

    // Landlord Details
    public $landlord_name = 'ملک محمد اسلم';
    public $landlord_father = 'ملک عبدالحفیظ';
    public $landlord_cnic = '31301-2345678-1';
    public $landlord_phone = '0300-9876543';
    public $landlord_address = 'مکان نمبر 24، گلی نمبر 3، ٹرسٹ کالونی، رحیم یار خان';

    // Tenant Details
    public $tenant_name = 'وسیم اکرم';
    public $tenant_father = 'بشیر احمد';
    public $tenant_cnic = '31301-8765432-1';
    public $tenant_phone = '0302-1234567';
    public $tenant_permanent_address = 'چک نمبر 89، ڈاکخانہ خاص، تحصیل صادق آباد';

    // Premises Information
    public $property_type = 'residential'; // residential, commercial, portion, warehouse
    public $property_title = 'رہائشی مکان رقبہ 5 مرلہ معہ بجلی و گیس کنکشن';
    public $city = 'Rahim Yar Khan';
    public $property_address = 'پلاٹ نمبر 108، بلاک بی، گلشنِ عثمان، رحیم یار خان';

    // Tenancy & Financial Terms
    public $monthly_rent = 25000;
    public $security_deposit = 50000; // 2 months advance security
    public $annual_increment_percentage = 10;
    public $tenancy_tenure_months = 11;
    public $start_date = '';
    public $end_date = '';
    public $rent_due_day = 5;
    public $payment_method = 'bank_transfer';

    // Meter Reference
    public $electricity_meter_no = 'MEPCO-889921';
    public $gas_meter_no = 'SNGPL-443321';
    public $current_electricity_reading = '14520';

    // Witnesses
    public $witness1_name = 'عبدالوحید ولد محمد صادق';
    public $witness1_cnic = '31301-1122334-5';
    public $witness2_name = 'محمد عدنان ولد نذیر احمد';
    public $witness2_cnic = '31301-5566778-9';

    public function mount()
    {
        $this->start_date = date('Y-m-d');$this->updateEndDate();
    }

    public function updatedStartDate()
    {
        $this->updateEndDate();
    }

    public function updatedTenancyTenureMonths()
    {
        $this->updateEndDate();
    }

    private function updateEndDate()
    {
        if ($this->start_date) {$months = max(1, (int)$this->tenancy_tenure_months);$this->end_date = Carbon::parse($this->start_date)->addMonths($months)->subDay()->format('Y-m-d');
        }
    }

    public function render()
    {
        return view('livewire.tools.rent-agreement-generator')
            ->layout('layouts.front-app', ['title' => 'خودکار کرایہ نامہ بلڈر و قانونی ٹیننسی ایگریمنٹ | AssanZameen']);
    }
}