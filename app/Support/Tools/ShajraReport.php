<?php

namespace App\Support\Tools;

use Illuminate\Support\Carbon;

/**
 * Shajre ke mutabiq "رپورٹ پٹواری" (hamesha Urdu, jaise intiqal-e-wirasat ki file mein likhi jati hai).
 *
 * Punjab ka tareeqa: patwari wafat aur warisan ki tasdeeq kar ke shajra nasab aur report pesh karta hai,
 * aur revenue officer jalsa-e-aam mein warisan ke bayan le kar intiqal manzoor karta hai.
 * Report sirf wahi baat likhti hai jo darj ki gayi ho ya chuni gayi ho; khud se kuch farz nahi karti.
 */
class ShajraReport
{
    public const REL = [
        'father' => 'والد', 'mother' => 'والدہ', 'husband' => 'خاوند', 'son' => 'پسر', 'daughter' => 'دختر',
        'brother' => 'برادر حقیقی', 'sister' => 'ہمشیرہ حقیقی', 'wife' => 'بیوہ', 'grandfather' => 'دادا',
        'paternal_brother' => 'برادر علاتی', 'paternal_sister' => 'ہمشیرہ علاتی', 'nephew' => 'بھتیجا',
        'uncle' => 'چچا', 'cousin' => 'چچا زاد',
    ];

    public const FEMALE = ['mother', 'daughter', 'sister', 'wife', 'paternal_sister'];

    public const FIQH = ['hanafi' => 'فقہ حنفی', 'jafari' => 'فقہ جعفریہ', 'ahl_hadith' => 'مسلک اہل حدیث', 'other' => ''];

    /**
     * @return array{title:string,paragraphs:array<int,string>}
     */
    public static function build(array $deceased, array $record, array $people, array $result, array $opt): array
    {
        $female = ($deceased['gender'] ?? 'male') === 'female';
        $dash = fn ($v) => trim((string) $v) !== '' ? trim((string) $v) : '__________';
        $rel = ['so' => 'ولد', 'do' => 'دختر', 'wo' => 'زوجہ'][$deceased['father_rel'] ?? 'so'] ?? 'ولد';
        $chuka = $female ? 'چکی' : 'چکا';
        $tha = $female ? 'تھی' : 'تھا';

        $p = [];

        // 1. Mutawaffi aur wafat
        $who = ($female ? 'مسمات ' : 'مسمی ') . $dash($deceased['name'] ?? '');
        if (trim((string) ($deceased['father_name'] ?? '')) !== '') {
            $who .= " {$rel} " . trim($deceased['father_name']);
        }
        if (trim((string) ($deceased['caste'] ?? '')) !== '') {
            $who .= ' قوم ' . trim($deceased['caste']);
        }
        // Khewat ka hawala nahi: aksar ek se zyada khewat hote hain
        $owner = 'مالک/حصہ دار اراضی واقع موضع ' . $dash($record['mauza'] ?? '');
        $date = '';
        if (! empty($deceased['death_date'])) {
            try {
                $date = 'بتاریخ ' . Carbon::parse($deceased['death_date'])->format('d-m-Y') . ' ';
            } catch (\Throwable) {
                $date = '';
            }
        }
        $proof = ! empty($opt['death_cert']) ? ' وفات کی تصدیق یونین کونسل کے جاری کردہ ڈیتھ سرٹیفکیٹ سے کی گئی۔' : '';
        $p[] = "جناب عالی! گزارش ہے کہ {$who} {$owner} {$date}فوت ہو {$chuka} ہے۔{$proof}";

        // 2. Warisan aur gawahon ki tasdeeq: koi waris posheeda nahi
        $witnesses = array_values(array_filter([trim((string) ($record['w1_name'] ?? '')), trim((string) ($record['w2_name'] ?? ''))]));
        $by = [];
        if (trim((string) ($record['informant'] ?? '')) !== '') {
            $by[] = 'حسب گفتہ ' . trim($record['informant']);
        }
        if ($witnesses) {
            $by[] = 'گواہان ' . implode(' و ', $witnesses);
        }
        $byText = $by ? implode(' اور ', $by) . ' نے' : 'ورثاء اور گواہان نے';
        $docs = [];
        if (! empty($opt['frc'])) {
            $docs[] = 'نادرا کے فیملی رجسٹریشن سرٹیفکیٹ (FRC)';
        }
        if (! empty($opt['cnic'])) {
            $docs[] = 'ورثاء کے شناختی کارڈز';
        }
        $docText = $docs ? ' ورثاء کی تصدیق ' . implode(' اور ', $docs) . ' سے بھی کی گئی۔' : '';
        $p[] = "{$byText} بیان کیا اور تصدیق کی کہ متوفی کے شرعی وارثان صرف وہی ہیں جو شجرہ نسب ہذا میں درج ہیں۔ کوئی وارث پوشیدہ نہیں رکھا گیا اور نہ ہی کسی وارث کو محروم کیا گیا ہے۔{$docText}";

        // 3. Munasakha, MFLO, nabaligh, mafqood: sirf jab shajre mein maujood ho
        $byId = array_column($people, null, 'id');
        $nodes = $result['nodes'] ?? [];
        $later = [];
        $mflo = [];
        foreach ($people as $x) {
            $state = $nodes[$x['id']]['state'] ?? '';
            if ($state === 'passed_on') {
                $later[] = self::nameOf($x);
            } elseif ($state === 'mflo') {
                $mflo[] = self::nameOf($x);
            }
        }
        if ($later) {
            $p[] = implode('، ', $later) . ' مورث کے بعد فوت ہو چکے ہیں، اس لیے ان کا حصہ بطور مناسخہ ان کے اپنے شرعی وارثان میں تقسیم کیا گیا ہے۔';
        }
        if ($mflo) {
            $p[] = implode('، ', $mflo) . ' مورث کی زندگی میں فوت ہو چکے تھے، اس لیے مسلم عائلی قوانین آرڈیننس 1961 کی دفعہ 4 کے تحت ان کا حصہ ان کی اولاد کو دیا گیا ہے۔';
        }

        $minors = [];
        $missing = [];
        $women = false;
        foreach ($result['holders'] ?? [] as $h) {
            $chain = $h['via'][0] ?? [];
            $last = end($chain) ?: ['relation' => $h['relation']];
            $women = $women || in_array($last['relation'], self::FEMALE, true);
            if ($h['minor']) {
                $minors[] = $h['name'] . ($h['guardian'] !== '' ? ' (بذریعہ ولی ' . $h['guardian'] . ')' : '');
            }
            if ($h['missing']) {
                $missing[] = $h['name'];
            }
        }
        if ($minors) {
            $p[] = 'نابالغان ' . implode('، ', $minors) . ' کا حصہ ان کے اپنے نام درج ہوگا اور ولی کے ذریعے سنبھالا جائے گا۔';
        }
        if ($missing) {
            $p[] = implode('، ', $missing) . ' مفقود الخبر ہیں، ان کا حصہ ان کے لیے محفوظ رکھا گیا ہے۔';
        }
        if ($women) {
            $p[] = 'خواتین ورثاء کو ان کا پورا شرعی حصہ دیا گیا ہے۔';
        }
        if (trim((string) ($opt['remarks'] ?? '')) !== '') {
            $p[] = trim($opt['remarks']);
        }

        // 4. Darkhwast
        $fiqh = self::FIQH[$record['fiqh'] ?? 'hanafi'] ?? '';
        $as = $fiqh !== '' ? "{$fiqh} کے مطابق " : '';
        $p[] = "لہٰذا شجرہ نسب مرتب کر کے رپورٹ پیش ہے کہ انتقال وراثت {$as}حسب شجرہ ہذا جلسہ عام میں ورثاء کی موجودگی میں تصدیق کر کے منظور فرمایا جائے۔";

        return ['title' => 'رپورٹ پٹواری حلقہ', 'paragraphs' => $p];
    }

    private static function nameOf(array $x): string
    {
        $prefix = in_array($x['relation'], self::FEMALE, true) ? 'مسمات ' : 'مسمی ';

        return $prefix . (trim((string) $x['name']) !== '' ? trim($x['name']) : '—') . ' (' . (self::REL[$x['relation']] ?? '') . ')';
    }
}
