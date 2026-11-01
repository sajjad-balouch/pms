<?php

// Sarkari rates ek hi jagah. Budget ya notification ke baad sirf yahan update karein, aur rates_as_of bhi.
return [
    'registry' => [
        'rates_as_of' => '2026-10-31',

        // Punjab: sources mein ikhtilaf hai, is liye user form mein yeh rate badal sakta hai
        'stamp_duty_pct' => 1.0,
        'tma_tax_pct' => 1.0,
        'map_fee_pct' => 2.0,

        'plra_flat_limit' => 3000000,
        'plra_flat_fee' => 3300,
        'plra_pct' => 0.1,
        'registrar_small_limit' => 500000,
        'registrar_small_fee' => 500,
        'registrar_fee' => 1000,
        'mutation_fee' => 700,

        // FBR Tenth Schedule, tax year 2026-27 (rate card se tasdeeq karte rahein)
        'buyer_236k' => [
            'filer' => [[null, 1.25]],
            'non_filer' => [[50000000, 10.5], [100000000, 14.5], [null, 18.5]],
        ],
        'seller_236c' => [
            'filer' => [[null, 2.75]],
            'non_filer' => [[null, 11.5]],
        ],
    ],
];
