<?php

namespace App\Livewire\Tools;

use App\Support\Tools\DeedHelper;
use App\Support\Tools\ToolCatalog;
use Livewire\Component;

class RentAgreementGenerator extends Component
{
    // Print Controls
    public $leave_stamp_space = true; // Top 4.5 inch blank space for e-Stamp Paper

    // Landlord Details
    public $landlord_name = '';
    public $landlord_father = '';
    public $landlord_cnic = '';
    public $landlord_phone = '';
    public $landlord_address = '';

    // Tenant Details
    public $tenant_name = '';
    public $tenant_father = '';
    public $tenant_cnic = '';
    public $tenant_phone = '';
    public $tenant_permanent_address = '';

    // Premises Information
    public $property_type = 'residential'; // residential, commercial, portion, warehouse
    public $property_title = '';
    public $city = '';
    public $property_address = '';

    // Tenancy & Financial Terms
    public $monthly_rent = 0;
    public $security_deposit = 0;
    public $annual_increment_percentage = 10;
    public $tenancy_tenure_months = 11;
    public $start_date = '';
    public $end_date = '';
    public $rent_due_day = 5;
    public $payment_method = 'bank_transfer';

    // Meter Reference
    public $electricity_meter_no = '';
    public $gas_meter_no = '';
    public $current_electricity_reading = '';

    // Witnesses
    public $witness1_name = '';
    public $witness1_cnic = '';
    public $witness2_name = '';
    public $witness2_cnic = '';

    public function mount()
    {
        $this->start_date = date('Y-m-d');
        $this->updateEndDate();
    }

    public function updated($propertyName)
    {
        $this->monthly_rent = max(0, (float) $this->monthly_rent);
        $this->security_deposit = max(0, (float) $this->security_deposit);
        $this->annual_increment_percentage = min(100, max(0, (float) $this->annual_increment_percentage));
        $this->tenancy_tenure_months = min(120, max(1, (int) $this->tenancy_tenure_months));
        $this->rent_due_day = min(28, max(1, (int) $this->rent_due_day));
        $this->updateEndDate();
    }

    private function updateEndDate()
    {
        $start = DeedHelper::date($this->start_date);
        $this->end_date = $start ? $start->addMonthsNoOverflow((int) $this->tenancy_tenure_months)->subDay()->format('Y-m-d') : '';
    }

    private function issues(): array
    {
        $issues = DeedHelper::missing([
            'Landlord name' => $this->landlord_name, 'Landlord father name' => $this->landlord_father, 'Landlord address' => $this->landlord_address,
            'Tenant name' => $this->tenant_name, 'Tenant father name' => $this->tenant_father, 'Tenant address' => $this->tenant_permanent_address,
            'Property details' => $this->property_title, 'Property address' => $this->property_address, 'City' => $this->city,
            'Witness 1' => $this->witness1_name, 'Witness 2' => $this->witness2_name,
        ]);

        foreach (['Landlord' => $this->landlord_cnic, 'Tenant' => $this->tenant_cnic, 'Witness 1' => $this->witness1_cnic, 'Witness 2' => $this->witness2_cnic] as $who => $cnic) {
            if (! DeedHelper::validCnic($cnic)) {
                $issues[] = __(':who CNIC must have 13 digits', ['who' => __($who)]);
            }
        }

        if ($this->monthly_rent <= 0) {
            $issues[] = __('Enter the monthly rent');
        }
        if (! DeedHelper::date($this->start_date)) {
            $issues[] = __('Enter the start date');
        }

        return $issues;
    }

    public function render()
    {
        return view('livewire.tools.rent-agreement-generator', ['issues' => $this->issues()])
            ->layout('layouts.front-app', ToolCatalog::meta('rent'));
    }
}
