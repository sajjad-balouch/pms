<?php

namespace App\Support\Tools;

/**
 * Hanafi faraid (Pakistan mein Sunni ke liye: Muslim Personal Law (Shariat) Application Act 1962).
 *
 * Rishte: shohar/biwi, walidain, dada, aulad, haqeeqi aur allati (sirf baap shareek) behan bhai,
 * aur asaba: bhateeja (haqeeqi bhai ka beta), haqeeqi chacha, chacha zad (haqeeqi chacha ka beta).
 * Dadi/nani, akhyafi (maan shareek) behan bhai aur zawil arham is engine mein nahi; UI par yeh wazeh likha hai.
 *
 * Asaba (baqi) ki tarteeb: beta → walid → dada → haqeeqi bhai → haqeeqi behan (beti ke saath)
 * → allati bhai → allati behan (beti ke saath) → bhateeja → chacha → chacha zad.
 * Koi asaba na ho to radd (shohar/biwi ke siwa); woh bhi na ho to 'unassigned' (zawil arham / spouse / hukoomat).
 *
 * Har heir group ko 'key' milti hai (translation view mein), hisse exact fraction ke float hain.
 */
class InheritanceEngine
{
    public const ORDER = ['husband', 'wife', 'father', 'grandfather', 'mother', 'son', 'daughter', 'brother', 'sister',
        'paternal_brother', 'paternal_sister', 'nephew', 'uncle', 'cousin'];

    /**
     * @param  array{gender:string,wives:int,husband:bool,father:bool,mother:bool,sons:int,daughters:int,brothers:int,sisters:int,grandfather?:bool,paternal_brothers?:int,paternal_sisters?:int,nephews?:int,uncles?:int,cousins?:int}  $h
     * @return array{groups:array<int,array>,awl:bool,radd:bool,unassigned:float}
     */
    public static function calculate(array $h): array
    {
        $h += ['grandfather' => false, 'paternal_brothers' => 0, 'paternal_sisters' => 0, 'nephews' => 0, 'uncles' => 0, 'cousins' => 0];

        $wives = $h['gender'] === 'male' ? $h['wives'] : 0;
        $husband = $h['gender'] === 'female' && $h['husband'];
        $father = $h['father'];
        // Dada sirf walid ki ghair maujoodgi mein, aur usi ki jagah
        $grandfather = ! $father && $h['grandfather'];
        $mother = $h['mother'];
        $sons = $h['sons'];
        $daughters = $h['daughters'];

        $hasChildren = $sons > 0 || $daughters > 0;
        // Maan ka 1/6 do ya zyada behan bhaiyon se, chahe woh khud mahroom hon
        $siblingCount = $h['brothers'] + $h['sisters'] + $h['paternal_brothers'] + $h['paternal_sisters'];

        // Beta, walid ya dada hon to behan bhai mahroom (Imam Abu Hanifa: dada bhi bhaiyon ko rokta hai)
        $siblingsBlocked = $sons > 0 || $father || $grandfather;
        $brothers = $siblingsBlocked ? 0 : $h['brothers'];
        $sisters = $siblingsBlocked ? 0 : $h['sisters'];

        // Allati behan bhai: haqeeqi bhai se, aur beti ke saath asaba haqeeqi behan se mahroom
        $fullSisterWithDaughter = $sisters > 0 && $brothers === 0 && $daughters > 0;
        $paternalBlocked = $siblingsBlocked || $brothers > 0 || $fullSisterWithDaughter;
        $pBrothers = $paternalBlocked ? 0 : $h['paternal_brothers'];
        $pSisters = $paternalBlocked ? 0 : $h['paternal_sisters'];

        $groups = [];
        $add = function (string $key, int $count, float $share, string $basis, bool $spouse = false) use (&$groups) {
            $groups[$key] = compact('key', 'count', 'share', 'basis', 'spouse');
        };

        $spouseShare = 0.0;
        if ($husband) {
            $spouseShare = $hasChildren ? 1 / 4 : 1 / 2;
            $add('husband', 1, $spouseShare, $hasChildren ? '1/4' : '1/2', true);
        } elseif ($wives > 0) {
            $spouseShare = $hasChildren ? 1 / 8 : 1 / 4;
            $add('wife', $wives, $spouseShare, $hasChildren ? '1/8' : '1/4', true);
        }

        if ($mother) {
            if ($hasChildren || $siblingCount >= 2) {
                $add('mother', 1, 1 / 6, '1/6');
            } elseif ($father && $spouseShare > 0) {
                // Umariyatain: spouse + walidain, aulad nahi: maan ko baqi ka 1/3 (dada ke saath nahi)
                $add('mother', 1, (1 - $spouseShare) / 3, '1/3 of remainder');
            } else {
                $add('mother', 1, 1 / 3, '1/3');
            }
        }

        if ($father && $hasChildren) {
            $add('father', 1, 1 / 6, '1/6');
        } elseif ($grandfather && $hasChildren) {
            $add('grandfather', 1, 1 / 6, '1/6');
        }

        if ($sons === 0 && $daughters > 0) {
            $add('daughter', $daughters, $daughters === 1 ? 1 / 2 : 2 / 3, $daughters === 1 ? '1/2' : '2/3');
        }

        // Asaba bil ghair se pehle sirf behnon ke muqarrar hisse (na bhai, na beti)
        if ($sisters > 0 && $brothers === 0 && $daughters === 0) {
            $add('sister', $sisters, $sisters === 1 ? 1 / 2 : 2 / 3, $sisters === 1 ? '1/2' : '2/3');
        }

        // Allati behnein: haqeeqi behan na ho to 1/2 ya 2/3; ek haqeeqi behan ho to takmila 1/6; do hon to kuch nahi
        if ($pSisters > 0 && $pBrothers === 0 && $daughters === 0) {
            if ($sisters === 0) {
                $add('paternal_sister', $pSisters, $pSisters === 1 ? 1 / 2 : 2 / 3, $pSisters === 1 ? '1/2' : '2/3');
            } elseif ($sisters === 1) {
                $add('paternal_sister', $pSisters, 1 / 6, '1/6 (completing 2/3)');
            }
        }

        $fixed = array_sum(array_column($groups, 'share'));
        $awl = $fixed > 1 + 1e-9;
        if ($awl) {
            // 'Awl: hisse poore 100% se barh jayen to sab tanasub se kam
            foreach ($groups as $k => $g) {
                $groups[$k]['share'] = $g['share'] / $fixed;
            }
            $fixed = 1.0;
        }

        $residue = max(0.0, 1 - $fixed);
        $radd = false;
        $unassigned = 0.0;

        // Mard aur aurat asaba: mard ko do, aurat ko ek hissa
        $pair = function (string $maleKey, int $males, string $femaleKey, int $females) use (&$add, $residue) {
            $units = $males * 2 + $females;
            $add($maleKey, $males, $residue * ($males * 2) / $units, 'Residue (2 parts each)');
            if ($females > 0) {
                $add($femaleKey, $females, $residue * $females / $units, 'Residue (1 part each)');
            }
        };
        $topUp = function (string $key, string $basis) use (&$groups, $residue) {
            $groups[$key] = isset($groups[$key])
                ? array_merge($groups[$key], ['share' => $groups[$key]['share'] + $residue, 'basis' => $groups[$key]['basis'] . ' + residue'])
                : ['key' => $key, 'count' => 1, 'share' => $residue, 'basis' => $basis, 'spouse' => false];
        };

        if ($residue > 1e-9) {
            if ($sons > 0) {
                $pair('son', $sons, 'daughter', $daughters);
            } elseif ($father) {
                $topUp('father', 'Residue');
            } elseif ($grandfather) {
                $topUp('grandfather', 'Residue');
            } elseif ($brothers > 0) {
                $pair('brother', $brothers, 'sister', $sisters);
            } elseif ($fullSisterWithDaughter) {
                // Beti ke saath behan asaba ma'al ghair
                $add('sister', $sisters, $residue, 'Residue with daughter');
            } elseif ($pBrothers > 0) {
                $pair('paternal_brother', $pBrothers, 'paternal_sister', $pSisters);
            } elseif ($pSisters > 0 && $daughters > 0) {
                $add('paternal_sister', $pSisters, $residue, 'Residue with daughter');
            } elseif ($h['nephews'] > 0) {
                $add('nephew', $h['nephews'], $residue, 'Residue');
            } elseif ($h['uncles'] > 0) {
                $add('uncle', $h['uncles'], $residue, 'Residue');
            } elseif ($h['cousins'] > 0) {
                $add('cousin', $h['cousins'], $residue, 'Residue');
            } else {
                // Radd: baqi spouse ke siwa fixed hisse daron ko tanasub se
                $nonSpouse = array_filter($groups, fn ($g) => ! $g['spouse']);
                $base = array_sum(array_column($nonSpouse, 'share'));
                if ($base > 0) {
                    foreach ($nonSpouse as $k => $g) {
                        $groups[$k]['share'] += $residue * $g['share'] / $base;
                        $groups[$k]['basis'] .= ' + radd';
                    }
                    $radd = true;
                } else {
                    // Sirf spouse ya koi heir nahi: zawil arham, phir spouse (radd), phir hukoomat
                    $unassigned = $residue;
                }
            }
        }

        $sorted = [];
        foreach (self::ORDER as $k) {
            if (isset($groups[$k])) {
                $sorted[] = $groups[$k];
            }
        }

        return ['groups' => $sorted, 'awl' => $awl, 'radd' => $radd, 'unassigned' => $unassigned];
    }
}
