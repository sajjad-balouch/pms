<?php

namespace App\Support\Tools;

/**
 * Raqbe ka hisaab (LRMIS / fard ki tarah): kanal - marla - murabba fut.
 * Revenue: 1 marla = 272.25 fut, 1 kanal = 20 marla = 5,445 fut, 1 acre = 8 kanal = 43,560 fut.
 * Society: 1 marla = 225 fut (acre phir bhi 43,560 fut).
 */
class RaqbaMath
{
    public const ACRE_SQFT = 43560.0;

    public const MARLA = ['revenue' => 272.25, 'society' => 225.0];

    public static function marla(string $standard): float
    {
        return self::MARLA[$standard] ?? self::MARLA['revenue'];
    }

    public static function toSqft(float $kanal, float $marla, float $feet, float $ms): float
    {
        return $kanal * 20 * $ms + $marla * $ms + $feet;
    }

    /**
     * Kul fut ko kanal-marla-fut mein. Manfi ho to 'sign' -1 aur baqi qeematein musbat.
     *
     * @return array{sign:int,kanal:int,marla:int,feet:float,marlas:float,sqft:float}
     */
    public static function split(float $sqft, float $ms): array
    {
        $sign = $sqft < -1e-9 ? -1 : 1;
        $abs = round(abs($sqft), 4);
        $kanalSqft = 20 * $ms;

        $kanal = (int) floor($abs / $kanalSqft + 1e-9);
        $rest = $abs - $kanal * $kanalSqft;
        $marla = (int) floor($rest / $ms + 1e-9);
        $feet = round($rest - $marla * $ms, 2);
        // Gol karne se poora marla ban jaye to aage le jao
        if ($feet >= $ms - 0.005) {
            $marla++;
            $feet = 0.0;
        }
        if ($marla >= 20) {
            $kanal++;
            $marla -= 20;
        }

        return ['sign' => $sign, 'kanal' => $kanal, 'marla' => $marla, 'feet' => max(0.0, $feet),
            'marlas' => round($abs / $ms, 4), 'sqft' => round($abs, 2)];
    }

    /**
     * Kul fut ko acre-kanal-marla-fut mein.
     *
     * @return array{sign:int,acre:int,kanal:int,marla:int,feet:float}
     */
    public static function splitAcre(float $sqft, float $ms): array
    {
        $sign = $sqft < -1e-9 ? -1 : 1;
        $abs = round(abs($sqft), 4);
        $acre = (int) floor($abs / self::ACRE_SQFT + 1e-9);
        $rest = self::split($abs - $acre * self::ACRE_SQFT, $ms);
        // Revenue mein 8 kanal = 1 acre; gol karne se 8 kanal ban jayen to acre mein
        if ($rest['kanal'] >= 8 && abs(8 * 20 * $ms - self::ACRE_SQFT) < 0.01) {
            $acre += intdiv($rest['kanal'], 8);
            $rest['kanal'] %= 8;
        }

        return ['sign' => $sign, 'acre' => $acre, 'kanal' => $rest['kanal'], 'marla' => $rest['marla'], 'feet' => $rest['feet']];
    }
}
