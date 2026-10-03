<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            // Capital
            [
                'name' => 'Islamabad',
                'province' => 'Islamabad Capital Territory',
                'is_featured' => true,
                'sort_order' => 1,
            ],

            // Punjab
            ['name' => 'Lahore', 'province' => 'Punjab', 'is_featured' => true, 'sort_order' => 2],
            ['name' => 'Rawalpindi', 'province' => 'Punjab', 'is_featured' => true, 'sort_order' => 3],
            ['name' => 'Faisalabad', 'province' => 'Punjab', 'is_featured' => true, 'sort_order' => 4],
            ['name' => 'Multan', 'province' => 'Punjab', 'is_featured' => true, 'sort_order' => 5],
            ['name' => 'Gujranwala', 'province' => 'Punjab', 'is_featured' => true, 'sort_order' => 6],
            ['name' => 'Sialkot', 'province' => 'Punjab', 'is_featured' => true, 'sort_order' => 7],
            ['name' => 'Bahawalpur', 'province' => 'Punjab', 'is_featured' => false, 'sort_order' => 10],
            ['name' => 'Sargodha', 'province' => 'Punjab', 'is_featured' => false, 'sort_order' => 11],
            ['name' => 'Sheikhupura', 'province' => 'Punjab', 'is_featured' => false, 'sort_order' => 12],
            ['name' => 'Jhang', 'province' => 'Punjab', 'is_featured' => false, 'sort_order' => 13],
            ['name' => 'Dera Ghazi Khan', 'province' => 'Punjab', 'is_featured' => false, 'sort_order' => 14],
            ['name' => 'Gujrat', 'province' => 'Punjab', 'is_featured' => false, 'sort_order' => 15],
            ['name' => 'Sahiwal', 'province' => 'Punjab', 'is_featured' => false, 'sort_order' => 16],
            ['name' => 'Kasur', 'province' => 'Punjab', 'is_featured' => false, 'sort_order' => 17],
            ['name' => 'Okara', 'province' => 'Punjab', 'is_featured' => false, 'sort_order' => 18],
            ['name' => 'Rahim Yar Khan', 'province' => 'Punjab', 'is_featured' => false, 'sort_order' => 19],
            ['name' => 'Chiniot', 'province' => 'Punjab', 'is_featured' => false, 'sort_order' => 20],
            ['name' => 'Kamoke', 'province' => 'Punjab', 'is_featured' => false, 'sort_order' => 21],
            ['name' => 'Hafizabad', 'province' => 'Punjab', 'is_featured' => false, 'sort_order' => 22],
            ['name' => 'Sadiqabad', 'province' => 'Punjab', 'is_featured' => false, 'sort_order' => 23],
            ['name' => 'Burewala', 'province' => 'Punjab', 'is_featured' => false, 'sort_order' => 24],
            ['name' => 'Khanewal', 'province' => 'Punjab', 'is_featured' => false, 'sort_order' => 25],
            ['name' => 'Muzaffargarh', 'province' => 'Punjab', 'is_featured' => false, 'sort_order' => 26],
            ['name' => 'Mandi Bahauddin', 'province' => 'Punjab', 'is_featured' => false, 'sort_order' => 27],
            ['name' => 'Jhelum', 'province' => 'Punjab', 'is_featured' => false, 'sort_order' => 28],
            ['name' => 'Khanpur', 'province' => 'Punjab', 'is_featured' => false, 'sort_order' => 29],
            ['name' => 'Pakpattan', 'province' => 'Punjab', 'is_featured' => false, 'sort_order' => 30],
            ['name' => 'Attock', 'province' => 'Punjab', 'is_featured' => false, 'sort_order' => 31],
            ['name' => 'Vehari', 'province' => 'Punjab', 'is_featured' => false, 'sort_order' => 32],
            ['name' => 'Toba Tek Singh', 'province' => 'Punjab', 'is_featured' => false, 'sort_order' => 33],
            ['name' => 'Bahawalnagar', 'province' => 'Punjab', 'is_featured' => false, 'sort_order' => 34],
            ['name' => 'Muridke', 'province' => 'Punjab', 'is_featured' => false, 'sort_order' => 35],
            ['name' => 'Chakwal', 'province' => 'Punjab', 'is_featured' => false, 'sort_order' => 36],
            ['name' => 'Layyah', 'province' => 'Punjab', 'is_featured' => false, 'sort_order' => 37],
            ['name' => 'Mianwali', 'province' => 'Punjab', 'is_featured' => false, 'sort_order' => 38],
            ['name' => 'Bhakkar', 'province' => 'Punjab', 'is_featured' => false, 'sort_order' => 39],
            ['name' => 'Murree', 'province' => 'Punjab', 'is_featured' => false, 'sort_order' => 40],

            // Sindh
            ['name' => 'Karachi', 'province' => 'Sindh', 'is_featured' => true, 'sort_order' => 8],
            ['name' => 'Hyderabad', 'province' => 'Sindh', 'is_featured' => true, 'sort_order' => 9],
            ['name' => 'Sukkur', 'province' => 'Sindh', 'is_featured' => false, 'sort_order' => 41],
            ['name' => 'Larkana', 'province' => 'Sindh', 'is_featured' => false, 'sort_order' => 42],
            ['name' => 'Nawabshah (Shaheed Benazirabad)', 'province' => 'Sindh', 'is_featured' => false, 'sort_order' => 43],
            ['name' => 'Mirpur Khas', 'province' => 'Sindh', 'is_featured' => false, 'sort_order' => 44],
            ['name' => 'Jacobabad', 'province' => 'Sindh', 'is_featured' => false, 'sort_order' => 45],
            ['name' => 'Shikarpur', 'province' => 'Sindh', 'is_featured' => false, 'sort_order' => 46],
            ['name' => 'Khairpur', 'province' => 'Sindh', 'is_featured' => false, 'sort_order' => 47],
            ['name' => 'Dadu', 'province' => 'Sindh', 'is_featured' => false, 'sort_order' => 48],
            ['name' => 'Tando Allahyar', 'province' => 'Sindh', 'is_featured' => false, 'sort_order' => 49],
            ['name' => 'Tando Adam', 'province' => 'Sindh', 'is_featured' => false, 'sort_order' => 50],
            ['name' => 'Thatta', 'province' => 'Sindh', 'is_featured' => false, 'sort_order' => 51],
            ['name' => 'Badin', 'province' => 'Sindh', 'is_featured' => false, 'sort_order' => 52],

            // Khyber Pakhtunkhwa (KPK)
            ['name' => 'Peshawar', 'province' => 'Khyber Pakhtunkhwa', 'is_featured' => true, 'sort_order' => 53],
            ['name' => 'Mardan', 'province' => 'Khyber Pakhtunkhwa', 'is_featured' => false, 'sort_order' => 54],
            ['name' => 'Mingora (Swat)', 'province' => 'Khyber Pakhtunkhwa', 'is_featured' => false, 'sort_order' => 55],
            ['name' => 'Kohat', 'province' => 'Khyber Pakhtunkhwa', 'is_featured' => false, 'sort_order' => 56],
            ['name' => 'Abbottabad', 'province' => 'Khyber Pakhtunkhwa', 'is_featured' => true, 'sort_order' => 57],
            ['name' => 'Dera Ismail Khan', 'province' => 'Khyber Pakhtunkhwa', 'is_featured' => false, 'sort_order' => 58],
            ['name' => 'Nowshera', 'province' => 'Khyber Pakhtunkhwa', 'is_featured' => false, 'sort_order' => 59],
            ['name' => 'Charsadda', 'province' => 'Khyber Pakhtunkhwa', 'is_featured' => false, 'sort_order' => 60],
            ['name' => 'Swabi', 'province' => 'Khyber Pakhtunkhwa', 'is_featured' => false, 'sort_order' => 61],
            ['name' => 'Mansehra', 'province' => 'Khyber Pakhtunkhwa', 'is_featured' => false, 'sort_order' => 62],
            ['name' => 'Bannu', 'province' => 'Khyber Pakhtunkhwa', 'is_featured' => false, 'sort_order' => 63],
            ['name' => 'Haripur', 'province' => 'Khyber Pakhtunkhwa', 'is_featured' => false, 'sort_order' => 64],

            // Balochistan
            ['name' => 'Quetta', 'province' => 'Balochistan', 'is_featured' => true, 'sort_order' => 65],
            ['name' => 'Gwadar', 'province' => 'Balochistan', 'is_featured' => true, 'sort_order' => 66],
            ['name' => 'Turbat', 'province' => 'Balochistan', 'is_featured' => false, 'sort_order' => 67],
            ['name' => 'Khuzdar', 'province' => 'Balochistan', 'is_featured' => false, 'sort_order' => 68],
            ['name' => 'Sibi', 'province' => 'Balochistan', 'is_featured' => false, 'sort_order' => 69],
            ['name' => 'Zhob', 'province' => 'Balochistan', 'is_featured' => false, 'sort_order' => 70],
            ['name' => 'Chaman', 'province' => 'Balochistan', 'is_featured' => false, 'sort_order' => 71],

            // Azad Jammu & Kashmir (AJK) & Gilgit-Baltistan
            ['name' => 'Muzaffarabad', 'province' => 'Azad Jammu & Kashmir', 'is_featured' => false, 'sort_order' => 72],
            ['name' => 'Mirpur', 'province' => 'Azad Jammu & Kashmir', 'is_featured' => false, 'sort_order' => 73],
            ['name' => 'Rawalakot', 'province' => 'Azad Jammu & Kashmir', 'is_featured' => false, 'sort_order' => 74],
            ['name' => 'Gilgit', 'province' => 'Gilgit-Baltistan', 'is_featured' => false, 'sort_order' => 75],
            ['name' => 'Skardu', 'province' => 'Gilgit-Baltistan', 'is_featured' => false, 'sort_order' => 76],
        ];

        foreach ($cities as $city) {
            City::updateOrCreate(
                ['name' => $city['name']],
                [
                    'slug' => Str::slug($city['name']),
                    'province' => $city['province'],
                    'is_active' => true,
                    'is_featured' => $city['is_featured'],
                    'sort_order' => $city['sort_order'],
                ]
            );
        }
    }
}