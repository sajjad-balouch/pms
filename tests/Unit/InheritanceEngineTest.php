<?php

namespace Tests\Unit;

use App\Support\Tools\InheritanceEngine;
use PHPUnit\Framework\TestCase;

class InheritanceEngineTest extends TestCase
{
    private function shares(array $h): array
    {
        $r = InheritanceEngine::calculate($h + ['gender' => 'male', 'wives' => 0, 'husband' => false, 'father' => false, 'mother' => false, 'sons' => 0, 'daughters' => 0, 'brothers' => 0, 'sisters' => 0]);

        return $r + ['map' => array_column($r['groups'], 'share', 'key')];
    }

    private function assertTotalIsWhole(array $r): void
    {
        $this->assertEqualsWithDelta(1.0, array_sum($r['map']) + $r['unassigned'], 1e-9);
    }

    public function test_wife_parents_two_sons_three_daughters(): void
    {
        $r = $this->shares(['wives' => 1, 'father' => true, 'mother' => true, 'sons' => 2, 'daughters' => 3]);
        $this->assertEqualsWithDelta(1 / 8, $r['map']['wife'], 1e-9);
        $this->assertEqualsWithDelta(1 / 6, $r['map']['father'], 1e-9);
        $this->assertEqualsWithDelta(1 / 6, $r['map']['mother'], 1e-9);
        $this->assertEqualsWithDelta($r['map']['son'] / 2 * 3 / 2, $r['map']['daughter'], 1e-9); // 4 parts : 3 parts
        $this->assertTotalIsWhole($r);
    }

    public function test_awl_husband_parents_two_daughters(): void
    {
        // 3/12 + 2/12 + 2/12 + 8/12 = 15/12, 'awl se 15 par
        $r = $this->shares(['gender' => 'female', 'husband' => true, 'father' => true, 'mother' => true, 'daughters' => 2]);
        $this->assertTrue($r['awl']);
        $this->assertEqualsWithDelta(3 / 15, $r['map']['husband'], 1e-9);
        $this->assertEqualsWithDelta(8 / 15, $r['map']['daughter'], 1e-9);
        $this->assertTotalIsWhole($r);
    }

    public function test_umariyatain_mother_gets_third_of_remainder(): void
    {
        $r = $this->shares(['gender' => 'female', 'husband' => true, 'father' => true, 'mother' => true]);
        $this->assertEqualsWithDelta(1 / 2, $r['map']['husband'], 1e-9);
        $this->assertEqualsWithDelta(1 / 6, $r['map']['mother'], 1e-9);
        $this->assertEqualsWithDelta(1 / 3, $r['map']['father'], 1e-9);
    }

    public function test_one_daughter_and_father(): void
    {
        $r = $this->shares(['father' => true, 'daughters' => 1]);
        $this->assertEqualsWithDelta(1 / 2, $r['map']['daughter'], 1e-9);
        $this->assertEqualsWithDelta(1 / 2, $r['map']['father'], 1e-9);
    }

    public function test_radd_excludes_spouse(): void
    {
        // Biwi 1/8, beti 1/2; baqi 3/8 sirf beti ko
        $r = $this->shares(['wives' => 1, 'daughters' => 1]);
        $this->assertTrue($r['radd']);
        $this->assertEqualsWithDelta(1 / 8, $r['map']['wife'], 1e-9);
        $this->assertEqualsWithDelta(7 / 8, $r['map']['daughter'], 1e-9);
    }

    public function test_siblings_reduce_mother_but_father_blocks_them(): void
    {
        $r = $this->shares(['mother' => true, 'father' => true, 'brothers' => 2]);
        $this->assertEqualsWithDelta(1 / 6, $r['map']['mother'], 1e-9);
        $this->assertEqualsWithDelta(5 / 6, $r['map']['father'], 1e-9);
        $this->assertArrayNotHasKey('brother', $r['map']);
    }

    public function test_brother_and_sister_share_two_to_one(): void
    {
        $r = $this->shares(['brothers' => 1, 'sisters' => 1]);
        $this->assertEqualsWithDelta(2 / 3, $r['map']['brother'], 1e-9);
        $this->assertEqualsWithDelta(1 / 3, $r['map']['sister'], 1e-9);
    }

    public function test_sister_takes_residue_with_daughter(): void
    {
        $r = $this->shares(['daughters' => 1, 'sisters' => 1]);
        $this->assertEqualsWithDelta(1 / 2, $r['map']['sister'], 1e-9);
    }

    public function test_only_wife_reports_unassigned_remainder(): void
    {
        $r = $this->shares(['wives' => 2]);
        $this->assertEqualsWithDelta(1 / 4, $r['map']['wife'], 1e-9);
        $this->assertEqualsWithDelta(3 / 4, $r['unassigned'], 1e-9);
    }
}
