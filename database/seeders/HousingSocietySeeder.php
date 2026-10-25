<?php

namespace Database\Seeders;

use App\Models\HousingSocietyVerification;
use Illuminate\Database\Seeder;

class HousingSocietySeeder extends Seeder
{
    public function run(): void
    {
        $societies = [
            [
                'name' => 'Etihad Garden (اتحاد گارڈن)',
                'city' => 'Rahim Yar Khan',
                'authority' => 'TMA / PHATA',
                'status' => 'approved',
                'noc_number' => 'TMA-RYK/TP/NOC-2019-142',
                'approval_date' => '2019-11-14',
                'developer_name' => 'Etihad Group',
                'total_sanctioned_area' => '1,850 Kanal',
                'sanctioned_plots_count' => 3200,
                'mortgaged_plots_count' => 640,
                'lop_status' => 'Sanctioned Layout Plan Final',
                'risk_warning' => null,
                'is_featured' => true,
            ],
            [
                'name' => 'Gulshan-e-Iqbal Housing Scheme',
                'city' => 'Rahim Yar Khan',
                'authority' => 'TMA',
                'status' => 'approved',
                'noc_number' => 'TMA-RYK/NOC-98/12',
                'approval_date' => '2012-05-10',
                'developer_name' => 'Private Developers',
                'total_sanctioned_area' => '420 Kanal',
                'sanctioned_plots_count' => 850,
                'mortgaged_plots_count' => 0,
                'lop_status' => 'Completed & Handed Over',
                'risk_warning' => null,
                'is_featured' => false,
            ],
            [
                'name' => 'Green Palms Housing (گرین پامز)',
                'city' => 'Khanpur',
                'authority' => 'TMA Khanpur',
                'status' => 'under_process',
                'noc_number' => 'APP-KHN-2024-91',
                'approval_date' => null,
                'developer_name' => 'Al-Madina Builders',
                'total_sanctioned_area' => '300 Kanal',
                'sanctioned_plots_count' => 600,
                'mortgaged_plots_count' => 120,
                'lop_status' => 'Preliminary Planning Permission Only',
                'risk_warning' => 'حتمی NOC جاری نہیں ہوا، فائلیں خریدنے سے قبل TMA سے تصدیق کریں۔',
                'is_featured' => false,
            ],
            [
                'name' => 'Al-Haram City (الحرم سٹی - غیر قانونی)',
                'city' => 'Rahim Yar Khan',
                'authority' => 'TMA / District Council',
                'status' => 'illegal',
                'noc_number' => 'NONE (Rejection Order: RYK-88)',
                'approval_date' => null,
                'developer_name' => 'Private Land Grabbers',
                'total_sanctioned_area' => '120 Kanal',
                'sanctioned_plots_count' => 0,
                'mortgaged_plots_count' => 0,
                'lop_status' => 'No Approved Layout Plan',
                'risk_warning' => 'یہ ہاؤسنگ سکیم بغیر منظوری کے زرعی اراضی پر پلاٹ کاٹ رہی ہے۔ خرید و فروخت پر پابندی ہے۔',
                'is_featured' => false,
            ],
        ];

        foreach ($societies as $item) {
            HousingSocietyVerification::updateOrCreate(['name' => $item['name']], $item);
        }
    }
}