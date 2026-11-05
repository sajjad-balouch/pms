<?php

namespace Tests\Unit;

use App\Support\Tools\ShajraLayout;
use PHPUnit\Framework\TestCase;

class ShajraLayoutTest extends TestCase
{
    private function p(string $id, string $parent, string $relation, string $status = 'alive', string $mother = ''): array
    {
        return ['id' => $id, 'parent' => $parent, 'relation' => $relation, 'name' => $id, 'status' => $status, 'cnic' => '', 'mother' => $mother];
    }

    private function cards(array $l): array
    {
        return array_column(array_map(fn ($c) => ['id' => $c['node']['id']] + $c, $l['cards']), null, 'id');
    }

    public function test_parents_above_wives_beside_and_children_under_their_mother(): void
    {
        $root = ['id' => 'root', 'name' => 'Aslam', 'relation' => 'root', 'status' => 'root', 'father_name' => 'Rasool'];
        $l = ShajraLayout::build($root, [
            $this->p('m', 'root', 'mother'),
            $this->p('w1', 'root', 'wife'),
            $this->p('w2', 'root', 'wife'),
            $this->p('s1', 'root', 'son', 'alive', 'w1'),
            $this->p('s2', 'root', 'son', 'alive', 'w2'),
            $this->p('b', 'root', 'brother'),
        ]);
        $c = $this->cards($l);

        // Walid (wildiyat se) aur walida upar
        $this->assertSame('display', $c['root-father']['role']);
        $this->assertLessThan($c['root']['y'], $c['m']['y']);
        // Biwiyan mutawaffi ki line mein, us ke baad
        $this->assertSame($c['root']['y'], $c['w1']['y']);
        // Do biwiyan: awwal baayen, doem daayen; patta bahar ki taraf nok-daar
        $this->assertLessThan($c['root']['x'], $c['w1']['x']);
        $this->assertGreaterThan($c['root']['x'], $c['w2']['x']);
        $this->assertSame('left', $c['w1']['facing']);
        $this->assertSame('right', $c['w2']['facing']);
        // Bhai bhi usi line mein
        $this->assertSame($c['root']['y'], $c['b']['y']);
        // Har beta apni maan ki taraf neeche
        $this->assertGreaterThan($c['root']['y'], $c['s1']['y']);
        $this->assertLessThan($c['root']['x'], $c['s1']['x']);
        $this->assertGreaterThan($c['root']['x'], $c['s2']['x']);
        $this->assertCount(0, $l['labels']);
        $this->assertCount(3, array_filter($l['lines'], fn ($x) => $x['s'] === 'marriage'));

        // Koi do card aapas mein na takrayein
        $all = array_values($c);
        foreach ($all as $i => $a) {
            foreach (array_slice($all, $i + 1) as $b) {
                $overlap = $a['x'] < $b['x'] + $b['w'] && $b['x'] < $a['x'] + $a['w'] && $a['y'] < $b['y'] + $b['h'] && $b['y'] < $a['y'] + $a['h'];
                $this->assertFalse($overlap, $a['id'] . ' overlaps ' . $b['id']);
            }
        }
    }

    public function test_rtl_mirrors_and_munasakha_son_carries_his_family(): void
    {
        $root = ['id' => 'root', 'name' => 'A', 'relation' => 'root', 'status' => 'root'];
        $people = [
            $this->p('w', 'root', 'wife'),
            $this->p('s', 'root', 'son', 'died_after'),
            $this->p('sm', 's', 'mother'),
            $this->p('sw', 's', 'wife'),
            $this->p('sk', 's', 'son'),
            $this->p('wd', 'root', 'daughter'),
        ];
        $ltr = $this->cards(ShajraLayout::build($root, $people));
        $rtl = $this->cards($l = ShajraLayout::build($root, $people, true));

        $this->assertSame('side', $ltr['sm']['role']);
        $this->assertGreaterThan($ltr['s']['y'], $ltr['sk']['y']);
        $this->assertLessThan($ltr['w']['x'], $ltr['root']['x']);
        $this->assertGreaterThan($rtl['w']['x'], $rtl['root']['x']);
        $this->assertEqualsWithDelta($l['width'] - $ltr['root']['x'] - $ltr['root']['w'], $rtl['root']['x'], 0.01);
    }
}
