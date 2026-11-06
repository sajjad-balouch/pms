@php
    use App\Support\Tools\ShajraEngine;

    $isUr = app()->getLocale() === 'ur';
    $statusLabels = [
        'alive' => __('Alive'),
        'died_after' => __('Died after the deceased'),
        'died_before' => __('Died before the deceased'),
        'missing' => __('Missing (mafqood-ul-khabar)'),
        'divorced' => __('Divorced'),
    ];
    $stateLabels = [
        'blocked' => __('Excluded by a closer heir'),
        'predeceased' => __('No share: died before'),
        'divorced' => __('No share: divorced'),
        'invalid' => __('Not counted'),
        'no_share' => __('No share'),
        'too_deep' => __('Not counted'),
        'passed_on' => __('Share passed to own heirs'),
        'mflo' => __('Share to children (MFLO s.4)'),
    ];
    $ordinals = $isUr ? ['اول', 'دوم', 'سوم', 'چہارم'] : ['1', '2', '3', '4'];
    $fatherRels = ['so' => __('s/o'), 'do' => __('d/o'), 'wo' => __('w/o')];

    // Patwar ki zaban mein rishta: pisar, dukhtar, bewa waghera
    $relWords = $isUr
        ? ['father' => 'والد', 'mother' => 'والدہ', 'husband' => 'خاوند', 'son' => 'پسر', 'daughter' => 'دختر', 'brother' => 'برادر حقیقی', 'sister' => 'ہمشیرہ حقیقی', 'wife' => 'زوجہ', 'widow' => 'بیوہ', 'grandfather' => 'دادا', 'paternal_brother' => 'برادر علاتی (باپ شریک)', 'paternal_sister' => 'ہمشیرہ علاتی (باپ شریک)', 'nephew' => 'بھتیجا (حقیقی بھائی کا بیٹا)', 'uncle' => 'چچا (حقیقی)', 'cousin' => 'چچا زاد (حقیقی چچا کا بیٹا)']
        : ['father' => 'Father', 'mother' => 'Mother', 'husband' => 'Husband', 'son' => 'Son', 'daughter' => 'Daughter', 'brother' => 'Full brother', 'sister' => 'Full sister', 'wife' => 'Wife', 'widow' => 'Widow', 'grandfather' => 'Paternal grandfather', 'paternal_brother' => 'Half brother (same father)', 'paternal_sister' => 'Half sister (same father)', 'nephew' => 'Nephew (full brother’s son)', 'uncle' => 'Paternal uncle (full)', 'cousin' => 'Cousin (full paternal uncle’s son)'];
    // Shajra sarkari kaghaz hai: us ke andar rishte hamesha Urdu mein (site ki zaban kuch bhi ho)
    $rel = function (array $node, ?array $card) use ($deceased) {
        $words = \App\Support\Tools\ShajraReport::REL;
        if ($node['relation'] === 'root') {
            return $deceased['gender'] === 'female' ? 'مورثہ (متوفیہ)' : 'مورث (متوفی)';
        }
        if ($node['relation'] === 'wife') {
            // Mard ki wafat par zinda zauja "bewa" kehlati hai
            $word = match ($node['status']) { 'alive', 'missing' => 'بیوہ', 'divorced' => 'مطلقہ', default => 'زوجہ' };
            if ($card && ($card['of'] ?? 1) > 1) {
                $word .= ' ' . ['اول', 'دوم', 'سوم', 'چہارم'][$card['ord'] - 1];
            }

            return $word;
        }

        return $words[$node['relation']] ?? $node['relation'];
    };
    $childrenCount = collect($people)->groupBy('parent')->map(fn ($g) => $g->whereIn('relation', ['son', 'daughter'])->count());
    // Patwar ki alamaat, naam ke neeche narangi rang mein (har zaban mein Urdu hi, jaise record mein)
    $signsOf = function (array $node, string $state) use ($childrenCount, $deceased) {
        $status = $node['status'] ?? '';
        $s = [];
        if ($status === 'root') {
            $s[] = $deceased['gender'] === 'female' ? 'متوفیہ' : 'متوفی';
        } elseif ($status === 'died_after') {
            $s[] = 'فوت';
        } elseif ($status === 'died_before') {
            $s[] = 'پہلے فوت';
        } elseif ($status === 'missing') {
            $s[] = 'مفقود الخبر';
        } elseif ($status === 'divorced') {
            $s[] = 'مطلقہ';
        } elseif ($status === 'alive') {
            $s[] = 'ز';
        }
        // Khud se andaaza nahi: sirf wahi likho jo darj karne wale ne bataya
        $who = $status === 'root' ? $deceased : $node;
        if (($who['married'] ?? '') === 'no') {
            $s[] = 'غیر شادی شدہ';
        } elseif (($who['issue'] ?? '') === 'no') {
            $s[] = 'لاولد';
        }
        if (! empty($node['minor'])) $s[] = 'نابالغ';
        if ($state === 'blocked') $s[] = 'محروم';

        return $s;
    };
    $legend = [
        'ز' => __('Alive'),
        'متوفی' => __('The deceased whose estate is being divided'),
        'فوت' => __('Died after the deceased'),
        'پہلے فوت' => __('Died before the deceased'),
        'لاولد' => __('Died without children'),
        'غیر شادی شدہ' => __('Never married'),
        'نابالغ' => __('Minor, through guardian'),
        'مفقود الخبر' => __('Missing, share kept safe'),
        'مطلقہ' => __('Divorced, no share'),
        'محروم' => __('Excluded by a closer heir'),
    ];
    $hasAncestors = collect($layout['cards'])->whereIn('role', ['ancestor', 'display'])->isNotEmpty();
    $chainLabel = function (array $chain) use ($relWords) {
        $last = end($chain);
        // Hissa lene wali zauja hamesha kisi fout shakhs ki bewa hai
        $r = $last['relation'] === 'wife' ? $relWords['widow'] : ($relWords[$last['relation']] ?? $last['relation']);
        if (count($chain) < 2) {
            return $r;
        }
        $via = $chain[count($chain) - 2];

        return __(':relation (through :name)', ['relation' => $r, 'name' => ($via['name'] ?: '—') . ' / ' . ($relWords[$via['relation']] ?? '')]);
    };
    $warningText = function (array $w) {
        return match ($w['key']) {
            'no_heirs' => __('No heirs added yet. Add the heirs of the deceased to see the shares.'),
            'no_heirs_sub' => __('A person who died has no heirs added under them, so their share is not given to anyone yet.'),
            'unassigned' => __('Part of an estate has no sharer or residuary heir here. In Pakistan (Hanafi law) it goes first to distant kindred (zawil arham) such as a daughter\'s or sister\'s children, then to the husband or wife by return, and only if there is nobody to the government. Please ask a scholar.'),
            'limit' => __('Only :max allowed for this relation. Extra entries are not counted.', ['max' => $w['max']]) . ' (' . __(ucfirst($w['relation'])) . ')',
            'too_deep' => __('The tree is too deep. Only :max generations are worked out.', ['max' => ShajraEngine::MAX_DEPTH]),
            default => '',
        };
    };
    $fiqhLabels = ['hanafi' => 'حنفی (سنی)', 'jafari' => 'جعفریہ (شیعہ)', 'ahl_hadith' => 'اہل حدیث', 'other' => $isUr ? 'دیگر (ہاتھ سے لکھیں)' : 'Other (write by hand)'];
    $fmt = fn ($n, $d = 4) => rtrim(rtrim(number_format($n, $d), '0'), '.');
    $input = 'w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white outline-none focus:border-amber-500';
    $lbl = 'block text-[10px] font-bold text-slate-400 uppercase mb-1';
@endphp
<div class="min-h-screen bg-[#070a13] text-slate-100 py-10 px-4 sm:px-6 lg:px-8 w-full overflow-x-hidden"
     x-data="{
        key: 'az_shajra_v2',
        save() {
            try {
                localStorage.setItem(this.key, JSON.stringify({ deceased: $wire.deceased, record: $wire.record, people: $wire.people, report: $wire.report }));
            } catch (e) {}
        },
        init() {
            try {
                const saved = localStorage.getItem(this.key) || localStorage.getItem('az_shajra_v1');
                if (saved) { $wire.restore(JSON.parse(saved)); }
            } catch (e) {}
            Livewire.hook('commit', ({ component, succeed }) => {
                if (component.id !== $wire.$id) return;
                succeed(() => queueMicrotask(() => this.save()));
            });
        }
     }"
     @shajra-focus-form.window="$nextTick(() => { $refs.addForm.scrollIntoView({ behavior: 'smooth', block: 'center' }); $refs.nameInput.focus(); })">

    <style>
        .shajra-inputs { display: grid; gap: 1.25rem; align-items: start; }
        @media (min-width: 768px) { .shajra-inputs { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (min-width: 1200px) { .shajra-inputs { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
        .shajra-edit-list { max-height: 30rem; overflow-y: auto; padding-inline-end: 4px; }
        .shajra-signs-row { display: none; }

        /* Patwar ka kaghaz: hari siyahi, narangi alamaat */
        .pw-paper { --ink: #047857; --mark: #ea580c; background: #fffdf6; border: 1px solid #e7e0c9; border-radius: 18px; padding: 14px; color: var(--ink); }
        /* Sarkari form ka saancha */
        .pf-form { color: #111827; font-family: 'Jameel Noori Nastaleeq', 'Noto Nastaliq Urdu', serif; margin-bottom: 14px; }
        .pf-head { display: flex; align-items: center; justify-content: space-between; gap: 16px; border-bottom: 3px solid #111827; padding-bottom: 8px; }
        .pf-headline { font-size: 22px; line-height: 2.1; }
        .pf-blank { display: inline-block; min-width: 90px; border-bottom: 1.5px dashed #111827; text-align: center; padding: 0 6px; line-height: 1.6; font-weight: 700; }
        .pf-blank.pf-wide { min-width: 300px; }
        .pf-logo { width: 74px; height: 74px; flex: none; border: 1.5px dashed #cbd5e1; border-radius: 999px; }
        .pf-body { display: flex; flex-wrap: wrap; justify-content: space-between; gap: 18px 28px; padding-top: 16px; }
        .pf-main { flex: 1 1 320px; }
        .pf-title { font-size: 40px; font-weight: 700; line-height: 1.6; margin: 0 0 10px; }
        .pf-field { font-size: 19px; line-height: 2.4; }
        .pf-meta { display: flex; flex-wrap: wrap; gap: 2px 16px; font-size: 14px; line-height: 2; color: #374151; margin-top: 6px; }
        .pf-witnesses { flex: 0 1 470px; display: flex; flex-direction: column; gap: 14px; }
        .pf-wit { width: 100%; border-collapse: collapse; font-size: 13px; }
        .pf-wit th, .pf-wit td { border: 1px solid #6b7280; padding: 2px 6px; height: 30px; vertical-align: middle; }
        .pf-wit th { width: 150px; font-weight: 400; text-align: start; white-space: nowrap; }
        .pf-wit td { font-weight: 700; font-size: 15px; }
        .pf-wit td.pf-sign { height: 34px; }
        .pf-cnic { display: flex; align-items: stretch; justify-content: flex-end; height: 26px; margin: -2px -6px; }
        .pf-cell { width: 19px; border-inline-start: 1px solid #9ca3af; display: flex; align-items: center; justify-content: center; font-family: ui-monospace, monospace; font-size: 13px; }
        .pf-dash { width: 14px; display: flex; align-items: center; justify-content: center; border-inline-start: 1px solid #9ca3af; }
        /* Shajra aur report ka box saath saath (landscape) */
        .pf-row { display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-start; }
        .pf-tree { flex: 1 1 520px; min-width: 0; }
        .pf-report { flex: 0 0 380px; max-width: 100%; color: #111827; font-family: 'Jameel Noori Nastaleeq', 'Noto Nastaliq Urdu', serif;
            border: 1.5px solid #111827; border-radius: 8px; padding: 10px 14px; background: #fff; }
        .pf-report-title { display: block; width: 100%; font-size: 20px; font-weight: 700; text-decoration: underline; text-underline-offset: 6px; margin: 0 0 4px; line-height: 1.9; }
        .pf-report-text { font-size: 14.5px; line-height: 2.05; text-align: justify; white-space: pre-wrap; }
        .pf-edit { font-family: inherit; color: inherit; background: #fffef9; border: 1px dashed #cbd5e1; border-radius: 6px; padding: 2px 6px; outline: none; }
        .pf-edit:focus { border-color: #f59e0b; background: #fffbeb; }
        textarea.pf-report-text { display: block; width: 100%; min-height: 360px; field-sizing: content; resize: vertical; }
        .pf-print-only { display: none; }
        .pf-report-tools { display: flex; flex-wrap: wrap; align-items: center; gap: 4px 10px; margin-top: 4px; font: 11px/1.5 system-ui, sans-serif; color: #64748b; }
        .pf-report-tools button { cursor: pointer; border-radius: 6px; background: #fef3c7; color: #92400e; padding: 1px 8px; font-weight: 700; }
        .pf-report-sign { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-top: 30px; font-size: 12.5px; line-height: 1.8; text-align: center; }
        .pf-sign-line { display: block; border-top: 1px solid #111827; margin: 0 12px 2px; }
        .sh-wrap { overflow-x: auto; padding-bottom: 6px; }
        .sh-canvas { position: relative; margin: 0 auto; }
        .sh-canvas > svg { position: absolute; inset: 0; overflow: visible; }
        .sh-canvas line { stroke: var(--ink); }
        .pw-node { position: absolute; text-align: center; color: var(--ink); }
        .pw-shape { position: absolute; top: 0; left: 0; overflow: visible; stroke: var(--ink); pointer-events: none; }
        .pw-name { position: absolute; top: 16px; left: 0; right: 0; height: 28px; line-height: 28px; padding: 0 16px;
            font-size: 15px; font-weight: 800; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .pw-name > span { background: #fffdf6; padding: 0 5px; }
        /* Patta khaali: kaghazi rang se bhara taa-ke neeche ki line chhup jaye, andar sirf naam */
        .pw-leaf-body { fill: #fffdf6; stroke: var(--ink); }
        .pw-actions { z-index: 2; }
        .pw-root .pw-name { font-size: 16px; text-decoration: underline; text-decoration-thickness: 1px; text-underline-offset: 5px; }
        .pw-sign { position: absolute; top: 45px; left: 0; right: 0; font-size: 12px; font-weight: 700; color: var(--mark); line-height: 16px;
            font-family: 'Jameel Noori Nastaleeq', 'Noto Nastaliq Urdu', serif; white-space: nowrap; }
        .pw-share { position: absolute; top: 61px; left: 0; right: 0; font-family: ui-monospace, monospace; font-size: 11px; font-weight: 800; color: #334155; line-height: 14px; }
        .pw-muted .pw-name { color: #6b7280; }
        .pw-list { position: absolute; top: 78px; left: 4px; right: 4px; border-top: 1px dashed #a7b8ad; padding-top: 3px; text-align: start; }
        .pw-list-row { font-size: 10px; line-height: 15px; height: 15px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--ink); }
        .pw-list-rel { color: #64748b; }
        .pw-caption { color: #64748b; margin-inline-end: 4px; }
        .pw-list-sign { color: var(--mark); margin-inline-start: 3px; }
        .pw-list-share { font-family: ui-monospace, monospace; color: #334155; font-weight: 800; margin-inline-start: 3px; }
        .pw-actions { position: absolute; top: 0; right: 0; display: flex; gap: 2px; opacity: .25; transition: opacity .15s; }
        .pw-node:hover .pw-actions { opacity: 1; }
        .pw-actions button { cursor: pointer; width: 18px; height: 18px; border-radius: 999px; background: #ecfdf5; color: var(--ink); font-size: 10px; line-height: 18px; }
        .pw-actions .pw-del { background: #fff1f2; color: #e11d48; }
        .pw-legend { display: flex; flex-wrap: wrap; gap: 6px 16px; font-size: 11px; color: #334155; border-top: 1px dashed #d6cfb5; padding-top: 10px; margin-top: 6px; }
        .pw-legend > span { display: inline-flex; align-items: center; gap: 5px; }
        .pw-legend b { color: var(--mark); font-family: 'Jameel Noori Nastaleeq', 'Noto Nastaliq Urdu', serif; font-size: 13px; }
        .pw-legend svg { flex: none; stroke: var(--ink); }

        @media print {
            header, footer, nav, .glass-nav, .no-print, [wire\:loading] { display: none !important; }
            body, html { background: #fff !important; color: #000 !important; margin: 0 !important; padding: 0 !important;
                -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .shajra-page { padding: 0 !important; background: #fff !important; min-height: 0 !important; }
            #shajra-sheet { border: 2px solid #0f172a !important; border-radius: 0 !important; background: #fff !important; box-shadow: none !important; padding: 10px !important; }
            #shajra-sheet * { color: #000 !important; }
            #shajra-sheet .pw-paper { border: 0 !important; padding: 0 !important; }
            #shajra-sheet .pw-node, #shajra-sheet .pw-node * { color: #065f46 !important; }
            #shajra-sheet .pw-sign, #shajra-sheet .pw-list-sign, #shajra-sheet .pw-legend b { color: #ea580c !important; }
            #shajra-sheet table, #shajra-sheet th, #shajra-sheet td { border-color: #94a3b8 !important; }
            #shajra-sheet .bg-slate-950, #shajra-sheet .bg-amber-500\/10 { background: #fff !important; }
            .sh-wrap { overflow: visible !important; }
            /* Print mein sirf form + shajra + report: legend, hisson ki table, warnings, wazahat waghera nahi */
            #shajra-sheet > *:not(.pw-paper) { display: none !important; }
            .pw-legend, .pf-meta { display: none !important; }
            .pf-print-only { display: block !important; }
            .pf-row { flex-wrap: nowrap !important; }
            .pf-logo { border: 0 !important; }
            .pf-form, .pf-form *, .pf-report, .pf-report * { color: #000 !important; }
            /* Safhe ka size neeche report ke option se (@page alag style mein) */
        }
    </style>

    <div class="shajra-page max-w-[1400px] mx-auto space-y-8 w-full">

        <div class="text-center space-y-3 no-print w-full">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-bold uppercase tracking-wider">
                <i class="fa-solid fa-sitemap text-sm"></i>
                <span>{{ __('Hanafi method') }} &bull; MFLO 1961</span>
            </div>
            <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight pt-4">{{ __('Digital Shajra Nasab Maker') }}</h1>
            <p class="text-sm text-slate-400 max-w-2xl mx-auto pt-4">
                {{ __('Draw the family tree of the deceased for an inheritance mutation, with each heir\'s share of the land, including heirs who died later.') }}
            </p>
        </div>

        {{-- Inputs --}}
        <div class="shajra-inputs no-print">
            <div class="space-y-5">
                <div class="bg-slate-900/90 rounded-3xl border border-slate-800 p-5 space-y-3">
                    <h3 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2 border-b border-slate-800 pb-3">
                        <i class="fa-solid fa-user text-amber-400"></i> {{ __('The deceased') }}
                    </h3>
                    <div>
                        <label class="{{ $lbl }}">{{ __('Name') }}</label>
                        <input type="text" dir="auto" wire:model.live.debounce.400ms="deceased.name" class="{{ $input }} text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach(['male' => __('Male'), 'female' => __('Female')] as $g => $label)
                            <button type="button" wire:click="$set('deceased.gender', '{{ $g }}')"
                                    class="py-2 rounded-xl text-xs font-bold cursor-pointer {{ $deceased['gender'] === $g ? 'bg-amber-500 text-slate-950' : 'bg-slate-950 text-slate-400 border border-slate-800' }}">{{ $label }}</button>
                        @endforeach
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <label class="{{ $lbl }}">{{ __('Relation') }}</label>
                            <select wire:model.live="deceased.father_rel" class="{{ $input }} px-2">
                                @foreach($fatherRels as $k => $label)
                                    @if($deceased['gender'] === 'female' || $k === 'so')<option value="{{ $k }}">{{ $label }}</option>@endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="{{ $lbl }}">{{ __('Father\'s / husband\'s name') }}</label>
                            <input type="text" dir="auto" wire:model.live.debounce.400ms="deceased.father_name" class="{{ $input }}">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="{{ $lbl }}">{{ __('Caste (qaum)') }}</label>
                            <input type="text" dir="auto" wire:model.live.debounce.400ms="deceased.caste" class="{{ $input }}">
                        </div>
                        <div>
                            <label class="{{ $lbl }}">{{ __('Date of death') }}</label>
                            <input type="date" wire:model.live="deceased.death_date" class="{{ $input }}">
                        </div>
                        <div class="col-span-2">
                            <label class="{{ $lbl }}">{{ __('CNIC') }}</label>
                            <input type="text" dir="ltr" inputmode="numeric" placeholder="35202-0000000-0" wire:model.live.debounce.400ms="deceased.cnic" class="{{ $input }} font-mono">
                        </div>
                        <div>
                            <label class="{{ $lbl }}">{{ __('Was married?') }}</label>
                            <select wire:model.live="deceased.married" class="{{ $input }} px-2">
                                <option value="">{{ __('Not specified') }}</option>
                                <option value="yes">{{ __('Yes') }}</option>
                                <option value="no">{{ __('No, never married') }}</option>
                            </select>
                        </div>
                        @if($deceased['married'] === 'yes')
                            <div>
                                <label class="{{ $lbl }}">{{ __('Has children?') }}</label>
                                <select wire:model.live="deceased.issue" class="{{ $input }} px-2">
                                    <option value="">{{ __('Not specified') }}</option>
                                    <option value="yes">{{ __('Yes') }}</option>
                                    <option value="no">{{ __('No (la-walad)') }}</option>
                                </select>
                            </div>
                        @endif
                    </div>
                    <p class="text-[10px] text-slate-500 leading-relaxed">{{ __('The father is drawn above the deceased from this name. If the father is also an heir or you want to mark him deceased, add him as an heir with the relation Father.') }}</p>
                </div>

                <div class="bg-slate-900/90 rounded-3xl border border-slate-800 p-5 space-y-3">
                    <h3 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2 border-b border-slate-800 pb-3">
                        <i class="fa-solid fa-map-location-dot text-emerald-400"></i> {{ __('Land record') }}
                    </h3>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach(['kanal' => __('Kanal'), 'marla' => __('Marla'), 'sarsahi' => __('Sarsahi')] as $k => $label)
                            <div>
                                <label class="{{ $lbl }}">{{ $label }}</label>
                                <input type="number" min="0" step="any" wire:model.live.debounce.400ms="record.{{ $k }}" class="{{ $input }} font-mono font-bold text-center px-2">
                            </div>
                        @endforeach
                    </div>
                    <p class="text-[10px] text-slate-500">{{ __('Revenue units: 1 kanal = 20 marla, 1 marla = 9 sarsahi.') }}</p>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach(['markaz' => __('Rural revenue centre (qanungoi)'), 'mauza' => __('Mauza'), 'tehsil' => __('Tehsil'), 'district' => __('District name'), 'khewat' => __('Khewat no.'), 'khatoni' => __('Khatoni no.'), 'intiqal' => __('Mutation no.')] as $k => $label)
                            <div @if($k === 'markaz') class="col-span-2" @endif>
                                <label class="{{ $lbl }}">{{ $label }}</label>
                                <input type="text" dir="auto" wire:model.live.debounce.400ms="record.{{ $k }}" class="{{ $input }} px-2 py-1.5">
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Sarkari form ke khaane --}}
                <div class="bg-slate-900/90 rounded-3xl border border-slate-800 p-5 space-y-3">
                    <h3 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2 border-b border-slate-800 pb-3">
                        <i class="fa-solid fa-file-signature text-sky-400"></i> {{ __('Form details and witnesses') }}
                    </h3>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="col-span-2">
                            <label class="{{ $lbl }}">{{ __('As stated by (hasb-e-guftah)') }}</label>
                            <input type="text" dir="auto" list="shajra-names" wire:model.live.debounce.400ms="record.informant" class="{{ $input }}">
                        </div>
                        <div class="col-span-2">
                            <label class="{{ $lbl }}">{{ __('Fiqh / maslak') }}</label>
                            <select wire:model.live="record.fiqh" class="{{ $input }}">
                                @foreach($fiqhLabels as $k => $label)<option value="{{ $k }}">{{ $label }}</option>@endforeach
                            </select>
                            @if($record['fiqh'] === 'jafari')
                                <p class="mt-1 text-[10px] text-amber-300">{{ __('The shares here follow Hanafi law. Fiqh Jafaria divides differently, so get the shares checked by a Shia scholar.') }}</p>
                            @endif
                        </div>
                        @foreach([1, 2] as $n)
                            <div>
                                <label class="{{ $lbl }}">{{ __('Witness :n name', ['n' => $n]) }}</label>
                                <input type="text" dir="auto" wire:model.live.debounce.400ms="record.w{{ $n }}_name" class="{{ $input }}">
                            </div>
                            <div>
                                <label class="{{ $lbl }}">{{ __('Witness :n CNIC', ['n' => $n]) }}</label>
                                <input type="text" dir="ltr" inputmode="numeric" maxlength="15" placeholder="3520200000000" wire:model.live.debounce.400ms="record.w{{ $n }}_cnic" class="{{ $input }} font-mono">
                            </div>
                        @endforeach
                    </div>
                    <p class="text-[10px] text-slate-500">{{ __('Signatures and thumb impressions are left blank for the witnesses to sign on paper.') }}</p>
                </div>

                {{-- Patwari report ke options --}}
                <div class="bg-slate-900/90 rounded-3xl border border-slate-800 p-5 space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                        <h3 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-file-pen text-emerald-400"></i> {{ __('Patwari report') }}
                        </h3>
                        <label class="flex items-center gap-1.5 text-xs text-slate-300 cursor-pointer">
                            <input type="checkbox" wire:model.live="report.on" class="rounded bg-slate-950 border-slate-600 text-amber-500"> {{ __('Show under the shajra') }}
                        </label>
                    </div>
                    @if($report['on'])
                        <p class="text-[10px] text-slate-400">{{ __('The report is written from the shajra. Tick only the documents you have actually checked.') }}</p>
                        <div class="space-y-1.5">
                            @foreach(['death_cert' => __('Death certificate from the Union Council checked'), 'frc' => __('NADRA Family Registration Certificate (FRC) checked'), 'cnic' => __('CNICs of the heirs checked')] as $k => $label)
                                <label class="flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                                    <input type="checkbox" wire:model.live="report.{{ $k }}" class="rounded bg-slate-950 border-slate-600 text-amber-500"> {{ $label }}
                                </label>
                            @endforeach
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="{{ $lbl }}">{{ __('Patwari name') }}</label>
                                <input type="text" dir="auto" wire:model.live.debounce.400ms="report.patwari" class="{{ $input }}">
                            </div>
                            <div>
                                <label class="{{ $lbl }}">{{ __('Report date') }}</label>
                                <input type="date" wire:model.live="report.date" class="{{ $input }}">
                            </div>
                            <div class="col-span-2">
                                <label class="{{ $lbl }}">{{ __('Extra remarks (optional)') }}</label>
                                <textarea dir="auto" rows="2" maxlength="600" wire:model.live.debounce.600ms="report.remarks" class="{{ $input }}"></textarea>
                            </div>
                        </div>
                    @endif
                    {{-- Safhe ka size report ke bina bhi kaam aata hai (form + shajra ek safhe par) --}}
                    <div>
                        <label class="{{ $lbl }}">{{ __('Print page') }}</label>
                        <select wire:model.live="report.page" class="{{ $input }}">
                            <option value="a4l">{{ __('A4 landscape') }}</option>
                            <option value="legal">{{ __('Legal landscape') }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div x-ref="addForm" class="bg-slate-900/90 rounded-3xl border border-amber-500/30 p-5 space-y-3">
                <h3 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2 border-b border-slate-800 pb-3">
                    <i class="fa-solid fa-user-plus text-amber-400"></i> {{ __('Add an heir') }}
                </h3>
                <form wire:submit="addPerson" class="space-y-3">
                    <div>
                        <label class="{{ $lbl }}">{{ __('Heir of') }}</label>
                        <select wire:model.live="form.parent" class="{{ $input }}">
                            @foreach($parentOptions as $id => $label)
                                <option value="{{ $id }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @if($formParentStatus === 'died_before')
                            <p class="mt-1 text-[10px] text-sky-300">{{ __('This person died before the deceased. Only their children can take their share.') }}</p>
                        @endif
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="{{ $lbl }}">{{ __('Relation') }}</label>
                            <select wire:model.live="form.relation" class="{{ $input }} px-2">
                                @foreach($formRelations as $r)
                                    <option value="{{ $r }}">{{ $relWords[$r] }}</option>
                                @endforeach
                            </select>
                            @error('form.relation')<p class="mt-1 text-[10px] text-rose-400">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="{{ $lbl }}">{{ __('Status') }}</label>
                            <select wire:model.live="form.status" class="{{ $input }} px-2">
                                @foreach($this->statusesFor($form['relation']) as $s)
                                    <option value="{{ $s }}">{{ $statusLabels[$s] }}</option>
                                @endforeach
                            </select>
                            @error('form.status')<p class="mt-1 text-[10px] text-rose-400">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    @if(in_array($form['relation'], ['son', 'daughter']) && count($formSpouses) > 0)
                        <div>
                            <label class="{{ $lbl }}">{{ __('Born of (mother / father)') }}</label>
                            <select wire:model.live="form.mother" class="{{ $input }}">
                                <option value="">{{ __('Not specified') }}</option>
                                @foreach($formSpouses as $sid => $sname)
                                    <option value="{{ $sid }}">{{ $sname ?: '—' }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    @if($this->asksMarriage($form['relation'], $form['status']))
                        {{-- Fout rishtedar: khud se lawalad nahi likhte, poochte hain --}}
                        <div class="grid grid-cols-2 gap-2 p-2.5 rounded-xl bg-sky-500/10 border border-sky-500/30">
                            <div>
                                <label class="{{ $lbl }}">{{ __('Was married?') }}</label>
                                <select wire:model.live="form.married" class="{{ $input }} px-2">
                                    <option value="">{{ __('Not specified') }}</option>
                                    <option value="yes">{{ __('Yes') }}</option>
                                    <option value="no">{{ __('No, never married') }}</option>
                                </select>
                            </div>
                            @if($form['married'] === 'yes')
                                <div>
                                    <label class="{{ $lbl }}">{{ __('Has children?') }}</label>
                                    <select wire:model.live="form.issue" class="{{ $input }} px-2">
                                        <option value="">{{ __('Not specified') }}</option>
                                        <option value="yes">{{ __('Yes') }}</option>
                                        <option value="no">{{ __('No (la-walad)') }}</option>
                                    </select>
                                </div>
                            @endif
                            @if($form['married'] === 'yes')
                                <p class="col-span-2 text-[10px] text-sky-200">{{ __('After adding, the form moves under this person so you can add their spouse and children.') }}</p>
                            @endif
                        </div>
                    @endif
                    @if($form['status'] === 'died_before' && in_array($form['relation'], ['brother', 'uncle']))
                        <p class="text-[10px] text-sky-300">{{ __('He gets no share himself, but his sons (alive) inherit as asaba: a brother\'s sons as nephews, an uncle\'s sons as cousins. Add them under him.') }}</p>
                    @elseif($form['status'] === 'died_before' && ! in_array($form['relation'], ['son', 'daughter']))
                        <p class="text-[10px] text-sky-300">{{ __('Shown in the shajra only. A relative who died before the deceased gets no share.') }}</p>
                    @endif
                    <div>
                        <label class="{{ $lbl }}">{{ __('Name') }}</label>
                        <input x-ref="nameInput" type="text" dir="auto" list="shajra-names" wire:model="form.name" class="{{ $input }} text-sm">
                        <datalist id="shajra-names">
                            @foreach($names as $n)<option value="{{ $n }}"></option>@endforeach
                        </datalist>
                        @error('form.name')<p class="mt-1 text-[10px] text-rose-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="{{ $lbl }}">{{ __('CNIC (optional)') }}</label>
                        <input type="text" dir="ltr" inputmode="numeric" placeholder="35202-0000000-0" wire:model="form.cnic" class="{{ $input }} font-mono">
                        @error('form.cnic')<p class="mt-1 text-[10px] text-rose-400">{{ __('Enter the CNIC as 13 digits.') }}</p>@enderror
                    </div>
                    @if(in_array($form['status'], ['alive', 'missing']))
                        <div class="grid grid-cols-[auto_1fr] items-end gap-2">
                            <label class="flex items-center gap-1.5 text-xs text-slate-300 pb-2 cursor-pointer">
                                <input type="checkbox" wire:model.live="form.minor" class="rounded bg-slate-950 border-slate-600 text-amber-500">
                                {{ __('Minor') }}
                            </label>
                            @if($form['minor'])
                                <div>
                                    <label class="{{ $lbl }}">{{ __('Guardian (wali)') }}</label>
                                    <input type="text" dir="auto" list="shajra-names" wire:model="form.guardian" class="{{ $input }}">
                                </div>
                            @endif
                        </div>
                    @endif
                    <button type="submit" class="w-full py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-sm font-black cursor-pointer">
                        <i class="fa-solid fa-plus"></i> {{ __('Add to tree') }}
                    </button>
                    <p class="text-[10px] text-slate-500 leading-relaxed">
                        {{ __('If an heir died after the deceased, mark them "Died after" and add their own heirs under them. A person who appears twice, such as a widow who is also the mother of a son who died later, is joined into one row when the name or CNIC is the same.') }}
                    </p>
                </form>
                <div class="flex gap-2 pt-2 border-t border-slate-800">
                    <button type="button" wire:click="loadSample" wire:confirm="{{ __('Replace the current tree with the example?') }}" class="flex-1 py-2 rounded-xl bg-slate-800 text-slate-200 text-xs font-bold hover:bg-slate-700 cursor-pointer">{{ __('Load example') }}</button>
                    <button type="button" wire:click="clearAll" wire:confirm="{{ __('Clear the whole tree?') }}" class="flex-1 py-2 rounded-xl bg-slate-800 text-rose-300 text-xs font-bold hover:bg-slate-700 cursor-pointer">{{ __('Start new') }}</button>
                </div>
                <p class="text-[10px] text-slate-500">{{ __('Your tree is kept only in this browser. It is not saved on our server.') }}</p>
            </div>

            <div class="bg-slate-900/90 rounded-3xl border border-slate-800 p-5 space-y-3 md:col-span-2 xl:col-span-1">
                <h3 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2 border-b border-slate-800 pb-3">
                    <i class="fa-solid fa-pen-to-square text-sky-400"></i> {{ __('Edit people') }}
                </h3>
                @if(! $people)
                    <p class="text-xs text-slate-500">{{ __('No heirs added yet. Add the heirs of the deceased to see the shares.') }}</p>
                @endif
                <div class="shajra-edit-list space-y-2">
                    @foreach($people as $i => $p)
                        @php $spouses = $this->spousesOf($p['parent']); @endphp
                        <div wire:key="edit-{{ $p['id'] }}" class="rounded-xl border border-slate-800 bg-slate-950 p-2.5 space-y-1.5">
                            <div class="flex items-center justify-between gap-2 text-[10px] text-slate-400">
                                <span><strong class="text-amber-300">{{ $relWords[$p['relation']] }}</strong> &bull; {{ __('Heir of') }} {{ $parentOptions[$p['parent']] ?? '' }}</span>
                                <button type="button" wire:click="removePerson('{{ $p['id'] }}')" wire:confirm="{{ __('Remove this person and everyone added under them?') }}" class="text-rose-300 hover:text-rose-200 cursor-pointer" title="{{ __('Remove') }}"><i class="fa-solid fa-trash-can"></i></button>
                            </div>
                            <input type="text" dir="auto" wire:model.live.debounce.500ms="people.{{ $i }}.name" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white outline-none">
                            <div class="grid grid-cols-2 gap-1.5">
                                <select wire:model.live="people.{{ $i }}.status" class="bg-slate-900 border border-slate-700 rounded-lg px-1.5 py-1 text-[11px] text-white outline-none">
                                    @foreach($this->statusesFor($p['relation']) as $s)
                                        <option value="{{ $s }}">{{ $statusLabels[$s] }}</option>
                                    @endforeach
                                </select>
                                <input type="text" dir="ltr" placeholder="{{ __('CNIC') }}" wire:model.live.debounce.500ms="people.{{ $i }}.cnic" class="bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-[11px] text-white font-mono outline-none">
                                @if($this->asksMarriage($p['relation'], $p['status']))
                                    <select wire:model.live="people.{{ $i }}.married" class="bg-slate-900 border border-slate-700 rounded-lg px-1.5 py-1 text-[11px] text-white outline-none">
                                        <option value="">{{ __('Was married?') }} —</option>
                                        <option value="yes">{{ __('Married') }}</option>
                                        <option value="no">{{ __('Never married') }}</option>
                                    </select>
                                    @if($p['married'] === 'yes')
                                        <select wire:model.live="people.{{ $i }}.issue" class="bg-slate-900 border border-slate-700 rounded-lg px-1.5 py-1 text-[11px] text-white outline-none">
                                            <option value="">{{ __('Has children?') }} —</option>
                                            <option value="yes">{{ __('Has children') }}</option>
                                            <option value="no">{{ __('No (la-walad)') }}</option>
                                        </select>
                                    @endif
                                    @if($p['married'] === 'yes' && $p['issue'] !== 'no' && isset($parentOptions[$p['id']]))
                                        <button type="button" wire:click="addUnder('{{ $p['id'] }}')" class="col-span-2 text-start text-[11px] text-amber-300 hover:text-amber-200 cursor-pointer">
                                            <i class="fa-solid fa-plus"></i> {{ __('Add spouse or children of :name', ['name' => $p['name'] ?: '—']) }}
                                            @if($p['issue'] === 'yes' && ! collect($people)->where('parent', $p['id'])->whereIn('relation', ['son', 'daughter'])->count())
                                                <span class="text-rose-300">({{ __('children not added yet') }})</span>
                                            @endif
                                        </button>
                                    @endif
                                @endif
                                @if(in_array($p['relation'], ['son', 'daughter']) && count($spouses) > 1)
                                    <select wire:model.live="people.{{ $i }}.mother" class="col-span-2 bg-slate-900 border border-slate-700 rounded-lg px-1.5 py-1 text-[11px] text-white outline-none">
                                        <option value="">{{ __('Born of (mother / father)') }}: {{ __('Not specified') }}</option>
                                        @foreach($spouses as $sid => $sname)
                                            <option value="{{ $sid }}">{{ __('Born of (mother / father)') }}: {{ $sname ?: '—' }}</option>
                                        @endforeach
                                    </select>
                                @endif
                                @if(in_array($p['status'], ['alive', 'missing']))
                                    <label class="flex items-center gap-1.5 text-[11px] text-slate-300 cursor-pointer">
                                        <input type="checkbox" wire:model.live="people.{{ $i }}.minor" class="rounded bg-slate-900 border-slate-600 text-amber-500"> {{ __('Minor') }}
                                    </label>
                                    @if($p['minor'])
                                        <input type="text" dir="auto" placeholder="{{ __('Guardian (wali)') }}" wire:model.live.debounce.500ms="people.{{ $i }}.guardian" class="bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-[11px] text-white outline-none">
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Sheet --}}
        <div class="space-y-4 min-w-0">
            <div class="no-print flex flex-wrap items-center justify-between gap-3 p-4 bg-slate-900 rounded-2xl border border-slate-800">
                <span class="text-xs font-bold text-slate-400">{{ __('Shajra nasab and shares:') }}</span>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="window.print()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 border border-slate-700 cursor-pointer">
                        <i class="fa-solid fa-print text-amber-400"></i> {{ __('Print / Save PDF') }}
                    </button>
                    <a href="https://wa.me/?text={{ urlencode(__('Digital shajra nasab maker on Assan Zameen:') . ' ' . url()->current()) }}" target="_blank" rel="noopener"
                       class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold flex items-center gap-1.5">
                        <i class="fa-brands fa-whatsapp text-sm"></i> {{ __('Share on WhatsApp') }}
                    </a>
                </div>
            </div>

            <div id="shajra-sheet" class="bg-slate-900/90 rounded-3xl border border-slate-800 p-5 sm:p-7 space-y-5 shadow-2xl">

                {{-- Shajra --}}
                <div class="no-print flex items-center justify-end">
                    <label class="flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                        <input type="checkbox" wire:model.live="showShares" class="rounded bg-slate-950 border-slate-600 text-amber-500">
                        {{ __('Show shares under names') }}
                    </label>
                </div>
                @php $page = \App\Livewire\Tools\ShajraNasab::PAGES[$report['page'] ?? 'a4p'] ?? \App\Livewire\Tools\ShajraNasab::PAGES['a4p']; @endphp
                <style>@media print { @page { size: {{ $page['css'] }}; margin: 8mm; } }</style>
                <div class="pw-paper" data-print-w="{{ $page['w'] }}" data-print-h="{{ $page['h'] }}">

                {{-- Sarkari form ka saancha (hamesha Urdu): header, unwan, hasb-e-guftah, fiqh, do gawah --}}
                @php
                    $blank = fn ($v, $cls = '') => '<span class="pf-blank ' . $cls . '">' . e($v) . '</span>';
                    $cnicBoxes = function (string $digits) {
                        $d = str_split(str_pad(substr($digits, 0, 13), 13, ' '));
                        $cell = fn ($c) => '<span class="pf-cell">' . e(trim($c)) . '</span>';
                        return implode('', array_map($cell, array_slice($d, 0, 5))) . '<span class="pf-dash">-</span>'
                            . implode('', array_map($cell, array_slice($d, 5, 7))) . '<span class="pf-dash">-</span>' . $cell($d[12]);
                    };
                @endphp
                <div class="pf-form" dir="rtl" lang="ur">
                    <div class="pf-head">
                        <div class="pf-headline">
                            دیہی مرکز مال قانونگوئی {!! $blank($record['markaz']) !!}
                            موضع {!! $blank($record['mauza']) !!}
                            تحصیل {!! $blank($record['tehsil']) !!}
                            ضلع {!! $blank($record['district']) !!}
                        </div>
                        <div class="pf-logo" aria-hidden="true"></div>
                    </div>

                    <div class="pf-body">
                        <div class="pf-main">
                            <h2 class="pf-title">شجرہ نسب برائے وراثت</h2>
                            <div class="pf-field">حسب گفتہ {!! $blank($record['informant'], 'pf-wide') !!}</div>
                            <div class="pf-field">فقہ / مسلک {!! $blank($record['fiqh'] === 'other' ? '' : $fiqhLabels[$record['fiqh']], 'pf-wide') !!}</div>
                            <div class="pf-meta">
                                @if($deceased['name'])<span>متوفی: <b>{{ $deceased['name'] }}</b>@if($deceased['father_name']) {{ ['so' => 'ولد', 'do' => 'دختر', 'wo' => 'زوجہ'][$deceased['father_rel']] }} <b>{{ $deceased['father_name'] }}</b>@endif</span>@endif
                                @if($deceased['caste'])<span>قوم: <b>{{ $deceased['caste'] }}</b></span>@endif
                                @if($deceased['death_date'])<span>تاریخ وفات: <b dir="ltr">{{ \Illuminate\Support\Carbon::parse($deceased['death_date'])->format('d-m-Y') }}</b></span>@endif
                                @if($record['intiqal'])<span>انتقال نمبر: <b>{{ $record['intiqal'] }}</b></span>@endif
                                <span>کل رقبہ: <b dir="ltr">{{ (int) $record['kanal'] }}-{{ (int) $record['marla'] }}-{{ $fmt($record['sarsahi'], 2) }}</b> (ک-م-س)</span>
                            </div>
                        </div>
                        <div class="pf-witnesses">
                            @foreach([1, 2] as $n)
                                <table class="pf-wit">
                                    <tr><th>نام پتی دار/گواہ نمبر {{ $n }}</th><td>{{ $record['w' . $n . '_name'] }}</td></tr>
                                    <tr><th>شناختی کارڈ نمبر</th><td><div class="pf-cnic" dir="ltr">{!! $cnicBoxes($record['w' . $n . '_cnic']) !!}</div></td></tr>
                                    <tr><th>دستخط/انگوٹھا</th><td class="pf-sign"></td></tr>
                                </table>
                            @endforeach
                        </div>
                    </div>
                </div>
                {{-- Landscape: shajra aur report box saath saath --}}
                <div class="pf-row" dir="rtl">
                <div class="pf-tree" dir="ltr">
                <div class="sh-wrap" dir="ltr"
                     x-data
                     x-init="$nextTick(() => { $el.scrollLeft = ($el.scrollWidth - $el.clientWidth) / 2 });
                             const c = $el.querySelector('.sh-canvas');
                             // Print: form + shajra + report ek hi safhe mein (chuna hua size); poora kaghaz chhota kar ke
                             const paper = $el.closest('.pw-paper');
                             window.addEventListener('beforeprint', () => {
                                 const pw = +paper.dataset.printW || 1063, ph = +paper.dataset.printH || 731;
                                 const box = paper.querySelector('.pf-report');
                                 // Kaghaz itna chaura ke shajra aur report box ek line mein aa jayein
                                 paper.style.zoom = ''; const w = Math.max(pw, c.offsetWidth + 30 + (box ? box.offsetWidth + 16 : 0)); paper.style.width = w + 'px';
                                 // Legend aur upar ki chhoti line print mein chhup jati hain; un ki lambai hisaab se nikaal do
                                 const hidden = [...paper.querySelectorAll('.pw-legend, .pf-meta')].reduce((s, e) => s + e.offsetHeight, 0);
                                 paper.style.zoom = Math.min(1, pw / w, ph / (paper.scrollHeight - hidden));
                             });
                             window.addEventListener('afterprint', () => { paper.style.zoom = ''; paper.style.width = ''; });">
                    <div class="sh-canvas" style="width: {{ $layout['width'] }}px; height: {{ $layout['height'] }}px;">
                        <svg width="{{ $layout['width'] }}" height="{{ $layout['height'] }}" aria-hidden="true">
                            @foreach($layout['lines'] as $l)
                                <line x1="{{ $l['x1'] }}" y1="{{ $l['y1'] }}" x2="{{ $l['x2'] }}" y2="{{ $l['y2'] }}" stroke-width="2" stroke-linecap="round" @if($l['s'] === 'dashed') stroke-dasharray="5 4" @endif/>
                            @endforeach
                        </svg>
                        @foreach($layout['cards'] as $card)
                            @include('livewire.tools.partials.shajra-card', ['card' => $card])
                        @endforeach
                    </div>
                </div>

                </div>{{-- /pf-tree --}}

                {{-- Rapor patwari ka box: shajre se khud likha jata hai, aur patwari usi box mein badal sakta hai --}}
                @if($patwariReport)
                    <div class="pf-report" dir="rtl" lang="ur">
                        <input type="text" class="pf-report-title pf-edit no-print" wire:model.live.debounce.500ms="report.title" maxlength="80" aria-label="{{ __('Report title') }}">
                        <h3 class="pf-report-title pf-print-only">{{ $patwariReport['title'] }}</h3>
                        <textarea class="pf-report-text pf-edit no-print" wire:model.live.debounce.700ms="report.text" maxlength="{{ \App\Livewire\Tools\ShajraNasab::MAX_REPORT }}" aria-label="{{ __('Report text') }}"></textarea>
                        <div class="pf-report-text pf-print-only">{!! nl2br(e($patwariReport['text'])) !!}</div>
                        <div class="pf-report-tools no-print">
                            @if($patwariReport['custom'])
                                <span>{{ __('You have edited this report. It no longer updates from the shajra.') }}</span>
                                <button type="button" wire:click="resetReportText" wire:confirm="{{ __('Discard your changes and write the report again from the shajra?') }}">{{ __('Restore automatic report') }}</button>
                            @else
                                <span>{{ __('Written from the shajra. Click in the box to change any word.') }}</span>
                            @endif
                        </div>
                        <div class="pf-report-sign">
                            <div><span class="pf-sign-line"></span>پٹواری حلقہ @if($report['patwari'])<b>{{ $report['patwari'] }}</b>@endif<br>تاریخ: <span dir="ltr">{{ $report['date'] ? \Illuminate\Support\Carbon::parse($report['date'])->format('d-m-Y') : '__________' }}</span></div>
                            <div><span class="pf-sign-line"></span>گرداور / قانونگو</div>
                            <div><span class="pf-sign-line"></span>ریونیو آفیسر</div>
                        </div>
                    </div>
                @endif
                </div>{{-- /pf-row --}}

                {{-- Alamaat --}}
                <div class="pw-legend">
                    <strong>{{ __('Signs:') }}</strong>
                    <span><svg width="28" height="10"><line x1="0" y1="5" x2="28" y2="5" stroke-width="2"/></svg> {{ __('Marriage (nikah)') }}</span>
                    <span><svg width="10" height="16"><line x1="5" y1="0" x2="5" y2="16" stroke-width="2"/></svg> {{ __('Children') }}</span>
                    <span><svg width="30" height="16"><polyline points="3,15 15,1 27,15" fill="none" stroke-width="2"/></svg> {{ __('Daughter or sister') }}</span>
                    <span><svg width="40" height="20"><path d="M 0,10 L 4,10" stroke-width="1.6"/><path class="pw-leaf-body" d="M 4,10 C 4.5,5 10,3 17,3 C 27,3 33,6 39,9 C 33,13 27,17 17,17 C 10,17 4.5,15 4,10 Z" stroke-width="1.5"/></svg> {{ __('Wife, widow or mother') }}</span>
                    <span><svg width="28" height="10"><line x1="0" y1="5" x2="28" y2="5" stroke-width="2" stroke-dasharray="5 4"/></svg> {{ __('Other heirs of a person who died later') }}</span>
                    @foreach($legend as $mark => $meaning)
                        <span><b>{{ $mark }}</b> {{ $meaning }}</span>
                    @endforeach
                </div>

                </div>

                {{-- Holders --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-slate-700 text-slate-400 uppercase font-bold text-[10px]">
                                <th class="py-2 px-2 text-start">#</th>
                                <th class="py-2 px-2 text-start">{{ __('Heir name') }}</th>
                                <th class="py-2 px-2 text-start">{{ __('Relation') }}</th>
                                <th class="py-2 px-2 text-start">{{ __('CNIC') }}</th>
                                <th class="py-2 px-2 text-end">{{ __('Fraction share') }}</th>
                                @if($result['total_parts'])<th class="py-2 px-2 text-end">{{ __('Parts of :total', ['total' => number_format($result['total_parts'])]) }}</th>@endif
                                <th class="py-2 px-2 text-end">%</th>
                                <th class="py-2 px-2 text-end">{{ __('Land (K-M-S)') }}</th>
                                <th class="py-2 px-2 text-start">{{ __('Remarks (kaifiyat)') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/70 text-slate-200">
                            @forelse($result['holders'] as $n => $h)
                                <tr>
                                    <td class="py-2 px-2 text-slate-500">{{ $n + 1 }}</td>
                                    <td class="py-2 px-2 font-bold text-white" dir="auto">{{ $h['name'] ?: '—' }}</td>
                                    <td class="py-2 px-2 text-slate-300 text-[11px]">
                                        @foreach($h['via'] as $chain)<div>{{ $chainLabel($chain) }}</div>@endforeach
                                    </td>
                                    <td class="py-2 px-2 font-mono text-[11px] text-slate-400" dir="ltr">{{ $h['cnic'] }}</td>
                                    <td class="py-2 px-2 text-end font-mono font-bold text-amber-300" dir="ltr">{{ $h['fraction'] }}</td>
                                    @if($result['total_parts'])<td class="py-2 px-2 text-end font-mono" dir="ltr">{{ number_format($h['parts']) }}</td>@endif
                                    <td class="py-2 px-2 text-end font-mono text-slate-300" dir="ltr">{{ $fmt($h['percentage']) }}</td>
                                    <td class="py-2 px-2 text-end font-mono font-bold text-emerald-300" dir="ltr">{{ $h['kanal'] }}-{{ $h['marla'] }}-{{ $fmt($h['sarsahi'], 2) }}</td>
                                    <td class="py-2 px-2 text-[11px] text-slate-300">
                                        @if($h['minor'])<div>{{ __('Minor') }}@if($h['guardian']), {{ __('through guardian :name', ['name' => $h['guardian']]) }}@endif</div>@endif
                                        @if($h['missing'])<div>{{ __('Missing: share kept safe') }}</div>@endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="py-6 text-center text-slate-500">{{ __('No heir selected') }}</td></tr>
                            @endforelse
                        </tbody>
                        @if($result['holders'])
                            <tfoot>
                                <tr class="border-t border-slate-700 font-bold text-white">
                                    <td colspan="4" class="py-2 px-2">{{ __('Total') }}</td>
                                    <td></td>
                                    @if($result['total_parts'])<td class="py-2 px-2 text-end font-mono">{{ number_format(collect($result['holders'])->sum('parts')) }}</td>@endif
                                    <td class="py-2 px-2 text-end font-mono" dir="ltr">{{ $fmt($result['total_pct']) }}</td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>

                @php
                    // Sirf betiyan (koi beta nahi): un ka hissa aur baqi kahan gaya, isi shajre ke hisaab se
                    $rootKids = collect($people)->where('parent', 'root');
                    $rootDaughters = $rootKids->where('relation', 'daughter')->filter(fn ($k) => ($result['nodes'][$k['id']]['share'] ?? 0) > 0);
                    $rootSons = $rootKids->where('relation', 'son')->filter(fn ($k) => ($result['nodes'][$k['id']]['share'] ?? 0) > 0);
                    $daughtersOnly = $rootDaughters->isNotEmpty() && $rootSons->isEmpty();
                    if ($daughtersOnly) {
                        $dShare = $rootDaughters->sum(fn ($k) => $result['nodes'][$k['id']]['share']);
                        [$dn, $dd] = ShajraEngine::fraction($dShare);
                        $residuaries = $rootKids->filter(fn ($k) => str_contains(strtolower($result['nodes'][$k['id']]['basis'] ?? ''), 'residue') && ! in_array($k['relation'], ['daughter'], true));
                        $wentRadd = $rootKids->contains(fn ($k) => str_contains($result['nodes'][$k['id']]['basis'] ?? '', 'radd'));
                    }
                @endphp
                @php
                    // Jin ke bare mein "aulad hai" kaha gaya magar aulad darj nahi
                    $kidsMissing = collect($people)->filter(fn ($p) => $p['issue'] === 'yes'
                        && ! collect($people)->where('parent', $p['id'])->whereIn('relation', ['son', 'daughter'])->count());
                @endphp
                @if($kidsMissing->isNotEmpty())
                    <div class="no-print p-3 bg-amber-500/10 rounded-2xl border border-amber-500/30 text-xs text-amber-100">
                        <i class="fa-solid fa-triangle-exclamation text-amber-400"></i>
                        {{ __('Children are marked but not added for: :names. Add them, or their share may be wrong.', ['names' => $kidsMissing->pluck('name')->implode('، ')]) }}
                    </div>
                @endif

                @if($daughtersOnly)
                    <div class="p-4 bg-sky-500/10 rounded-2xl border border-sky-500/30 text-xs text-sky-50 space-y-2 leading-relaxed">
                        <p class="font-bold text-white text-sm"><i class="fa-solid fa-circle-info text-sky-300"></i> {{ __('The deceased left daughters but no son') }}</p>
                        <p>{{ __('Daughters together get :share of the estate (one daughter 1/2, two or more daughters 2/3, shared equally).', ['share' => $dn . '/' . $dd]) }}</p>
                        @if($residuaries->isNotEmpty())
                            <p>{{ __('The rest goes to the nearest male relative on the father\'s side (asaba). Here it went to:') }}
                                <strong>{{ $residuaries->map(fn ($k) => $k['name'] . ' (' . $relWords[$k['relation']] . ', ' . ($result['nodes'][$k['id']]['fraction'] ?? '') . ')')->implode('، ') }}</strong></p>
                        @elseif($wentRadd)
                            <p>{{ __('No residuary relative is listed, so the rest returns to the daughters (and the mother, if alive) in proportion (radd). The husband or wife does not share in this return.') }}</p>
                        @endif
                        <p class="text-sky-200/80">{{ __('Order for the rest: father, paternal grandfather, full brothers (with full sisters, 2:1), full sister (with the daughter), half brothers and sisters from the same father, full brother\'s sons, full paternal uncles, their sons. Only the nearest one inherits; a nearer one excludes the farther.') }}</p>
                        <p class="text-sky-200/80">{{ __('This follows Hanafi law, which Pakistani courts apply to Sunni Muslims under the Shariat Application Act 1962. Under Fiqh Jafaria (Shia), daughters exclude brothers and uncles and take the rest themselves. Depriving a woman of her inheritance is an offence under section 498A of the Pakistan Penal Code.') }}</p>
                    </div>
                @endif

                @if($result['warnings'])
                    <div class="p-3.5 bg-amber-500/10 rounded-2xl border border-amber-500/30 text-xs text-amber-100 space-y-1.5 leading-relaxed">
                        @foreach($result['warnings'] as $w)
                            <p><i class="fa-solid fa-triangle-exclamation text-amber-400"></i> {{ $warningText($w) }}</p>
                        @endforeach
                        @if($result['unassigned_pct'] > 0)
                            <p>{{ __(':pct% of the estate is not given to anyone yet.', ['pct' => $fmt($result['unassigned_pct'])]) }}</p>
                        @endif
                    </div>
                @endif

                <div class="p-3.5 bg-slate-950 rounded-2xl border border-slate-800 text-[11px] text-slate-400 space-y-1 leading-relaxed">
                    <span class="font-bold text-white flex items-center gap-1.5"><i class="fa-solid fa-book-quran text-amber-400"></i> {{ __('How it was worked out:') }}</span>
                    <p>{{ __('Each person who died is worked out on their own, by the Hanafi method, for spouse, parents, children and full brothers and sisters. When an heir died after the deceased, the share they received is divided among their own heirs.') }}</p>
                    <p>{{ __('Under section 4 of the Muslim Family Laws Ordinance 1961, the children of a son or daughter who died before the deceased take the share that parent would have received, a son getting twice a daughter.') }}</p>
                    <p>{{ __('The share of a missing heir is kept safe for them. A minor\'s share is recorded in their own name and handled through the guardian.') }}</p>
                </div>

                <div class="shajra-signs-row text-[11px] text-center">
                    <div class="border-t border-slate-500 pt-1">{{ __('Patwari halqa') }}</div>
                    <div class="border-t border-slate-500 pt-1">{{ __('Lumbardar / witness') }}</div>
                    <div class="border-t border-slate-500 pt-1">{{ __('Revenue officer') }}</div>
                </div>

                <div class="pt-3 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between text-[11px] text-slate-500 gap-2">
                    <span>{{ __('This is a draft for guidance. The official shajra and mutation are made by the revenue office.') }}</span>
                    <span class="font-mono">Assan Zameen</span>
                </div>
            </div>
        </div>

        @include('livewire.tools.partials.tool-footer', ['toolKey' => 'shajra'])
    </div>
</div>
