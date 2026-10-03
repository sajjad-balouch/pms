<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $methods = [
            [
                'method_name' => 'JazzCash',
                'account_title' => 'Muhammad Ali',
                'account_number' => '03001234567',
                'instructions' => 'Please transfer the amount to the JazzCash account above and upload the payment screenshot with the Transaction ID (TID).',
                'is_active' => true,
            ],
            [
                'method_name' => 'EasyPaisa',
                'account_title' => 'Muhammad Ali',
                'account_number' => '03451234567',
                'instructions' => 'Send payment to the EasyPaisa mobile account. Keep the screenshot ready to upload as proof.',
                'is_active' => true,
            ],
            [
                'method_name' => 'Meezan Bank',
                'account_title' => 'Company Official Account',
                'account_number' => '010101029384756',
                'instructions' => 'Meezan Bank Account. IBAN: PK36MEZN00010101029384756. Mention your registered email or username in the transfer remarks.',
                'is_active' => true,
            ],
            [
                'method_name' => 'Nayapay / Sadapay',
                'account_title' => 'Muhammad Ali',
                'account_number' => '03121234567',
                'instructions' => 'Instant wallet transfer via NayaPay or SadaPay.',
                'is_active' => true,
            ],
        ];

        foreach ($methods as $method) {
            PaymentMethod::updateOrCreate(
                ['method_name' => $method['method_name']],
                $method
            );
        }
    }
}