<?php

namespace Tests\Unit;

use App\Support\Tools\ShajraEngine;
use App\Support\Tools\ShajraReport;
use PHPUnit\Framework\TestCase;

class ShajraReportTest extends TestCase
{
    private function p(string $id, string $parent, string $relation, string $name, string $status = 'alive', array $more = []): array
    {
        return array_merge(compact('id', 'parent', 'relation', 'name', 'status') + ['cnic' => '', 'minor' => false, 'guardian' => ''], $more);
    }

    public function test_report_follows_the_shajra_and_only_states_what_was_entered(): void
    {
        $people = [
            $this->p('w', 'root', 'wife', 'Salma'),
            $this->p('s', 'root', 'son', 'Umar', 'died_after'),
            $this->p('sw', 's', 'wife', 'Ayesha'),
            $this->p('sk', 's', 'son', 'Hamza', 'alive', ['minor' => true, 'guardian' => 'Ayesha']),
            $this->p('d', 'root', 'daughter', 'Amna'),
        ];
        $result = ShajraEngine::build('male', $people, 160);
        $deceased = ['name' => 'Ali Khan', 'father_rel' => 'so', 'father_name' => 'Ahmad', 'gender' => 'male', 'caste' => 'Arain', 'death_date' => '2026-01-15'];
        $record = ['mauza' => 'Chak 101', 'khewat' => '45', 'khatoni' => '', 'informant' => 'Amna', 'w1_name' => 'Ghulam Hussain', 'w2_name' => '', 'fiqh' => 'hanafi'];

        $r = ShajraReport::build($deceased, $record, $people, $result, ['death_cert' => true, 'frc' => false, 'cnic' => false, 'remarks' => '']);
        $text = implode("\n", $r['paragraphs']);

        $this->assertStringContainsString('مسمی Ali Khan ولد Ahmad قوم Arain', $text);
        $this->assertStringContainsString('کھیوٹ نمبر 45', $text);
        $this->assertStringContainsString('بتاریخ 15-01-2026', $text);
        $this->assertStringContainsString('ڈیتھ سرٹیفکیٹ', $text);
        $this->assertStringNotContainsString('فیملی رجسٹریشن', $text); // FRC chuna nahi gaya
        $this->assertStringContainsString('کوئی وارث پوشیدہ نہیں', $text);
        $this->assertStringContainsString('گواہان Ghulam Hussain', $text);
        $this->assertStringContainsString('مسمی Umar (پسر) مورث کے بعد فوت', $text); // munasakha
        $this->assertStringNotContainsString('دفعہ 4', $text); // MFLO nahi
        $this->assertStringContainsString('بذریعہ ولی Ayesha', $text);
        $this->assertStringContainsString('خواتین ورثاء', $text);
        $this->assertStringContainsString('فقہ حنفی کے مطابق', $text);
        $this->assertCount(4, $r['heirs']);
        $this->assertSame('پسر (بذریعہ Umar)', collect($r['heirs'])->firstWhere('name', 'Hamza')['relation']);
    }

    public function test_female_deceased_and_blank_fields_are_left_to_fill(): void
    {
        $people = [$this->p('h', 'root', 'husband', 'Aslam'), $this->p('b', 'root', 'son', 'Bilal', 'died_before'), $this->p('g', 'b', 'son', 'Saad')];
        $result = ShajraEngine::build('female', $people, 20);
        $r = ShajraReport::build(['name' => 'Zainab', 'gender' => 'female', 'father_rel' => 'wo', 'father_name' => 'Aslam'], ['fiqh' => 'other'], $people, $result, []);
        $text = implode("\n", $r['paragraphs']);

        $this->assertStringContainsString('مسمات Zainab زوجہ Aslam', $text);
        $this->assertStringContainsString('فوت ہو چکی ہے', $text);
        $this->assertStringContainsString('موضع __________', $text);
        $this->assertStringContainsString('دفعہ 4', $text);
        $this->assertStringContainsString('ورثاء اور گواہان نے', $text);
        $this->assertStringContainsString('انتقال وراثت حسب شجرہ', $text);
    }
}
