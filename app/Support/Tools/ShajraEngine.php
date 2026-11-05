<?php

namespace App\Support\Tools;

/**
 * Shajra nasab (wirasat) ka hisaab: mutawaffi ke warisan ka darakht aur har waris ka hissa.
 *
 * - Har marhoom (root ya woh waris jo baad mein fout hua, munasakha) ke apne warisan
 *   InheritanceEngine (Hanafi) se hissa lete hain.
 * - Jo beta/beti mutawaffi se PEHLE fout hua, us ki aulad MFLO 1961 dafa 4 ke tehat
 *   us ka hissa leti hai (larka 2, larki 1).
 * - Baqi rishte jo pehle fout hue, waris nahi.
 *
 * Node: ['id','parent','name','relation','status','cnic']; root ka id 'root' hai.
 * status: alive | died_after | died_before | missing (mafqood, hissa mehfooz) | divorced (sirf shohar/biwi, hissa nahi)
 * Ikhtiyari: 'mother' (aulad kis biwi se, wife node id), 'minor', 'guardian'.
 */
class ShajraEngine
{
    public const RELATIONS = ['wife', 'husband', 'son', 'daughter', 'father', 'mother', 'brother', 'sister',
        'grandfather', 'paternal_brother', 'paternal_sister', 'nephew', 'uncle', 'cousin'];

    public const MALE = ['husband', 'son', 'father', 'brother', 'grandfather', 'paternal_brother', 'nephew', 'uncle', 'cousin'];

    /** Ek marhoom ke neeche har rishte ki had */
    public const LIMITS = ['wife' => 4, 'husband' => 1, 'father' => 1, 'mother' => 1, 'grandfather' => 1];

    public const MAX_DEPTH = 6;

    private array $byParent = [];

    private array $info = [];

    private array $holders = [];

    private array $warnings = [];

    private float $unassigned = 0.0;

    public static function genderOf(string $relation): string
    {
        return in_array($relation, self::MALE, true) ? 'male' : 'female';
    }

    /** Is marhoom ke neeche kaun se rishte jor sakte hain */
    public static function allowedRelations(string $parentGender, string $parentStatus): array
    {
        // Pehle fout: sirf us ki aulad aur shohar/biwi (shajre ke liye; hissa sirf qanooni raste se)
        if ($parentStatus === 'died_before') {
            return ['son', 'daughter', $parentGender === 'male' ? 'wife' : 'husband'];
        }

        return array_values(array_filter(self::RELATIONS, fn ($r) => $r !== ($parentGender === 'male' ? 'husband' : 'wife')));
    }

    /**
     * @param  string  $rootGender  male|female
     * @param  array<int,array>  $people
     * @param  float  $totalMarlas  kul raqba marlon mein (sarsahi = 1/9 marla)
     */
    public static function build(string $rootGender, array $people, float $totalMarlas = 0): array
    {
        return (new self)->run($rootGender === 'female' ? 'female' : 'male', $people, max(0.0, $totalMarlas));
    }

    private function run(string $rootGender, array $people, float $totalMarlas): array
    {
        $ids = ['root' => true];
        foreach ($people as $p) {
            $ids[$p['id']] = true;
        }
        foreach ($people as $p) {
            // Jis ka parent hi nahi, woh darakht se bahar hai
            if (isset($ids[$p['parent']]) && $p['parent'] !== $p['id']) {
                $this->byParent[$p['parent']][] = $p;
            }
        }

        $this->info['root'] = ['share' => 1.0, 'basis' => '', 'state' => 'estate'];
        $this->distribute('root', $rootGender, 'estate', 1.0, [], 0);

        $holders = array_values($this->holders);
        foreach ($holders as &$h) {
            $h += $this->landOf($h['share'], $totalMarlas);
        }
        unset($h);

        $fractions = array_map(fn ($h) => self::fraction($h['share']), $holders);
        $lcm = 1;
        foreach ($fractions as [, $den]) {
            $lcm = self::lcm($lcm, $den);
        }
        // Bohat bara mukhraj patwari ke kaam ka nahi; tab sirf kasr dikhate hain
        $useLcm = $lcm <= 10_000_000;
        foreach ($holders as $i => &$h) {
            [$num, $den] = $fractions[$i];
            $h['fraction'] = $num . '/' . $den;
            $h['parts'] = $useLcm ? intdiv($lcm, $den) * $num : null;
            $h['percentage'] = round($h['share'] * 100, 4);
        }
        unset($h);

        foreach ($this->info as $id => &$row) {
            if ($row['share'] > 0) {
                [$n, $d] = self::fraction($row['share']);
                $row['fraction'] = $n . '/' . $d;
            } else {
                $row['fraction'] = '0';
            }
        }
        unset($row);

        $total = array_sum(array_column($holders, 'share'));

        return [
            'holders' => $holders,
            'nodes' => $this->info,
            'total_parts' => $useLcm ? $lcm : null,
            'unassigned_pct' => round($this->unassigned * 100, 4),
            'total_pct' => round($total * 100, 4),
            'warnings' => array_values(array_unique($this->warnings, SORT_REGULAR)),
            'children' => $this->byParent,
        ];
    }

    /**
     * @param  string  $mode  estate (Hanafi warasat) | mflo (pehle fout aulad ka hissa us ki aulad ko)
     */
    private function distribute(string $id, string $gender, string $mode, float $fraction, array $chain, int $depth): void
    {
        $kids = $this->byParent[$id] ?? [];

        if ($depth >= self::MAX_DEPTH) {
            $this->leaveUnassigned($fraction, 'too_deep');
            foreach ($kids as $k) {
                $this->mark($k, 0.0, '', 'too_deep');
            }

            return;
        }

        if ($mode === 'mflo') {
            $this->distributeMflo($id, $kids, $fraction, $chain, $depth);

            return;
        }

        $allowed = self::allowedRelations($gender, 'alive');
        $members = array_fill_keys(self::RELATIONS, []);
        foreach ($kids as $k) {
            if (! in_array($k['relation'], $allowed, true)) {
                $this->mark($k, 0.0, '', 'invalid');
                continue;
            }
            if ($k['status'] === 'divorced') {
                // Talaq (iddat khatam) ke baad shohar/biwi waris nahi
                $this->mark($k, 0.0, '', 'divorced');
                continue;
            }
            if ($k['status'] === 'died_before') {
                // Sirf beta/beti, aur woh bhi tab jab us ki aulad darj ho (MFLO dafa 4)
                if (in_array($k['relation'], ['son', 'daughter'], true) && $this->hasLivingLine($k['id'], 0)) {
                    $members[$k['relation']][] = $k;
                } else {
                    $this->mark($k, 0.0, '', 'predeceased');
                    // Pehle fout haqeeqi bhai ke bete = bhateeje, haqeeqi chacha ke bete = chacha zad (dono asaba).
                    // Un ki betiyan zawil arham hain, hissa nahi.
                    $as = ['brother' => 'nephew', 'uncle' => 'cousin'][$k['relation']] ?? null;
                    foreach ($as ? ($this->byParent[$k['id']] ?? []) : [] as $g) {
                        if ($g['relation'] === 'son' && in_array($g['status'], ['alive', 'died_after', 'missing'], true)) {
                            $members[$as][] = array_merge($g, ['relation' => $as]);
                        }
                    }
                }
                continue;
            }
            $members[$k['relation']][] = $k;
        }

        foreach (self::LIMITS as $rel => $max) {
            if (count($members[$rel]) > $max) {
                $this->warnings[] = ['key' => 'limit', 'relation' => $rel, 'max' => $max];
                foreach (array_slice($members[$rel], $max) as $extra) {
                    $this->mark($extra, 0.0, '', 'invalid');
                }
                $members[$rel] = array_slice($members[$rel], 0, $max);
            }
        }

        $hasAny = array_filter($members);
        if (! $hasAny) {
            $this->leaveUnassigned($fraction, $id === 'root' ? 'no_heirs' : 'no_heirs_sub');

            return;
        }

        $result = InheritanceEngine::calculate([
            'gender' => $gender,
            'wives' => count($members['wife']),
            'husband' => count($members['husband']) > 0,
            'father' => count($members['father']) > 0,
            'mother' => count($members['mother']) > 0,
            'sons' => count($members['son']),
            'daughters' => count($members['daughter']),
            'brothers' => count($members['brother']),
            'sisters' => count($members['sister']),
            'grandfather' => count($members['grandfather']) > 0,
            'paternal_brothers' => count($members['paternal_brother']),
            'paternal_sisters' => count($members['paternal_sister']),
            'nephews' => count($members['nephew']),
            'uncles' => count($members['uncle']),
            'cousins' => count($members['cousin']),
        ]);

        $got = [];
        foreach ($result['groups'] as $g) {
            $list = $members[$g['key']];
            foreach ($list as $k) {
                $got[$k['id']] = true;
                $this->give($k, $fraction * $g['share'] / count($list), $g['basis'], $chain, $depth);
            }
        }

        // Jin ko kuch na mila (maslan beta ho to bhai mahjoob)
        foreach ($members as $list) {
            foreach ($list as $k) {
                if (! isset($got[$k['id']])) {
                    $this->mark($k, 0.0, '', 'blocked');
                }
            }
        }

        if ($result['unassigned'] > 1e-12) {
            $this->leaveUnassigned($fraction * $result['unassigned'], 'unassigned');
        }
    }

    private function distributeMflo(string $id, array $kids, float $fraction, array $chain, int $depth): void
    {
        $line = [];
        foreach ($kids as $k) {
            if (! in_array($k['relation'], ['son', 'daughter'], true)) {
                // Pehle fout bete ki bewa: shajre mein dikhti hai, MFLO dafa 4 mein hissa sirf aulad ka
                $this->mark($k, 0.0, '', 'no_share');
            } elseif ($k['status'] === 'died_before' && ! $this->hasLivingLine($k['id'], 0)) {
                $this->mark($k, 0.0, '', 'predeceased');
            } else {
                $line[] = $k;
            }
        }

        $units = 0;
        foreach ($line as $k) {
            $units += $k['relation'] === 'son' ? 2 : 1;
        }
        if ($units === 0) {
            $this->leaveUnassigned($fraction, 'no_heirs_sub');

            return;
        }

        foreach ($line as $k) {
            $w = $k['relation'] === 'son' ? 2 : 1;
            $this->give($k, $fraction * $w / $units, 'MFLO s.4', $chain, $depth);
        }
    }

    private function give(array $k, float $share, string $basis, array $chain, int $depth): void
    {
        // Kis raste se hissa mila: har qadam par rishta aur naam
        $chain[] = ['relation' => $k['relation'], 'name' => $k['name']];

        // Mafqood-ul-khabar ka hissa us ke liye mehfooz rakha jata hai, is liye hissa dar ki tarah
        if (in_array($k['status'], ['alive', 'missing'], true)) {
            $this->mark($k, $share, $basis, 'holder');
            $key = trim((string) ($k['cnic'] ?? '')) !== ''
                ? 'c:' . preg_replace('/\D/', '', $k['cnic'])
                : 'n:' . mb_strtolower(trim((string) $k['name']));
            if ($key === 'n:') {
                $key = 'id:' . $k['id'];
            }
            if (isset($this->holders[$key])) {
                $this->holders[$key]['share'] += $share;
                $this->holders[$key]['via'][] = $chain;
            } else {
                $this->holders[$key] = [
                    'id' => $k['id'],
                    'name' => $k['name'],
                    'cnic' => $k['cnic'] ?? '',
                    'relation' => $k['relation'],
                    'share' => $share,
                    'via' => [$chain],
                    'missing' => $k['status'] === 'missing',
                    'minor' => ! empty($k['minor']),
                    'guardian' => (string) ($k['guardian'] ?? ''),
                ];
            }

            return;
        }

        if ($k['status'] === 'died_after') {
            $this->mark($k, $share, $basis, 'passed_on');
            $this->distribute($k['id'], self::genderOf($k['relation']), 'estate', $share, $chain, $depth + 1);

            return;
        }

        // died_before beta/beti: MFLO
        $this->mark($k, $share, $basis, 'mflo');
        $this->distribute($k['id'], self::genderOf($k['relation']), 'mflo', $share, $chain, $depth + 1);
    }

    private function hasLivingLine(string $id, int $depth): bool
    {
        if ($depth > self::MAX_DEPTH) {
            return false;
        }
        foreach ($this->byParent[$id] ?? [] as $k) {
            if (! in_array($k['relation'], ['son', 'daughter'], true)) {
                continue;
            }
            if ($k['status'] !== 'died_before' || $this->hasLivingLine($k['id'], $depth + 1)) {
                return true;
            }
        }

        return false;
    }

    private function mark(array $k, float $share, string $basis, string $state): void
    {
        $this->info[$k['id']] = compact('share', 'basis', 'state');
        // Mahroom/ghalat node ke neeche walon ko bhi kuch nahi
        if ($share <= 0) {
            foreach ($this->byParent[$k['id']] ?? [] as $c) {
                if (! isset($this->info[$c['id']])) {
                    $this->mark($c, 0.0, '', 'no_share');
                }
            }
        }
    }

    private function leaveUnassigned(float $fraction, string $reason): void
    {
        $this->unassigned += $fraction;
        $this->warnings[] = ['key' => $reason];
    }

    /** Raqba: kanal (20 marla), marla, sarsahi (1 marla = 9 sarsahi) */
    private function landOf(float $share, float $totalMarlas): array
    {
        $totalSarsahi = $share * $totalMarlas * 9;
        $kanal = (int) floor($totalSarsahi / 180 + 1e-9);
        $rest = $totalSarsahi - $kanal * 180;
        $marla = (int) floor($rest / 9 + 1e-9);
        $sarsahi = round($rest - $marla * 9, 2);
        if ($sarsahi >= 9) {
            $marla++;
            $sarsahi = 0.0;
        }
        if ($marla >= 20) {
            $kanal++;
            $marla -= 20;
        }

        return ['kanal' => $kanal, 'marla' => $marla, 'sarsahi' => $sarsahi, 'marlas_total' => round($share * $totalMarlas, 4)];
    }

    /** Float ko sab se qareeb chhoti kasr mein (continued fraction) */
    public static function fraction(float $x, int $maxDen = 100_000_000): array
    {
        if ($x <= 0) {
            return [0, 1];
        }
        [$h0, $h1, $k0, $k1] = [0, 1, 1, 0];
        $v = $x;
        for ($i = 0; $i < 64; $i++) {
            $a = (int) floor($v);
            [$h0, $h1] = [$h1, $a * $h1 + $h0];
            [$k0, $k1] = [$k1, $a * $k1 + $k0];
            if ($k1 > $maxDen) {
                [$h1, $k1] = [$h0, $k0];
                break;
            }
            if (abs($x - $h1 / $k1) < 1e-12 * max(1, $x)) {
                break;
            }
            $frac = $v - $a;
            if ($frac < 1e-15) {
                break;
            }
            $v = 1 / $frac;
        }

        return [$h1, $k1];
    }

    private static function lcm(int $a, int $b): int
    {
        [$x, $y] = [$a, $b];
        while ($y) {
            [$x, $y] = [$y, $x % $y];
        }
        $cap = intdiv(PHP_INT_MAX, 4);
        $q = intdiv($a, $x);

        return $q > intdiv($cap, max(1, $b)) ? $cap : $q * $b;
    }
}
