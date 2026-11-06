@php
    $fmt = fn ($n, $d = 2) => rtrim(rtrim(number_format((float) $n, $d, '.', ''), '0'), '.') ?: '0';
    $kmf = fn ($s) => $s['kanal'] . '-' . $s['marla'] . '-' . $fmt($s['feet']);
    $neg = $total['sign'] < 0;
    $box = 'w-full rounded-xl border border-indigo-300/30 bg-slate-950/70 px-3 py-2.5 text-center font-mono text-xl font-black ' . ($neg ? 'text-rose-300' : 'text-white');
    $small = 'w-full rounded-lg border border-indigo-300/30 bg-slate-950/70 px-2 py-1.5 text-center font-mono text-base font-bold ' . ($neg ? 'text-rose-300' : 'text-white');
    $urdu = "font-family: 'Jameel Noori Nastaleeq', 'Noto Nastaliq Urdu', serif;";
@endphp
<div class="min-h-screen bg-[#070a13] text-slate-100 py-10 px-4 sm:px-6 lg:px-8 w-full overflow-x-hidden"
     x-data="{
        key: 'az_raqba_v1',
        init() {
            try {
                const saved = localStorage.getItem(this.key);
                if (saved) { $wire.restore(JSON.parse(saved)); }
            } catch (e) {}
            Livewire.hook('commit', ({ component, succeed }) => {
                if (component.id !== $wire.$id) return;
                succeed(() => queueMicrotask(() => {
                    try { localStorage.setItem(this.key, JSON.stringify({ entries: $wire.entries, standard: $wire.standard })); } catch (e) {}
                }));
            });
        }
     }"
>

    <style>
        .rq-grid { display: grid; gap: 1.25rem; align-items: start; }
        @media (min-width: 1024px) { .rq-grid { grid-template-columns: minmax(0, 1fr) 320px; } }
        .rq-label { font-size: 22px; font-weight: 700; line-height: 1.9; }
        .rq-list { max-height: 30rem; overflow-y: auto; }
    </style>

    <div class="max-w-6xl mx-auto space-y-8 w-full">

        <div class="text-center space-y-3 w-full">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-indigo-500/10 border border-indigo-400/30 text-indigo-300 text-xs font-bold uppercase tracking-wider">
                <i class="fa-solid fa-calculator text-sm"></i>
                <span>{{ __('1 marla = :ft sq ft', ['ft' => $fmt($ms)]) }}</span>
            </div>
            <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight pt-4">{{ __('LRMIS Land Calculator') }}</h1>
            <p class="text-sm text-slate-400 max-w-2xl mx-auto pt-3">{{ __('Add or subtract land in kanal, marla and feet, and see the total in kanal-marla-feet, total marlas and acres.') }}</p>
        </div>

        <div class="rq-grid">
            {{-- Baayen: khaane aur nateeja --}}
            <div class="space-y-5">
                {{-- Khaane sirf browser mein: Enter par foran khaali, qeematein server ko; tez likhne par bhi kuch nahi mit-ta --}}
                <form x-data="{
                          row: { kanal: '', marla: '', feet: '' }, empty: false,
                          submitRow() {
                              const r = this.row;
                              if (!(+r.kanal > 0 || +r.marla > 0 || +r.feet > 0)) { this.empty = true; return; }
                              this.empty = false;
                              $wire.addEntry(r.kanal, r.marla, r.feet);
                              this.clear();
                          },
                          clear() { this.row = { kanal: '', marla: '', feet: '' }; this.empty = false; $refs.kanal.focus(); }
                      }"
                      wire:ignore.self
                      @submit.prevent="submitRow()"
                      class="bg-slate-900/90 rounded-3xl border border-slate-800 p-5 space-y-4">
                    <div class="grid grid-cols-3 gap-3" dir="rtl">
                        @foreach(['kanal' => 'کنال', 'marla' => 'مرلہ', 'feet' => 'فٹ'] as $field => $label)
                            <div class="text-center">
                                <label for="rq-{{ $field }}" class="rq-label block text-amber-300" style="{{ $urdu }}">{{ $label }}</label>
                                <input id="rq-{{ $field }}" @if($field === 'kanal') x-ref="kanal" autofocus @endif
                                       type="number" min="0" step="any" inputmode="decimal" x-model="row.{{ $field }}"
                                       class="w-full rounded-xl border border-slate-600 bg-slate-950 px-3 py-3 text-center font-mono text-2xl font-black text-white outline-none focus:border-amber-500">
                            </div>
                        @endforeach
                    </div>
                    <p x-show="empty" x-cloak class="text-xs text-rose-400 text-center">{{ __('Enter kanal, marla or feet first.') }}</p>
                    @error('kanal')<p class="text-xs text-rose-400 text-center">{{ $message }}</p>@enderror

                    <div class="flex flex-wrap items-center gap-2">
                        <button type="submit" class="flex-1 min-w-[9rem] py-3 rounded-xl text-base font-black cursor-pointer {{ $subtract ? 'bg-rose-500 hover:bg-rose-400 text-white' : 'bg-amber-500 hover:bg-amber-400 text-slate-950' }}">
                            <i class="fa-solid {{ $subtract ? 'fa-minus' : 'fa-plus' }}"></i>
                            {{ $subtract ? __('Subtract (Enter)') : __('Add (Enter)') }}
                        </button>
                        <button type="button" @click="clear()" class="px-4 py-3 rounded-xl bg-slate-950 border border-slate-600 text-white font-bold hover:bg-slate-800 cursor-pointer">Reset</button>
                        <button type="button" wire:click="resetAll" wire:confirm="{{ __('Clear all added land?') }}" class="px-4 py-3 rounded-xl bg-slate-950 border border-slate-600 text-white font-bold hover:bg-slate-800 cursor-pointer">Reset All</button>
                        <label class="flex items-center gap-2 px-3 py-3 rounded-xl border cursor-pointer {{ $subtract ? 'border-rose-400 bg-rose-500/10 text-rose-300' : 'border-slate-700 text-rose-300' }}">
                            <input type="checkbox" wire:model.live="subtract" class="rounded bg-slate-950 border-slate-600 text-rose-500">
                            <span class="font-bold">Subtract</span> <span style="{{ $urdu }}">(تفریق)</span>
                        </label>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                        <label class="flex items-center gap-2 text-xs text-slate-400">
                            {{ __('Marla size') }}
                            <select wire:model.live="standard" class="rounded-lg bg-slate-950 border border-slate-700 px-2 py-1 text-xs text-white">
                                <option value="revenue">{{ __('272.25 sq ft (revenue / LRMIS)') }}</option>
                                <option value="society">{{ __('225 sq ft (society)') }}</option>
                            </select>
                        </label>
                        <a href="{{ route('tools.tax-calculator') }}" class="px-4 py-2 rounded-xl bg-white text-slate-900 text-sm font-bold hover:bg-slate-100">
                            <i class="fa-solid fa-receipt"></i> Fee Calculator
                        </a>
                    </div>
                </form>

                {{-- Nateeja --}}
                <div class="rounded-3xl border border-indigo-400/30 bg-indigo-500/10 p-5" dir="rtl">
                    @if($neg)
                        <p class="mb-3 text-center text-sm font-bold text-rose-300" style="{{ $urdu }}">{{ __('The total is negative: more land was subtracted than added.') }}</p>
                    @endif
                    <div class="grid gap-5 md:grid-cols-[1fr_auto]">
                        <div class="space-y-3">
                            @foreach(['kanal' => ['کنال', $total['kanal']], 'marla' => ['مرلہ', $total['marla']], 'feet' => ['فٹ', $fmt($total['feet'])], 'marlas' => ['کل مرلے', $fmt($total['marlas'], 4)]] as $k => [$label, $value])
                                <div class="grid grid-cols-[7rem_1fr] items-center gap-3">
                                    <span class="rq-label text-indigo-100" style="{{ $urdu }}">{{ $label }}</span>
                                    <output class="{{ $box }}" dir="ltr" data-total="{{ $k }}">{{ $neg ? '-' : '' }}{{ $value }}</output>
                                </div>
                            @endforeach
                            <div class="grid grid-cols-[7rem_1fr] items-center gap-3">
                                <span class="text-base font-bold text-indigo-100" style="{{ $urdu }}">کل مربع فٹ</span>
                                <output class="{{ $box }} text-base" dir="ltr">{{ $neg ? '-' : '' }}{{ number_format($total['sqft'], 2) }}</output>
                            </div>
                        </div>
                        <div class="md:w-44 space-y-2 rounded-2xl bg-slate-950/40 p-3">
                            <p class="text-center text-xs font-bold text-indigo-200" style="{{ $urdu }}">ایکڑ کے حساب سے</p>
                            @foreach(['acre' => 'ایکڑ', 'kanal' => 'کنال', 'marla' => 'مرلہ', 'feet' => 'فٹ'] as $k => $label)
                                <div class="grid grid-cols-[1fr_3.5rem] items-center gap-2">
                                    <output class="{{ $small }}" dir="ltr" data-acre="{{ $k }}">{{ $k === 'feet' ? $fmt($acre['feet']) : $acre[$k] }}</output>
                                    <span class="text-sm font-bold text-indigo-100" style="{{ $urdu }}">{{ $label }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <p class="mt-4 text-center font-mono text-lg font-black {{ $neg ? 'text-rose-300' : 'text-amber-300' }}" dir="ltr">
                        {{ $neg ? '-' : '' }}{{ $kmf($total) }} <span class="text-xs text-slate-400" style="{{ $urdu }}">(کنال-مرلہ-فٹ)</span>
                    </p>
                </div>

                
            </div>

            {{-- Daayen: kul jama karda aur fehrist --}}
            <div class="rounded-3xl border border-indigo-400/30 bg-indigo-500/10 p-4 space-y-3" dir="rtl">
                <div class="flex items-center justify-between">
                    <span class="text-lg font-bold" style="{{ $urdu }}">کل جمع کردہ:</span>
                    <span class="min-w-[2.5rem] rounded-lg bg-slate-950 px-3 py-1 text-center font-mono text-lg font-black text-white" data-count>{{ count($entries) }}</span>
                </div>
                @if($subtracted)
                    <p class="text-xs text-slate-300" style="{{ $urdu }}">{{ $added }} جمع، {{ $subtracted }} تفریق</p>
                @endif
                <div class="rq-list space-y-1.5">
                    @forelse($rows as $n => $r)
                        <div wire:key="rq-{{ $r['i'] }}-{{ count($entries) }}" class="flex items-center justify-between gap-2 rounded-xl bg-slate-950/70 px-3 py-2">
                            <span class="text-xs text-slate-500 font-mono">{{ $n + 1 }}</span>
                            <span class="flex-1 font-mono text-sm font-bold {{ $r['sign'] < 0 ? 'text-rose-300' : 'text-emerald-300' }}" dir="ltr">
                                {{ $r['sign'] < 0 ? '−' : '+' }} {{ $kmf($r['norm']) }}
                            </span>
                            <button type="button" wire:click="removeEntry({{ $r['i'] }})" class="text-slate-500 hover:text-rose-300 cursor-pointer" title="{{ __('Remove') }}"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                    @empty
                        <p class="py-8 text-center text-sm text-slate-400" style="{{ $urdu }}">رقبہ لکھ کر Enter دبائیں</p>
                    @endforelse
                </div>
            </div>
        </div>

        @include('livewire.tools.partials.tool-footer', ['toolKey' => 'raqba'])
    </div>
</div>
