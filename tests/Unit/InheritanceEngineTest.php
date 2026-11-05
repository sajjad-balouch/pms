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

    public function test_only_daughters_remainder_goes_to_closest_residuary(): void
    {
        // Biwi 1/8, 2 betiyan 2/3, baqi 5/24 bhai ko
        $r = $this->shares(['wives' => 1, 'daughters' => 2, 'brothers' => 1, 'nephews' => 3, 'uncles' => 1]);
        $this->assertEqualsWithDelta(2 / 3, $r['map']['daughter'], 1e-9);
        $this->assertEqualsWithDelta(5 / 24, $r['map']['brother'], 1e-9);
        $this->assertArrayNotHasKey('nephew', $r['map']);
        $this->assertFalse($r['radd']);

        // Bhai nahi: bhateeje; woh bhi nahi: chacha; woh bhi nahi: chacha zad
        $this->assertEqualsWithDelta(1 / 2, $this->shares(['daughters' => 1, 'nephews' => 2, 'uncles' => 1])['map']['nephew'], 1e-9);
        $this->assertEqualsWithDelta(1 / 3, $this->shares(['daughters' => 2, 'uncles' => 1, 'cousins' => 4])['map']['uncle'], 1e-9);
        $this->assertEqualsWithDelta(1 / 3, $this->shares(['daughters' => 2, 'cousins' => 4])['map']['cousin'], 1e-9);

        // Koi asaba nahi: baqi radd se betiyon ko, biwi ko nahi
        $r = $this->shares(['wives' => 1, 'daughters' => 2]);
        $this->assertTrue($r['radd']);
        $this->assertEqualsWithDelta(7 / 8, $r['map']['daughter'], 1e-9);
        $this->assertTotalIsWhole($r);
    }

    public function test_grandfather_takes_fathers_place_and_blocks_brothers(): void
    {
        $r = $this->shares(['daughters' => 1, 'grandfather' => true, 'brothers' => 2]);
        $this->assertEqualsWithDelta(1 / 2, $r['map']['grandfather'], 1e-9); // 1/6 + baqi
        $this->assertArrayNotHasKey('brother', $r['map']);

        // Walid ho to dada kuch nahi
        $r = $this->shares(['daughters' => 1, 'father' => true, 'grandfather' => true]);
        $this->assertArrayNotHasKey('grandfather', $r['map']);
    }

    public function test_paternal_half_siblings(): void
    {
        // Ek haqeeqi behan 1/2, allati behan takmila 1/6, baqi allati bhai ko nahi (woh ho to behan ke saath asaba)
        $r = $this->shares(['sisters' => 1, 'paternal_sisters' => 1, 'uncles' => 1]);
        $this->assertEqualsWithDelta(1 / 2, $r['map']['sister'], 1e-9);
        $this->assertEqualsWithDelta(1 / 6, $r['map']['paternal_sister'], 1e-9);
        $this->assertEqualsWithDelta(1 / 3, $r['map']['uncle'], 1e-9);

        // Haqeeqi bhai allati bhai ko rokta hai
        $r = $this->shares(['brothers' => 1, 'paternal_brothers' => 2]);
        $this->assertEqualsWithDelta(1.0, $r['map']['brother'], 1e-9);

        // Beti ke saath haqeeqi behan asaba: allati bhai mahroom
        $r = $this->shares(['daughters' => 1, 'sisters' => 1, 'paternal_brothers' => 1]);
        $this->assertEqualsWithDelta(1 / 2, $r['map']['sister'], 1e-9);
        $this->assertArrayNotHasKey('paternal_brother', $r['map']);

        $r = $this->shares(['daughters' => 2, 'paternal_brothers' => 1, 'paternal_sisters' => 1]);
        $this->assertEqualsWithDelta(2 / 9, $r['map']['paternal_brother'], 1e-9);
        $this->assertEqualsWithDelta(1 / 9, $r['map']['paternal_sister'], 1e-9);
    }

    public function test_only_wife_reports_unassigned_remainder(): void
    {
        $r = $this->shares(['wives' => 2]);
        $this->assertEqualsWithDelta(1 / 4, $r['map']['wife'], 1e-9);
        $this->assertEqualsWithDelta(3 / 4, $r['unassigned'], 1e-9);
    }
}
