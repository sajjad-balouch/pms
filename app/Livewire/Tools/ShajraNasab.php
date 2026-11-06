<?php

namespace App\Livewire\Tools;

use App\Support\Tools\ShajraEngine;
use App\Support\Tools\ShajraLayout;
use App\Support\Tools\ToolCatalog;
use Livewire\Component;

/**
 * Digital shajra nasab: mutawaffi, warisan (aur baad mein fout hone walon ke warisan) ka darakht,
 * har waris ka hissa aur raqba. Kuch bhi server par save nahi hota; browser localStorage mein rehta hai.
 */
class ShajraNasab extends Component
{
    public const MAX_PEOPLE = 120;

    public const FIQH = ['hanafi', 'jafari', 'ahl_hadith', 'other'];

    public const STATUSES =['alive', 'died_after', 'died_before', 'missing', 'divorced'];

    public array $deceased = [
        'name' => '', 'father_rel' => 'so', 'father_name' => '', 'gender' => 'male', 'death_date' => '', 'cnic' => '', 'caste' => '', 'married' => '', 'issue' => '',
    ];

    public array $record = [
        'mauza' => '', 'tehsil' => '', 'district' => '', 'khewat' => '', 'khatoni' => '', 'intiqal' => '',
        // Sarkari form ke khaane: halqa qanungoi, kis ke kehne par (hasb-e-guftah), fiqh, do gawah
        'markaz' => '', 'informant' => '', 'fiqh' => 'hanafi', 'w1_name' => '', 'w1_cnic' => '', 'w2_name' => '', 'w2_cnic' => '',
        'kanal' => 8, 'marla' => 0, 'sarsahi' => 0,
    ];

    public array $people = [];

    public array $form = [];

    public int $nextId = 1;

    /** Shajre mein naam ke neeche hissa dikhana (patwari chahe to print ke liye band kar sakta hai) */
    public bool $showShares = true;

    public function mount(): void
    {
        $this->loadSample();
    }

    private function person(string $id, string $parent, string $relation, string $name, string $status = 'alive', array $more = []): array
    {
        return array_merge(['id' => $id, 'parent' => $parent, 'relation' => $relation, 'name' => $name, 'status' => $status,
            'cnic' => '', 'mother' => '', 'minor' => false, 'guardian' => '', 'married' => '', 'issue' => ''], $more);
    }

    public function loadSample(): void
    {
        $this->deceased = ['name' => 'محمد اسلم', 'father_rel' => 'so', 'father_name' => 'غلام رسول', 'gender' => 'male', 'death_date' => '', 'cnic' => '', 'caste' => 'آرائیں', 'married' => 'yes', 'issue' => 'yes'];
        $this->record = array_merge($this->record, ['kanal' => 8, 'marla' => 0, 'sarsahi' => 0]);
        $this->people = [
            $this->person('n1', 'root', 'father', 'غلام رسول', 'died_before'),
            $this->person('n2', 'root', 'mother', 'حاجرہ بی بی'),
            $this->person('n3', 'root', 'wife', 'زینب بی بی'),
            $this->person('n4', 'root', 'wife', 'رقیہ بی بی'),
            $this->person('n5', 'root', 'son', 'محمد علی', 'alive', ['mother' => 'n3']),
            $this->person('n6', 'root', 'son', 'محمد عمر', 'died_after', ['mother' => 'n3', 'married' => 'yes', 'issue' => 'yes']),
            $this->person('n7', 'root', 'daughter', 'فاطمہ بی بی', 'alive', ['mother' => 'n3']),
            $this->person('n8', 'root', 'son', 'محمد عثمان', 'alive', ['mother' => 'n4', 'minor' => true, 'guardian' => 'رقیہ بی بی']),
            $this->person('n9', 'root', 'brother', 'کریم بخش'),
            $this->person('n10', 'n6', 'mother', 'زینب بی بی'),
            $this->person('n11', 'n6', 'wife', 'عائشہ بی بی'),
            $this->person('n12', 'n6', 'son', 'حمزہ', 'alive', ['minor' => true, 'guardian' => 'عائشہ بی بی']),
        ];
        $this->nextId = 13;
        $this->resetForm('root');
    }

    public function clearAll(): void
    {
        $this->deceased = ['name' => '', 'father_rel' => 'so', 'father_name' => '', 'gender' => 'male', 'death_date' => '', 'cnic' => '', 'caste' => '', 'married' => '', 'issue' => ''];
        $this->record = ['mauza' => '', 'tehsil' => '', 'district' => '', 'khewat' => '', 'khatoni' => '', 'intiqal' => '', 'kanal' => 0, 'marla' => 0, 'sarsahi' => 0,
            'markaz' => '', 'informant' => '', 'fiqh' => 'hanafi', 'w1_name' => '', 'w1_cnic' => '', 'w2_name' => '', 'w2_cnic' => ''];
        $this->people = [];
        $this->nextId = 1;
        $this->resetForm('root');
    }

    /** Browser mein mehfooz shajra wapas lana. Har cheez dobara jaanchi jati hai. */
    public function restore($state): void
    {
        if (! is_array($state) || ! is_array($state['people'] ?? null)) {
            return;
        }

        $str = fn ($v, $len = 120) => mb_substr(trim(is_scalar($v) ? (string) $v : ''), 0, $len);

        $d = is_array($state['deceased'] ?? null) ? $state['deceased'] : [];
        $this->deceased = [
            'name' => $str($d['name'] ?? ''),
            'father_rel' => in_array($d['father_rel'] ?? '', ['so', 'do', 'wo'], true) ? $d['father_rel'] : 'so',
            'father_name' => $str($d['father_name'] ?? ''),
            'gender' => ($d['gender'] ?? '') === 'female' ? 'female' : 'male',
            'death_date' => $str($d['death_date'] ?? '', 10), 'cnic' => $str($d['cnic'] ?? '', 15),
            'caste' => $str($d['caste'] ?? '', 60),
            'married' => $this->yesNo($d['married'] ?? ''),
            'issue' => $this->yesNo($d['issue'] ?? ''),
        ];

        $r = is_array($state['record'] ?? null) ? $state['record'] : [];
        foreach (['mauza', 'tehsil', 'district', 'khewat', 'khatoni', 'intiqal', 'markaz', 'informant', 'w1_name', 'w2_name'] as $k) {
            $this->record[$k] = $str($r[$k] ?? '', 60);
        }
        // Gawahon ke shanakhti card: sirf hindse, 13 tak
        foreach (['w1_cnic', 'w2_cnic'] as $k) {
            $this->record[$k] = substr(preg_replace('/\D/', '', (string) ($r[$k] ?? '')), 0, 13);
        }
        $this->record['fiqh'] = in_array($r['fiqh'] ?? '', self::FIQH, true) ? $r['fiqh'] : 'hanafi';
        foreach (['kanal', 'marla', 'sarsahi'] as $k) {
            $this->record[$k] = $this->num($r[$k] ?? 0);
        }

        $ids = ['root' => true];
        $people = [];
        $max = 0;
        foreach (array_slice($state['people'], 0, self::MAX_PEOPLE) as $p) {
            if (! is_array($p)) {
                continue;
            }
            $id = $str($p['id'] ?? '', 12);
            $parent = $str($p['parent'] ?? '', 12);
            // Parent pehle aana chahiye, is se cycle mumkin nahi
            if (! preg_match('/^n\d+$/', $id) || isset($ids[$id]) || ! isset($ids[$parent])) {
                continue;
            }
            $ids[$id] = true;
            $max = max($max, (int) substr($id, 1));
            $people[] = $this->person($id, $parent,
                in_array($p['relation'] ?? '', ShajraEngine::RELATIONS, true) ? $p['relation'] : 'son',
                $str($p['name'] ?? ''),
                in_array($p['status'] ?? '', self::STATUSES, true) ? $p['status'] : 'alive',
                [
                    'cnic' => $str($p['cnic'] ?? '', 15),
                    'mother' => $str($p['mother'] ?? '', 12),
                    'minor' => ! empty($p['minor']),
                    'guardian' => $str($p['guardian'] ?? ''),
                    'married' => $this->yesNo($p['married'] ?? ''),
                    'issue' => $this->yesNo($p['issue'] ?? ''),
                ]);
        }
        $this->people = $people;
        foreach (array_keys($this->people) as $i) {
            $this->cleanPerson($i);
        }
        $this->nextId = $max + 1;
        $this->resetForm('root');
    }

    public function updated($name): void
    {
        if (str_starts_with($name, 'record.') && in_array(substr($name, 7), ['kanal', 'marla', 'sarsahi'], true)) {
            $this->record[substr($name, 7)] = $this->num($this->record[substr($name, 7)]);
        }
        if (in_array($name, ['record.w1_cnic', 'record.w2_cnic'], true)) {
            $k = substr($name, 7);
            $this->record[$k] = substr(preg_replace('/\D/', '', (string) $this->record[$k]), 0, 13);
        }
        if ($name === 'record.fiqh' && ! in_array($this->record['fiqh'], self::FIQH, true)) {
            $this->record['fiqh'] = 'hanafi';
        }
        if ($name === 'deceased.gender') {
            $this->deceased['gender'] = $this->deceased['gender'] === 'female' ? 'female' : 'male';
            if ($this->deceased['gender'] === 'male' && $this->deceased['father_rel'] !== 'so') {
                $this->deceased['father_rel'] = 'so';
            } elseif ($this->deceased['gender'] === 'female' && $this->deceased['father_rel'] === 'so') {
                $this->deceased['father_rel'] = 'do';
            }
        }
        if ($name === 'deceased.father_rel' && ! in_array($this->deceased['father_rel'], ['so', 'do', 'wo'], true)) {
            $this->deceased['father_rel'] = 'so';
        }
        if (in_array($name, ['form.parent', 'form.relation', 'form.status', 'form.married'], true)) {
            $this->fixForm();
        }
        if (in_array($name, ['deceased.married', 'deceased.issue'], true)) {
            $this->deceased['married'] = $this->yesNo($this->deceased['married']);
            $this->deceased['issue'] = $this->deceased['married'] === 'no' ? '' : $this->yesNo($this->deceased['issue']);
            $this->pruneChildrenOf('root');
        }
        if (preg_match('/^people\.(\d+)\.(status|mother|minor|married|issue)$/', $name, $m)) {
            $this->cleanPerson((int) $m[1]);
            if (in_array($m[2], ['status', 'married', 'issue'], true) && isset($this->people[(int) $m[1]])) {
                $this->pruneChildrenOf($this->people[(int) $m[1]]['id']);
            }
        }
    }

    public function addPerson(): void
    {
        $parents = $this->parentOptions();
        $this->validate([
            'form.name' => 'required|string|max:120',
            'form.cnic' => ['nullable', 'regex:/^\d{5}-?\d{7}-?\d$/'],
            'form.guardian' => 'nullable|string|max:120',
            'form.parent' => 'required|in:' . implode(',', array_keys($parents)),
            'form.status' => 'required|in:' . implode(',', self::STATUSES),
        ], [], [
            'form.name' => __('Name'), 'form.cnic' => __('CNIC'),
        ]);

        if (count($this->people) >= self::MAX_PEOPLE) {
            $this->addError('form.name', __('A family tree can have up to :max people.', ['max' => self::MAX_PEOPLE]));

            return;
        }

        $allowed = $this->allowedFor($this->form['parent']);
        if (! in_array($this->form['relation'], $allowed, true)) {
            $this->addError('form.relation', __('This relation cannot be added here.'));

            return;
        }

        $limit = ShajraEngine::LIMITS[$this->form['relation']] ?? null;
        if ($limit !== null) {
            $same = collect($this->people)->where('parent', $this->form['parent'])->where('relation', $this->form['relation'])->count();
            if ($same >= $limit) {
                $this->addError('form.relation', __('Only :max allowed for this relation.', ['max' => $limit]));

                return;
            }
        }

        if (! in_array($this->form['status'], $this->statusesFor($this->form['relation']), true)) {
            $this->addError('form.status', __('This status does not apply to this relation.'));

            return;
        }

        $id = 'n' . $this->nextId++;
        $this->people[] = $this->person($id, $this->form['parent'], $this->form['relation'], trim($this->form['name']), $this->form['status'], [
            'cnic' => trim((string) $this->form['cnic']),
            'mother' => (string) $this->form['mother'],
            'minor' => (bool) $this->form['minor'],
            'guardian' => trim((string) $this->form['guardian']),
            'married' => $this->yesNo($this->form['married']),
            'issue' => $this->yesNo($this->form['issue']),
        ]);
        $this->cleanPerson(array_key_last($this->people));
        $added = end($this->people);

        // Shadi shuda fout shakhs ki aulad hai: foran usi ke neeche biwi/aulad darj karne ke liye form wahan le jao
        if ($added['married'] === 'yes' && isset($this->parentOptions()[$id])) {
            $this->resetForm($id);
            $this->dispatch('shajra-focus-form');

            return;
        }

        // Agle bachay ke liye wohi maan aur rishta rehne do; baqi khaali
        $this->form = array_merge($this->form, ['name' => '', 'cnic' => '', 'status' => 'alive', 'minor' => false, 'guardian' => '', 'married' => '', 'issue' => '']);
        $this->fixForm();
    }

    /** Fout rishtedar jin ke bare mein poocha jata hai: shadi shuda? aulad? */
    public const MARRIAGE_ASKED = ['son', 'daughter', 'brother', 'sister', 'paternal_brother', 'paternal_sister', 'nephew', 'uncle', 'cousin'];

    public function asksMarriage(string $relation, string $status): bool
    {
        return in_array($relation, self::MARRIAGE_ASKED, true) && in_array($status, ['died_after', 'died_before'], true);
    }

    private function yesNo($v): string
    {
        return in_array($v, ['yes', 'no'], true) ? $v : '';
    }

    public function removePerson(string $id): void
    {
        $drop = [$id => true];
        // Neeche wali saari shaakhein bhi
        do {
            $before = count($drop);
            foreach ($this->people as $p) {
                if (isset($drop[$p['parent']])) {
                    $drop[$p['id']] = true;
                }
            }
        } while (count($drop) > $before);

        $this->people = array_values(array_filter($this->people, fn ($p) => ! isset($drop[$p['id']])));
        foreach ($this->people as &$p) {
            if (isset($drop[$p['mother']])) {
                $p['mother'] = '';
            }
        }
        unset($p);
        if (! isset($this->parentOptions()[$this->form['parent']])) {
            $this->resetForm('root');
        } else {
            $this->fixForm();
        }
    }

    public function addUnder(string $id): void
    {
        if (isset($this->parentOptions()[$id])) {
            $this->resetForm($id);
            $this->dispatch('shajra-focus-form');
        }
    }

    /** Is rishte ke liye kaun si haalat mumkin hai */
    public function statusesFor(string $relation): array
    {
        $s = ['alive', 'died_after', 'died_before', 'missing'];
        if (in_array($relation, ['wife', 'husband'], true)) {
            $s[] = 'divorced';
        }

        return $s;
    }

    /** Is marhoom ki biwiyan/shohar, jin se aulad jori ja sakti hai */
    public function spousesOf(string $parent): array
    {
        return collect($this->people)->where('parent', $parent)->whereIn('relation', ['wife', 'husband'])->pluck('name', 'id')->all();
    }

    private function resetForm(string $parent): void
    {
        $this->form = ['parent' => $parent, 'relation' => 'son', 'name' => '', 'status' => 'alive', 'cnic' => '', 'mother' => '', 'minor' => false, 'guardian' => '', 'married' => '', 'issue' => ''];
        $this->fixForm();
        $this->resetErrorBag();
    }

    private function fixForm(): void
    {
        $allowed = $this->allowedFor($this->form['parent']);
        if (! $allowed) {
            $this->form['parent'] = 'root';
            $allowed = $this->allowedFor('root');
        }
        if (! in_array($this->form['relation'], $allowed, true)) {
            $this->form['relation'] = in_array('son', $allowed, true) ? 'son' : $allowed[0];
        }
        if (! $this->asksMarriage($this->form['relation'], $this->form['status'])) {
            $this->form['married'] = '';
        }
        if ($this->form['married'] !== 'yes') {
            $this->form['issue'] = '';
        }
        if (! in_array($this->form['status'], $this->statusesFor($this->form['relation']), true)) {
            $this->form['status'] = 'alive';
        }
        $spouses = $this->spousesOf($this->form['parent']);
        if (! in_array($this->form['relation'], ['son', 'daughter'], true) || ! isset($spouses[$this->form['mother']])) {
            // Ek hi biwi ho to wohi maan
            $this->form['mother'] = in_array($this->form['relation'], ['son', 'daughter'], true) && count($spouses) === 1 ? array_key_first($spouses) : '';
        }
    }

    /** Ek shakhs ki ghalat ya be-maani values theek karna */
    private function cleanPerson(int $i): void
    {
        if (! isset($this->people[$i])) {
            return;
        }
        $p = &$this->people[$i];
        if (! in_array($p['status'], $this->statusesFor($p['relation']), true)) {
            $p['status'] = 'alive';
        }
        $spouses = $this->spousesOf($p['parent']);
        if (! in_array($p['relation'], ['son', 'daughter'], true) || ! isset($spouses[$p['mother']])) {
            $p['mother'] = '';
        }
        $p['minor'] = (bool) $p['minor'] && in_array($p['status'], ['alive', 'missing'], true);
        if (! $p['minor']) {
            $p['guardian'] = '';
        }
        if (! $this->asksMarriage($p['relation'], $p['status'])) {
            $p['married'] = '';
        }
        if ($p['married'] !== 'yes') {
            $p['issue'] = '';
        }
        unset($p);
    }

    /** Jo rishte ab is shakhs ke neeche nahi ban sakte (zinda ho gaya, ghair shadi shuda, lawalad) woh hata do */
    private function pruneChildrenOf(string $id): void
    {
        $allowed = $this->allowedFor($id);
        foreach ($this->people as $c) {
            if ($c['parent'] === $id && ! in_array($c['relation'], $allowed, true)) {
                $this->removePerson($c['id']);
            }
        }
        if (! isset($this->parentOptions()[$this->form['parent']])) {
            $this->resetForm('root');
        } else {
            $this->fixForm();
        }
    }

    /**
     * Jin ke neeche log jor sakte hain: mutawaffi; baad mein fout har waris (us ke warisan);
     * pehle fout beta/beti (MFLO); aur pehle fout koi bhi shadi shuda rishtedar (biwi/aulad, maslan bhai ke bete = bhateeje).
     */
    private function parentOptions(): array
    {
        $opts = ['root' => $this->deceased['name'] ?: __('The deceased')];
        foreach ($this->people as $p) {
            $ok = $p['status'] === 'died_after'
                || ($p['status'] === 'died_before' && $p['married'] !== 'no'
                    && (in_array($p['relation'], ['son', 'daughter'], true) || $p['married'] === 'yes'));
            if ($ok && $this->allowedFor($p['id'], false)) {
                $opts[$p['id']] = ($p['name'] ?: '—') . ' (' . __(ucfirst($p['relation'])) . ')';
            }
        }

        return $opts;
    }

    private function allowedFor(string $parent, bool $checkParent = true): array
    {
        if ($parent === 'root') {
            $who = ['gender' => $this->deceased['gender'], 'status' => 'died_after', 'married' => $this->deceased['married'], 'issue' => $this->deceased['issue']];
        } else {
            $p = collect($this->people)->firstWhere('id', $parent);
            if (! $p || $p['status'] === 'alive' || ($checkParent && ! isset($this->parentOptions()[$parent]))) {
                return [];
            }
            $who = ['gender' => ShajraEngine::genderOf($p['relation']), 'status' => $p['status'], 'married' => $p['married'], 'issue' => $p['issue']];
        }

        $rels = ShajraEngine::allowedRelations($who['gender'], $who['status'] === 'died_before' ? 'died_before' : 'alive');
        // Ghair shadi shuda: na shohar/biwi, na aulad. Lawalad: aulad nahi
        $spouse = $who['gender'] === 'male' ? 'wife' : 'husband';
        if ($who['married'] === 'no') {
            $rels = array_diff($rels, [$spouse, 'son', 'daughter']);
        } elseif ($who['issue'] === 'no') {
            $rels = array_diff($rels, ['son', 'daughter']);
        }

        return array_values($rels);
    }

    private function num($v): float
    {
        return is_numeric($v) ? min(1_000_000, max(0.0, (float) $v)) : 0.0;
    }

    public function render()
    {
        $totalMarlas = $this->record['kanal'] * 20 + $this->record['marla'] + $this->record['sarsahi'] / 9;
        $result = ShajraEngine::build($this->deceased['gender'], $this->people, $totalMarlas);

        $root = ['id' => 'root', 'parent' => '', 'relation' => 'root', 'status' => 'root', 'cnic' => $this->deceased['cnic'],
            'name' => $this->deceased['name'], 'father_name' => $this->deceased['father_name'], 'father_rel' => $this->deceased['father_rel']];
        // Patwar ke khaake ki tarah: mard baayen, zauja daayen (Urdu mein bhi ulta nahi)
        $layout = ShajraLayout::build($root, $this->people);

        return view('livewire.tools.shajra-nasab', [
            'result' => $result,
            'layout' => $layout,
            'parentOptions' => $this->parentOptions(),
            'formRelations' => $this->allowedFor($this->form['parent']),
            'formParentStatus' => collect($this->people)->firstWhere('id', $this->form['parent'])['status'] ?? 'root',
            'formSpouses' => $this->spousesOf($this->form['parent']),
            'totalMarlas' => $totalMarlas,
            'names' => collect($this->people)->pluck('name')->filter()->unique()->values(),
        ])->layout('layouts.front-app', ToolCatalog::meta('shajra'));
    }
}
