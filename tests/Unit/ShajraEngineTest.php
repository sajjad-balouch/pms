<?php

namespace Tests\Unit;

use App\Support\Tools\ShajraEngine;
use PHPUnit\Framework\TestCase;

class ShajraEngineTest extends TestCase
{
    private function p(string $id, string $parent, string $relation, string $name, string $status = 'alive', string $cnic = ''): array
    {
        return compact('id', 'parent', 'relation', 'name', 'status', 'cnic');
    }

    private function byName(array $r): array
    {
        return array_column($r['holders'], null, 'name');
    }

    public function test_simple_family_shares_and_common_denominator(): void
    {
        // Biwi 1/8, 2 bete 7/8 mein se 2:2, 1 beti 1 -> 40 hisse
        $r = ShajraEngine::build('male', [
            $this->p('a', 'root', 'wife', 'Zainab'),
            $this->p('b', 'root', 'son', 'Ali'),
            $this->p('c', 'root', 'son', 'Umar'),
            $this->p('d', 'root', 'daughter', 'Fatima'),
        ], 160);

        $h = $this->byName($r);
        $this->assertSame('1/8', $h['Zainab']['fraction']);
        $this->assertSame('7/20', $h['Ali']['fraction']);
        $this->assertSame('7/40', $h['Fatima']['fraction']);
        $this->assertSame(40, $r['total_parts']);
        $this->assertSame(5, $h['Zainab']['parts']);
        $this->assertSame(1, $h['Zainab']['kanal']); // 160 marla ka 1/8 = 20 marla
        $this->assertSame(0, $h['Zainab']['marla']);
        $this->assertEqualsWithDelta(100, $r['total_pct'], 1e-6);
    }

    public function test_munasakha_heir_dying_later_passes_share_on(): void
    {
        // Baap: biwi + 1 beta + 1 beti. Beta baad mein fout: us ki maan (wohi biwi) + us ki biwi + us ka beta
        $r = ShajraEngine::build('male', [
            $this->p('w', 'root', 'wife', 'Zainab', 'alive', '35202-1111111-2'),
            $this->p('s', 'root', 'son', 'Ali', 'died_after'),
            $this->p('d', 'root', 'daughter', 'Fatima'),
            $this->p('sm', 's', 'mother', 'Zainab', 'alive', '35202-1111111-2'),
            $this->p('sw', 's', 'wife', 'Ayesha'),
            $this->p('ss', 's', 'son', 'Hamza'),
        ], 0);

        $h = $this->byName($r);
        // Ali ko 7/12; us mein se maan 1/6, biwi 1/8, beta baqi 17/24
        $this->assertSame('7/12', $r['nodes']['s']['fraction']);
        $this->assertEqualsWithDelta(1 / 8 + 7 / 12 / 6, $h['Zainab']['share'], 1e-12);
        $this->assertCount(2, $h['Zainab']['via']); // dono raston se mila, ek hi row
        $this->assertEqualsWithDelta(7 / 12 / 8, $h['Ayesha']['share'], 1e-12);
        $this->assertEqualsWithDelta(7 / 12 * 17 / 24, $h['Hamza']['share'], 1e-12);
        $this->assertArrayNotHasKey('Ali', $h);
        $this->assertEqualsWithDelta(100, $r['total_pct'], 1e-6);
    }

    public function test_predeceased_son_children_take_his_share_under_mflo(): void
    {
        // 2 bete, ek pehle fout; us ke 1 beta 1 beti -> 1/2 ko 2:1
        $r = ShajraEngine::build('male', [
            $this->p('a', 'root', 'son', 'Ali'),
            $this->p('b', 'root', 'son', 'Umar', 'died_before'),
            $this->p('b1', 'b', 'son', 'Bilal'),
            $this->p('b2', 'b', 'daughter', 'Sana'),
            $this->p('x', 'root', 'brother', 'Karim'),
        ]);

        $h = $this->byName($r);
        $this->assertSame('1/2', $h['Ali']['fraction']);
        $this->assertSame('1/3', $h['Bilal']['fraction']);
        $this->assertSame('1/6', $h['Sana']['fraction']);
        $this->assertSame('blocked', $r['nodes']['x']['state']); // bete ke hote bhai mahroom
        $this->assertSame('mflo', $r['nodes']['b']['state']);
    }

    public function test_predeceased_without_children_and_other_relations_get_nothing(): void
    {
        $r = ShajraEngine::build('male', [
            $this->p('a', 'root', 'son', 'Ali'),
            $this->p('b', 'root', 'son', 'Umar', 'died_before'),
            $this->p('m', 'root', 'mother', 'Hajra', 'died_before'),
        ]);

        $this->assertSame('1/1', $this->byName($r)['Ali']['fraction']);
        $this->assertSame('predeceased', $r['nodes']['b']['state']);
        $this->assertSame('predeceased', $r['nodes']['m']['state']);
    }

    public function test_sons_of_predeceased_brother_inherit_as_nephews(): void
    {
        // Betiyan 2/3; bhai pehle fout, us ke bete bhateeje ban kar baqi 1/3 lete hain, beti (bhateeji) nahi
        $r = ShajraEngine::build('male', [
            $this->p('d1', 'root', 'daughter', 'Amna'),
            $this->p('d2', 'root', 'daughter', 'Maryam'),
            $this->p('b', 'root', 'brother', 'Karim', 'died_before'),
            $this->p('bw', 'b', 'wife', 'Bano'),
            $this->p('n1', 'b', 'son', 'Saqib'),
            $this->p('n2', 'b', 'son', 'Raheel'),
            $this->p('nd', 'b', 'daughter', 'Hina'),
            $this->p('u', 'root', 'uncle', 'Rasheed'),
        ]);

        $h = $this->byName($r);
        $this->assertSame('1/3', $h['Amna']['fraction']);
        $this->assertSame('1/6', $h['Saqib']['fraction']);
        $this->assertSame('1/6', $h['Raheel']['fraction']);
        $this->assertArrayNotHasKey('Hina', $h);
        $this->assertArrayNotHasKey('Bano', $h);
        $this->assertSame('blocked', $r['nodes']['u']['state']);
        $this->assertSame('nephew', end($h['Saqib']['via'][0])['relation']);
    }

    public function test_heir_who_died_later_without_heirs_is_reported(): void
    {
        $r = ShajraEngine::build('female', [
            $this->p('h', 'root', 'husband', 'Aslam'),
            $this->p('d', 'root', 'daughter', 'Sana', 'died_after'),
        ]);

        $this->assertEqualsWithDelta(75, $r['unassigned_pct'], 1e-6);
        $this->assertContains(['key' => 'no_heirs_sub'], $r['warnings']);
    }

    public function test_more_than_four_wives_is_flagged(): void
    {
        $people = [];
        foreach (range(1, 5) as $i) {
            $people[] = $this->p("w$i", 'root', 'wife', "W$i");
        }
        $r = ShajraEngine::build('male', $people);

        $this->assertSame('invalid', $r['nodes']['w5']['state']);
        $this->assertSame(['key' => 'limit', 'relation' => 'wife', 'max' => 4], $r['warnings'][0]);
    }

    public function test_fraction_recovers_exact_ratios(): void
    {
        $this->assertSame([7, 288], ShajraEngine::fraction(7 / 288));
        $this->assertSame([17, 81], ShajraEngine::fraction(17 / 81));
    }
}
