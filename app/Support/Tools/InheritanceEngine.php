<?php

namespace App\Support\Tools;

/**
 * Hanafi faraid: spouse, parents, children aur real (full) behan bhai.
 * Baqi rishte (poté, dada, sautele behan bhai) is engine mein nahi; UI par yeh wazeh likha hai.
 *
 * Har heir group ko 'key' milti hai (translation view mein), hisse exact fraction ke float hain.
 */
class InheritanceEngine
{
    /**
     * @param  array{gender:string,wives:int,husband:bool,father:bool,mother:bool,sons:int,daughters:int,brothers:int,sisters:int}  $h
     * @return array{groups:array<int,array>,awl:bool,radd:bool,unassigned:float}
     */
    public static function calculate(array $h): array
    {
        $wives = $h['gender'] === 'male' ? $h['wives'] : 0;
        $husband = $h['gender'] === 'female' && $h['husband'];
        $father = $h['father'];
        $mother = $h['mother'];
        $sons = $h['sons'];
        $daughters = $h['daughters'];

        $hasChildren = $sons > 0 || $daughters > 0;
        $siblingCount = $h['brothers'] + $h['sisters'];

        // Beta ya baap hon to behan bhai mahroom. (Mother ka 1/6 phir bhi unki ginti se banta hai.)
        $siblingsBlocked = $sons > 0 || $father;
        $brothers = $siblingsBlocked ? 0 : $h['brothers'];
        $sisters = $siblingsBlocked ? 0 : $h['sisters'];

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
                // Umariyatain: spouse + walidain, aulad nahi: maan ko baqi ka 1/3
                $add('mother', 1, (1 - $spouseShare) / 3, '1/3 of remainder');
            } else {
                $add('mother', 1, 1 / 3, '1/3');
            }
        }

        if ($father && $hasChildren) {
            $add('father', 1, 1 / 6, '1/6');
        }

        if ($sons === 0 && $daughters > 0) {
            $add('daughter', $daughters, $daughters === 1 ? 1 / 2 : 2 / 3, $daughters === 1 ? '1/2' : '2/3');
        }

        // Asaba bil ghair se pehle sirf behnon ke muqarrar hisse (na bhai, na beti)
        if ($sisters > 0 && $brothers === 0 && $daughters === 0) {
            $add('sister', $sisters, $sisters === 1 ? 1 / 2 : 2 / 3, $sisters === 1 ? '1/2' : '2/3');
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

        if ($residue > 1e-9) {
            if ($sons > 0) {
                $units = $sons * 2 + $daughters;
                $add('son', $sons, $residue * ($sons * 2) / $units, 'Residue (2 parts each)');
                if ($daughters > 0) {
                    $add('daughter', $daughters, $residue * $daughters / $units, 'Residue (1 part each)');
                }
            } elseif ($father) {
                $groups['father'] = isset($groups['father'])
                    ? array_merge($groups['father'], ['share' => $groups['father']['share'] + $residue, 'basis' => '1/6 + residue'])
                    : ['key' => 'father', 'count' => 1, 'share' => $residue, 'basis' => 'Residue', 'spouse' => false];
            } elseif ($brothers > 0) {
                $units = $brothers * 2 + $sisters;
                $add('brother', $brothers, $residue * ($brothers * 2) / $units, 'Residue (2 parts each)');
                if ($sisters > 0) {
                    $add('sister', $sisters, $residue * $sisters / $units, 'Residue (1 part each)');
                }
            } elseif ($sisters > 0 && $daughters > 0) {
                // Beti ke saath behan asaba ma'al ghair
                $add('sister', $sisters, $residue, 'Residue with daughter');
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
                    // Sirf spouse ya koi heir nahi: engine ke rishton mein koi hisse dar nahi
                    $unassigned = $residue;
                }
            }
        }

        $order = ['husband', 'wife', 'father', 'mother', 'son', 'daughter', 'brother', 'sister'];
        $sorted = [];
        foreach ($order as $k) {
            if (isset($groups[$k])) {
                $sorted[] = $groups[$k];
            }
        }

        return ['groups' => $sorted, 'awl' => $awl, 'radd' => $radd, 'unassigned' => $unassigned];
    }
}
