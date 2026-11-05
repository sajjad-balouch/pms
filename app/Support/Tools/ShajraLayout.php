<?php

namespace App\Support\Tools;

/**
 * Shajra ka naqsha, patwar ke tareeqe par: har naam aur line ki jagah yahan hisaab hoti hai.
 *
 *        Walid ——— Walida
 *          │
 *        MUTAWAFFI ——— (Zauja)
 *          │
 *     ┌────┴────┬──────┐
 *     ^         │      │
 *   Dukhtar   Pisar  Pisar
 *
 * - Nikah ki seedhi line; aulad ki line mard (sarbarah) se neeche aati hai, walidain mein walid se.
 * - Ek se zyada biwiyan hon to har biwi ki aulad usi biwi se neeche (az batan).
 * - Fout beta/beti apna khandan neeche le kar chalte hain (munasakha / MFLO).
 * - Kisi fout biwi, walid, bhai waghera ke apne warisan naam ke neeche chhoti fehrist mein.
 */
class ShajraLayout
{
    public const W = 132;

    public const H = 78;

    public const GX = 16;

    public const GM = 40;

    public const GY = 70;

    public const ROW = 15;

    /** Naam ki darmiyani satah, jahan nikah ki line lagti hai */
    public const LINE_Y = 30;

    public const PAD = 12;

    public const MAX_LIST = 30;

    private array $by = [];

    public static function build(array $root, array $people, bool $rtl = false): array
    {
        return (new self)->run($root, $people, $rtl);
    }

    private function run(array $root, array $people, bool $rtl): array
    {
        $ids = ['root' => true];
        foreach ($people as $p) {
            $ids[$p['id']] = true;
        }
        foreach ($people as $p) {
            if (isset($ids[$p['parent']]) && $p['parent'] !== $p['id']) {
                $this->by[$p['parent']][] = $p;
            }
        }

        $block = $this->family($root, true, 0);
        $items = $this->shift($block['items'], self::PAD, self::PAD);
        $width = $block['w'] + 2 * self::PAD;
        $height = $block['h'] + 2 * self::PAD;

        if ($rtl) {
            foreach ($items as &$it) {
                if ($it['t'] === 'card') {
                    $it['x'] = $width - $it['x'] - $it['w'];
                } elseif ($it['t'] === 'line') {
                    $it['x1'] = $width - $it['x1'];
                    $it['x2'] = $width - $it['x2'];
                } else {
                    $it['x'] = $width - $it['x'];
                }
            }
            unset($it);
        }

        return [
            'width' => (int) ceil($width),
            'height' => (int) ceil($height),
            'cards' => array_values(array_filter($items, fn ($i) => $i['t'] === 'card')),
            'lines' => array_values(array_filter($items, fn ($i) => $i['t'] === 'line')),
            'labels' => array_values(array_filter($items, fn ($i) => $i['t'] === 'label')),
            'rtl' => $rtl,
        ];
    }

    /** Ek shakhs aur us ka khandan (biwiyan, aulad, aur root ke liye walidain/behan bhai) */
    private function family(array $p, bool $isRoot, int $depth): array
    {
        $kids = $this->by[$p['id']] ?? [];
        $expand = $isRoot || (in_array($p['status'], ['died_after', 'died_before'], true) && $kids && $depth < 7);
        if (! $expand) {
            return $this->single($p, 'lineage');
        }


        $pick = fn (array $rels) => array_values(array_filter($kids, fn ($k) => in_array($k['relation'], $rels, true)));
        $spouses = $pick(['wife', 'husband']);
        $children = $pick(['son', 'daughter']);
        $parents = $pick(['father', 'mother']);
        $siblings = $pick(['brother', 'sister', 'paternal_brother', 'paternal_sister']);
        // Door ke asaba (dada, bhateeja, chacha, chacha zad): ek taraf, dotted line se
        $extended = $pick(['grandfather', 'nephew', 'uncle', 'cousin']);

        $items = [];
        $x = 0.0;
        $rowAnchors = [];
        $rowH = 0.0;
        $place = function (array $card) use (&$items, &$rowH) {
            $items[] = $card;
            $rowH = max($rowH, $card['h']);

            return $card['x'] + self::W / 2;
        };

        if ($isRoot) {
            foreach ($siblings as $s) {
                $rowAnchors[] = $place($this->card($s, 'sibling', $x, 0));
                $x += self::W + self::GX;
            }
        }

        // Ek biwi ho to daayen. Do ya zyada hon to awwal baayen, doem daayen, soem baayen, chaharum daayen
        $multi = count($spouses) > 1;
        $left = [];
        $right = [];
        foreach ($spouses as $i => $s) {
            $s['_ord'] = $i + 1;
            ($multi && $i % 2 === 0) ? $left[] = $s : $right[] = $s;
        }
        $extra = fn ($s, $facing) => ['ord' => $s['_ord'], 'of' => count($spouses), 'facing' => $facing];

        $drop = [];
        $leftCenters = [];
        foreach (array_reverse($left) as $s) {
            $card = $this->card($s, 'spouse', $x, 0, $extra($s, 'left'));
            $leftCenters[] = $place($card);
            $drop[$s['id']] = ['x' => $card['x'] + self::W / 2, 'y' => $card['h']];
            $x += self::W + self::GM;
        }

        $pX = $x;
        $pH = $this->height($p, $isRoot ? 'root' : 'lineage');
        $pCenter = $place($this->card($p, $isRoot ? 'root' : 'lineage', $x, 0, ['h' => $pH]));
        $rowAnchors[] = $pCenter;
        $x += self::W;

        // Nikah ki lineen naam ke beech se naam ke beech tak (naam ki pusht kaghazi hai, line naam se ja milti hai)
        $chain = array_merge($leftCenters, [$pCenter]);
        for ($i = 1; $i < count($chain); $i++) {
            $items[] = $this->line($chain[$i - 1], self::LINE_Y, $chain[$i], self::LINE_Y, 'marriage');
        }

        $last = $pCenter;
        foreach ($right as $s) {
            $x += self::GM;
            $card = $this->card($s, 'spouse', $x, 0, $extra($s, 'right'));
            $c = $place($card);
            $items[] = $this->line($last, self::LINE_Y, $c, self::LINE_Y, 'marriage');
            $drop[$s['id']] = ['x' => $c, 'y' => $card['h']];
            $last = $c;
            $x += self::W;
        }

        // Fout bete ke apne walidain/behan bhai, aur har jagah door ke asaba: saath mein, dotted line se
        $others = $isRoot ? $extended : array_merge($parents, $siblings, $extended);
        foreach ($others as $o) {
            $x += self::GX;
            $c = $place($this->card($o, 'side', $x, 0));
            $items[] = $this->line($last, self::LINE_Y, $c, self::LINE_Y, 'dashed');
            $last = $c;
            $x += self::W;
        }

        // Aulad ko maaon ke hisaab se groups mein
        $groups = [];
        $byMother = [];
        $own = [];
        foreach ($children as $c) {
            $m = (string) ($c['mother'] ?? '');
            if ($m !== '' && isset($drop[$m])) {
                $byMother[$m][] = $c;
            } else {
                $own[] = $c;
            }
        }
        // Ek hi biwi (ya koi nahi): patwar ki tarah saari aulad mard ke naam ke neeche se
        if (count($spouses) <= 1) {
            $own = $children;
            $byMother = [];
        }
        if ($own) {
            $groups[] = ['x' => $pX + self::W / 2, 'y' => $pH, 'kids' => $own];
        }
        foreach ($spouses as $s) {
            if (! empty($byMother[$s['id']])) {
                $groups[] = ['x' => $drop[$s['id']]['x'], 'y' => $drop[$s['id']]['y'], 'kids' => $byMother[$s['id']]];
            }
        }
        // Baayen se daayen tarteeb, taa-ke har maan ki aulad usi ke neeche aaye
        usort($groups, fn ($a, $b) => $a['x'] <=> $b['x']);

        // Har maan ki aulad ki apni ufuqi line, alag satah par, taa-ke groups aapas mein na milein
        $levels = max(1, count($groups));
        $y0 = $rowH + max(self::GY, 34 + $levels * 16 + 18);
        $cursor = -INF;
        foreach ($groups as $gi => $g) {
            // Baad wale (daayen) group ki line oopar: is tarah lineen ek doosre ko nahi kaatteen
            $busY = $rowH + 34 + ($levels - 1 - $gi) * 16;
            $blocks = array_map(fn ($c) => $this->family($c, false, $depth + 1), $g['kids']);
            $gw = array_sum(array_column($blocks, 'w')) + self::GX * (count($blocks) - 1);
            $left = max($g['x'] - $gw / 2, $cursor);
            $cx = $left;
            $anchors = [];
            foreach ($blocks as $b) {
                array_push($items, ...$this->shift($b['items'], $cx, $y0));
                $anchors[] = $cx + $b['anchor'];
                $cx += $b['w'] + self::GX;
            }
            $cursor = $cx;

            $items[] = $this->line($g['x'], $g['y'], $g['x'], $busY, 'solid');
            $items[] = $this->line(min($g['x'], $anchors[0]), $busY, max($g['x'], end($anchors)), $busY, 'solid');
            foreach ($anchors as $a) {
                $items[] = $this->line($a, $busY, $a, $y0, 'solid');
            }
        }

        if ($isRoot) {
            $items = $this->ancestors($p, $parents, $items, $rowAnchors, $rowH);
        }

        return $this->normalize($items, $pX + self::W / 2);
    }

    /** Mutawaffi ke walidain upar; behan bhai unhi ki aulad ki tarah ek line se */
    private function ancestors(array $root, array $parents, array $items, array $rowAnchors, float $rowH): array
    {
        $father = null;
        $mother = null;
        foreach ($parents as $pp) {
            if ($pp['relation'] === 'father' && ! $father) {
                $father = $pp;
            } elseif ($pp['relation'] === 'mother' && ! $mother) {
                $mother = $pp;
            }
        }
        // Walid ka node na ho to wildiyat (s/o, d/o) se sirf dikhane ke liye card; zauja (w/o) ho to nahi
        if (! $father && trim((string) ($root['father_name'] ?? '')) !== '' && ($root['father_rel'] ?? 'so') !== 'wo') {
            $father = ['id' => 'root-father', 'parent' => '', 'relation' => 'father', 'name' => $root['father_name'], 'status' => 'unknown', 'cnic' => ''];
        }

        $minA = min($rowAnchors);
        $maxA = max($rowAnchors);
        $c = ($minA + $maxA) / 2;

        if (! $father && ! $mother) {
            if (count($rowAnchors) > 1) {
                $items[] = $this->line($minA, -self::GY / 2, $maxA, -self::GY / 2, 'solid');
                foreach ($rowAnchors as $a) {
                    $items[] = $this->line($a, -self::GY / 2, $a, 0, 'solid');
                }
            }

            return $items;
        }

        $top = [];
        if ($father && $mother) {
            // Walid aulad ke oopar, walida saath; aulad ki line walid se
            $fx = $c - self::W / 2;
            $mx = $fx + self::W + self::GM;
            $fCard = $this->card($father, $father['id'] === 'root-father' ? 'display' : 'ancestor', $fx, 0);
            $top[] = $fCard;
            $top[] = $this->card($mother, 'ancestor', $mx, 0);
            $top[] = $this->line($fx + self::W / 2, self::LINE_Y, $mx + self::W / 2, self::LINE_Y, 'marriage');
            $dropY = $fCard['h'];
        } else {
            $one = $father ?: $mother;
            $card = $this->card($one, ($one['id'] ?? '') === 'root-father' ? 'display' : 'ancestor', $c - self::W / 2, 0);
            $top[] = $card;
            $dropY = $card['h'];
        }

        $aH = max(array_map(fn ($i) => $i['t'] === 'card' ? $i['h'] : 0, $top));
        $shiftY = $aH + self::GY;
        $busY = $shiftY - self::GY / 2;
        $items = $this->shift($items, 0, $shiftY);
        array_push($items, ...$top);
        $items[] = $this->line($c, $dropY, $c, $busY, 'solid');
        if (count($rowAnchors) > 1) {
            $items[] = $this->line($minA, $busY, $maxA, $busY, 'solid');
        }
        foreach ($rowAnchors as $a) {
            $items[] = $this->line($a, $busY, $a, $shiftY, 'solid');
        }

        return $items;
    }

    private function single(array $p, string $role): array
    {
        return $this->normalize([$this->card($p, $role, 0, 0)], self::W / 2);
    }

    private function card(array $node, string $role, float $x, float $y, array $extra = []): array
    {
        $list = in_array($role, ['root', 'lineage'], true) ? [] : $this->compact($node['id'], 0);
        $h = $extra['h'] ?? $this->height($node, $role, $list);
        unset($extra['h']);

        return ['t' => 'card', 'node' => $node, 'role' => $role, 'x' => $x, 'y' => $y, 'w' => self::W, 'h' => $h, 'list' => $list] + $extra;
    }

    private function height(array $node, string $role, ?array $list = null): float
    {
        $list ??= in_array($role, ['root', 'lineage'], true) ? [] : $this->compact($node['id'], 0);

        return self::H + ($list ? count($list) * self::ROW + 8 : 0);
    }

    /** Card ke andar warisan ki chhoti fehrist (jab un ka apna khana na bane) */
    private function compact(string $id, int $level): array
    {
        $rows = [];
        foreach ($this->by[$id] ?? [] as $k) {
            if (count($rows) >= self::MAX_LIST) {
                break;
            }
            $rows[] = ['level' => $level, 'node' => $k];
            if ($level < 4) {
                array_push($rows, ...$this->compact($k['id'], $level + 1));
            }
        }

        return array_slice($rows, 0, self::MAX_LIST);
    }

    private function line(float $x1, float $y1, float $x2, float $y2, string $style): array
    {
        return ['t' => 'line', 'x1' => $x1, 'y1' => $y1, 'x2' => $x2, 'y2' => $y2, 's' => $style];
    }

    private function shift(array $items, float $dx, float $dy): array
    {
        foreach ($items as &$it) {
            if ($it['t'] === 'line') {
                $it['x1'] += $dx;
                $it['x2'] += $dx;
                $it['y1'] += $dy;
                $it['y2'] += $dy;
            } else {
                $it['x'] += $dx;
                $it['y'] += $dy;
            }
        }

        return $items;
    }

    /** Block ko (0,0) se shuru karna; anchor = khandan ke sarbarah ke card ka beech */
    private function normalize(array $items, float $anchor): array
    {
        $minX = INF;
        $minY = INF;
        $maxX = -INF;
        $maxY = -INF;
        foreach ($items as $it) {
            if ($it['t'] === 'card') {
                $minX = min($minX, $it['x']);
                $maxX = max($maxX, $it['x'] + $it['w']);
                $minY = min($minY, $it['y']);
                $maxY = max($maxY, $it['y'] + $it['h']);
            } elseif ($it['t'] === 'line') {
                $minX = min($minX, $it['x1'], $it['x2']);
                $maxX = max($maxX, $it['x1'], $it['x2']);
                $minY = min($minY, $it['y1'], $it['y2']);
                $maxY = max($maxY, $it['y1'], $it['y2']);
            } else {
                // Label ke liye thori jagah
                $maxX = max($maxX, $it['x'] + 110);
            }
        }

        return [
            'items' => $this->shift($items, -$minX, -$minY),
            'w' => $maxX - $minX,
            'h' => $maxY - $minY,
            'anchor' => $anchor - $minX,
        ];
    }
}
