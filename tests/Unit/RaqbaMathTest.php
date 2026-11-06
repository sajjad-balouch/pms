<?php

namespace Tests\Unit;

use App\Support\Tools\RaqbaMath;
use PHPUnit\Framework\TestCase;

class RaqbaMathTest extends TestCase
{
    public function test_adds_and_carries_like_the_revenue_record(): void
    {
        $ms = RaqbaMath::marla('revenue');
        // 2-15-200 + 1-10-100 = 4-5-300 -> 300 fut = 1 marla 27.75 fut -> 4-6-27.75
        $sqft = RaqbaMath::toSqft(2, 15, 200, $ms) + RaqbaMath::toSqft(1, 10, 100, $ms);
        $s = RaqbaMath::split($sqft, $ms);

        $this->assertSame([4, 6], [$s['kanal'], $s['marla']]);
        $this->assertEqualsWithDelta(27.75, $s['feet'], 0.001);
        $this->assertEqualsWithDelta(86.1019, $s['marlas'], 0.0001);
    }

    public function test_subtraction_and_negative_total(): void
    {
        $ms = RaqbaMath::marla('revenue');
        $s = RaqbaMath::split(RaqbaMath::toSqft(1, 0, 0, $ms) - RaqbaMath::toSqft(0, 5, 0, $ms), $ms);
        $this->assertSame([1, 0, 15], [$s['sign'], $s['kanal'], $s['marla']]);

        $neg = RaqbaMath::split(RaqbaMath::toSqft(0, 2, 0, $ms) - RaqbaMath::toSqft(0, 3, 0, $ms), $ms);
        $this->assertSame([-1, 0, 1], [$neg['sign'], $neg['kanal'], $neg['marla']]);
    }

    public function test_acre_breakdown(): void
    {
        $ms = RaqbaMath::marla('revenue');
        // 17 kanal 5 marla = 2 acre 1 kanal 5 marla
        $a = RaqbaMath::splitAcre(RaqbaMath::toSqft(17, 5, 0, $ms), $ms);
        $this->assertSame([2, 1, 5], [$a['acre'], $a['kanal'], $a['marla']]);
        $this->assertSame(1, RaqbaMath::splitAcre(43560, $ms)['acre']);

        // Society marla: 1 acre 43,560 fut hi rehta hai
        $soc = RaqbaMath::splitAcre(43560 + 225 * 3, RaqbaMath::marla('society'));
        $this->assertSame([1, 0, 3], [$soc['acre'], $soc['kanal'], $soc['marla']]);
    }

    public function test_rounding_never_shows_a_full_marla_in_feet(): void
    {
        $ms = RaqbaMath::marla('revenue');
        $s = RaqbaMath::split(272.2499, $ms);
        $this->assertSame([0, 1], [$s['kanal'], $s['marla']]);
        $this->assertSame(0.0, $s['feet']);
    }
}
