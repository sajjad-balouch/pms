<?php

namespace App\Support;

class PlotPricing
{
    const MARLAS_PER_KANAL = 20;

    // Premium % added on top of the base price. Apni marzi ke mutabiq badal lein.
    const FEATURES = [
        'corner' => ['label' => 'Corner', 'premium' => 10],
        'park_facing' => ['label' => 'Park Facing', 'premium' => 10],
        'main_boulevard' => ['label' => 'Main Boulevard', 'premium' => 15],
    ];

    const SIZES = [
        '1 Marla', '2 Marla', '3 Marla', '4 Marla', '5 Marla', '6 Marla', '7 Marla', '8 Marla', '9 Marla', '10 Marla',
        '11 Marla', '12 Marla', '13 Marla', '14 Marla', '15 Marla', '16 Marla', '17 Marla', '18 Marla', '19 Marla',
        '1 Kanal', '2 Kanal',
    ];

    // "5 Marla" => 5, "1 Kanal" => 20, unknown => 0
    public static function marlas(?string $size): float
    {
        if (! $size || ! preg_match('/([\d.]+)\s*(marla|kanal)/i', $size, $m)) {
            return 0;
        }

        return (float) $m[1] * (strtolower($m[2]) === 'kanal' ? self::MARLAS_PER_KANAL : 1);
    }

    public static function cleanFeatures($features): array
    {
        return array_values(array_intersect(array_keys(self::FEATURES), (array) $features));
    }

    public static function premiumPercent($features): float
    {
        return collect(self::cleanFeatures($features))->sum(fn ($f) => self::FEATURES[$f]['premium']);
    }

    public static function totalPrice($basePrice, $features): float
    {
        return round((float) $basePrice * (1 + self::premiumPercent($features) / 100));
    }

    // For old plots that only have total_price saved
    public static function baseFromTotal($totalPrice, $features): float
    {
        return round((float) $totalPrice / (1 + self::premiumPercent($features) / 100));
    }

    public static function featureLabels($features): array
    {
        return array_map(fn ($f) => self::FEATURES[$f]['label'], self::cleanFeatures($features));
    }
}
