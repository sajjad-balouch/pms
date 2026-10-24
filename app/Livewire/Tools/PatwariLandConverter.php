<?php

namespace App\Livewire\Tools;

use Livewire\Component;

class PatwariLandConverter extends Component
{
    public $activeTab = 'irregular';
    public $standard = 'society'; // 'society' (225) ya 'revenue' (272.25)

    // Unit Converter
    public $input_value = 1;
    public $from_unit = 'kanal';

    // 4-Sided Plot Inputs (in Feet)
    public $side_north = 50; // North (Top)
    public $side_south = 45; // South (Bottom)
    public $side_east = 30;  // East (Right)
    public $side_west = 35;  // West (Left)
    public $diagonal = 60;   // Diagonal / Watar (North-West corner to South-East corner)

    public function render()
    {
        $marlaSqFt = ($this->standard === 'society') ? 225.0 : 272.25;

        $convertedUnits = $this->calculateUnitConversions($marlaSqFt);
        $plotResult = $this->calculateIrregularPlot($marlaSqFt);
        $svgCoordinates = $this->calculatePlotPolygonCoordinates();

        return view('livewire.tools.patwari-land-converter', [
            'marlaSqFt' => $marlaSqFt,
            'convertedUnits' => $convertedUnits,
            'plotResult' => $plotResult,
            'svgCoordinates' => $svgCoordinates,
        ])->layout('layouts.front-app', ['title' => 'پٹواری لینڈ کنورٹر و ٹیڑھے پلاٹ کا رقبہ کیلکولیٹر | AssanZameen']);
    }

    private function calculateUnitConversions($marlaSqFt)
    {
        $val = max(0, (float) $this->input_value);
        $sarsahiSqFt = $marlaSqFt / 9.0;
        $karamFeet = ($this->standard === 'society') ? 5.0 : 5.5;

        $totalSqFt = 0;
        switch ($this->from_unit) {
            case 'karam': $totalSqFt = $val * ($karamFeet * $karamFeet); break;
            case 'sarsahi': $totalSqFt = $val * $sarsahiSqFt; break;
            case 'sqft': $totalSqFt = $val; break;
            case 'sqyards': $totalSqFt = $val * 9.0; break;
            case 'marla': $totalSqFt = $val * $marlaSqFt; break;
            case 'kanal': $totalSqFt = $val * ($marlaSqFt * 20.0); break;
            case 'acre': $totalSqFt = $val * ($marlaSqFt * 160.0); break;
            case 'murabba': $totalSqFt = $val * ($marlaSqFt * 160.0 * 25.0); break;
        }

        return [
            'sqft' => round($totalSqFt, 2),
            'sqyards' => round($totalSqFt / 9.0, 2),
            'sarsahi' => round($totalSqFt / $sarsahiSqFt, 2),
            'marla' => round($totalSqFt / $marlaSqFt, 3),
            'kanal' => round($totalSqFt / ($marlaSqFt * 20.0), 4),
            'acre' => round($totalSqFt / ($marlaSqFt * 160.0), 4),
            'karam' => round($totalSqFt / ($karamFeet * $karamFeet), 2),
            'patwari_formatted' => $this->formatToKanalMarlaSarsahi($totalSqFt, $marlaSqFt),
        ];
    }

    private function calculateIrregularPlot($marlaSqFt)
    {
        $a = max(1, (float) $this->side_north);
        $b = max(1, (float) $this->side_south);
        $c = max(1, (float) $this->side_east);
        $d = max(1, (float) $this->side_west);
        $diag = (float) $this->diagonal;

        $totalAreaSqFt = 0;
        $isDiagonalUsed = false;

        // Heron's Triangle Verification
        if ($diag > 0 && ($a + $c > $diag) && ($a + $diag > $c) && ($c + $diag > $a) && ($b + $d > $diag) && ($b + $diag > $d) && ($d + $diag > $b)) {
            $s1 = ($a + $c + $diag) / 2.0;
            $area1 = sqrt(max(0, $s1 * ($s1 - $a) * ($s1 - $c) * ($s1 - $diag)));

            $s2 = ($b + $d + $diag) / 2.0;
            $area2 = sqrt(max(0, $s2 * ($s2 - $b) * ($s2 - $d) * ($s2 - $diag)));

            $totalAreaSqFt = $area1 + $area2;
            $isDiagonalUsed = true;
        } else {
            $avgLength = ($a + $b) / 2.0;
            $avgWidth = ($c + $d) / 2.0;
            $totalAreaSqFt = $avgLength * $avgWidth;
        }

        $totalMarlas = $totalAreaSqFt / $marlaSqFt;

        return [
            'sqft' => round($totalAreaSqFt, 2),
            'sqyards' => round($totalAreaSqFt / 9.0, 2),
            'marlas' => round($totalMarlas, 3),
            'kanals' => round($totalMarlas / 20.0, 4),
            'is_diagonal_used' => $isDiagonalUsed,
            'patwari_formatted' => $this->formatToKanalMarlaSarsahi($totalAreaSqFt, $marlaSqFt),
        ];
    }

    // Dynamic Geometry Engine: Har value ke mutabiq exact SVG Polygon draw karega
    private function calculatePlotPolygonCoordinates()
    {
        $n = max(1, (float) $this->side_north);
        $s = max(1, (float) $this->side_south);
        $e = max(1, (float) $this->side_east);
        $w = max(1, (float) $this->side_west);
        $diag = (float) $this->diagonal;

        // Step 1: Cartesian Points Setup (P1, P2, P3, P4)
        // P1 = North-West Top-Left (0, 0)
        $p1 = ['x' => 0, 'y' => 0];

        // P2 = North-East Top-Right
        $p2 = ['x' => $n, 'y' => 0];

        // Diagonal Calculation check
        $validDiag = ($diag > 0 && ($n + $e > $diag) && ($n + $diag > $e) && ($e + $diag > $n) && ($s + $w > $diag));

        if ($validDiag) {
            // Law of Cosines for Triangle P1-P2-P3 (North, East, Diag)
            $cosA = ($n * $n + $diag * $diag - $e * $e) / (2 * $n * $diag);
            $cosA = max(-1, min(1, $cosA));
            $sinA = sqrt(1 - $cosA * $cosA);

            // P3 = South-East Bottom-Right
            $p3 = [
                'x' => $diag * $cosA,
                'y' => $diag * $sinA
            ];

            // P4 = South-West Bottom-Left from P1 & P3
            $cosB = ($w * $w + $diag * $diag - $s * $s) / (2 * $w * $diag);
            $cosB = max(-1, min(1, $cosB));
            $sinB = sqrt(1 - $cosB * $cosB);

            // Angle of diagonal
            $diagAngle = atan2($p3['y'], $p3['x']);
            $p4Angle = $diagAngle + acos($cosB);

            $p4 = [
                'x' => $w * cos($p4Angle),
                'y' => $w * sin($p4Angle)
            ];
        } else {
            // Default Trapezius Estimation
            $p3 = ['x' => $n + ($s - $n) * 0.3, 'y' => $e];
            $p4 = ['x' => -($s - $n) * 0.2, 'y' => $w];
        }

        // Step 2: Fit into SVG Box (Width: 320, Height: 200 with padding 30)
        $points = [$p1, $p2, $p3, $p4];
        $minX = min(array_column($points, 'x'));
        $maxX = max(array_column($points, 'x'));
        $minY = min(array_column($points, 'y'));
        $maxY = max(array_column($points, 'y'));

        $plotWidth = max(1, $maxX - $minX);
        $plotHeight = max(1, $maxY - $minY);

        $svgW = 320;
        $svgH = 200;
        $padding = 35;

        $scaleX = ($svgW - 2 * $padding) / $plotWidth;
        $scaleY = ($svgH - 2 * $padding) / $plotHeight;
        $scale = min($scaleX, $scaleY);

        $offsetX = ($svgW - $plotWidth * $scale) / 2;
        $offsetY = ($svgH - $plotHeight * $scale) / 2;

        $finalPoints = [];
        foreach ($points as $pt) {
            $finalPoints[] = [
                'x' => round($offsetX + ($pt['x'] - $minX) * $scale, 1),
                'y' => round($offsetY + ($pt['y'] - $minY) * $scale, 1),
            ];
        }

        $polygonString = "{$finalPoints[0]['x']},{$finalPoints[0]['y']} {$finalPoints[1]['x']},{$finalPoints[1]['y']} {$finalPoints[2]['x']},{$finalPoints[2]['y']} {$finalPoints[3]['x']},{$finalPoints[3]['y']}";

        // Labels Centers
        $northCenter = [
            'x' => ($finalPoints[0]['x'] + $finalPoints[1]['x']) / 2,
            'y' => max(15, ($finalPoints[0]['y'] + $finalPoints[1]['y']) / 2 - 10)
        ];
        $southCenter = [
            'x' => ($finalPoints[3]['x'] + $finalPoints[2]['x']) / 2,
            'y' => min($svgH - 8, ($finalPoints[3]['y'] + $finalPoints[2]['y']) / 2 + 16)
        ];
        $eastCenter = [
            'x' => min($svgW - 12, ($finalPoints[1]['x'] + $finalPoints[2]['x']) / 2 + 14),
            'y' => ($finalPoints[1]['y'] + $finalPoints[2]['y']) / 2
        ];
        $westCenter = [
            'x' => max(15, ($finalPoints[0]['x'] + $finalPoints[3]['x']) / 2 - 14),
            'y' => ($finalPoints[0]['y'] + $finalPoints[3]['y']) / 2
        ];

        return [
            'polygon' => $polygonString,
            'p1' => $finalPoints[0],
            'p2' => $finalPoints[1],
            'p3' => $finalPoints[2],
            'p4' => $finalPoints[3],
            'northLabel' => $northCenter,
            'southLabel' => $southCenter,
            'eastLabel' => $eastCenter,
            'westLabel' => $westCenter,
        ];
    }

    private function formatToKanalMarlaSarsahi($sqft, $marlaSqFt)
    {
        $sarsahiSqFt = $marlaSqFt / 9.0;
        $totalSarsahis = floor($sqft / $sarsahiSqFt);

        $kanals = floor($totalSarsahis / (20 * 9));
        $remSarsahis = $totalSarsahis % (20 * 9);

        $marlas = floor($remSarsahis / 9);
        $sarsahi = $remSarsahis % 9;

        $feetRemainder = round(fmod($sqft, $sarsahiSqFt), 1);

        return "{$kanals} کنال - {$marlas} مرلہ - {$sarsahi} سرساہی" . ($feetRemainder > 0 ? " ({$feetRemainder} فٹ)" : "");
    }
}