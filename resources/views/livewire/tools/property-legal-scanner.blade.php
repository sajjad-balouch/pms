@php
    $levels = [
        'high' => ['High risk: do not pay yet', 'Clear the problems below before giving any money.', 'border-rose-500/40 bg-rose-500/10 text-rose-300', 'fa-triangle-exclamation'],
        'incomplete' => ['Checks still open', 'Answer the remaining checks. Treat Not sure as not done.', 'border-amber-500/40 bg-amber-500/10 text-amber-300', 'fa-list-check'],
        'medium' => ['Some risk remains', 'The main checks are done, but clear the points below before paying.', 'border-amber-500/40 bg-amber-500/10 text-amber-300', 'fa-circle-exclamation'],
        'clear' => ['All checks done', 'Next, have a lawyer review the papers before you pay. This list does not prove ownership.', 'border-emerald-500/40 bg-emerald-500/10 text-emerald-300', 'fa-circle-check'],
    ];
    [$levelTitle, $levelText, $levelClass, $levelIcon] = $levels[$result['level']];
@endphp

<div class="bg-[#070a13] text-slate-100 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto space-y-8">

        <div class="text-center space-y-3">
            <h1 class="text-3xl sm:text-4xl font-black text-white">{{ __('Property Document Checker') }}</h1>
            <p class="text-sm text-slate-400 max-w-2xl mx-auto">{{ __('Check a fard, registry, allotment letter or intiqal step by step and see the risks to clear before you pay.') }}</p>
        </div>

        <div class="grid lg:grid-cols-5 gap-6 items-start">

            <div class="lg:col-span-3 space-y-5">
                <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-5 space-y-2">
                    <label for="doc-type" class="block text-sm font-bold text-white">{{ __('Which document do you have?') }}</label>
                    <select id="doc-type" wire:model.live="document_type" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2.5 text-sm text-white focus:border-amber-500 outline-none">
                        @foreach($types as $value => $label)
                            <option value="{{ $value }}">{{ __($label) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="bg-slate-900/90 rounded-2xl border border-slate-800 divide-y divide-slate-800">
                    @foreach($checks as $key => [$label, $critical])
                        <fieldset class="p-4 space-y-2.5" wire:key="check-{{ $key }}">
                            <legend class="text-sm text-slate-200 leading-relaxed">
                                {{ __($label) }}
                                @if($critical)
                                    <span class="ms-1 text-[11px] font-bold text-rose-300">{{ __('Important') }}</span>
                                @endif
                            </legend>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach(['yes' => ['Yes', 'peer-checked:bg-emerald-500'], 'no' => ['No', 'peer-checked:bg-rose-500'], 'unknown' => ['Not sure', 'peer-checked:bg-amber-500']] as $value => [$text, $tone])
                                    <label class="cursor-pointer">
                                        <input type="radio" class="peer sr-only" name="answer-{{ $key }}" value="{{ $value }}" wire:model.live="answers.{{ $key }}">
                                        <span class="block text-center py-2 rounded-lg text-xs font-bold bg-slate-950 border border-slate-700 text-slate-300 peer-checked:text-slate-950 peer-checked:border-transparent peer-focus-visible:ring-2 peer-focus-visible:ring-amber-400 {{ $tone }}">{{ __($text) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>
                    @endforeach
                </div>

                <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-5 space-y-3">
                    <h2 class="text-sm font-bold text-white">{{ __('Optional: read a PDF') }}</h2>
                    <p class="text-xs text-slate-400 leading-relaxed">{{ __('We read the text of a PDF to find khewat, khasra and CNIC numbers, then discard the file. Scanned photos cannot be read.') }}</p>
                    <input type="file" accept="application/pdf" wire:model="document" class="block w-full text-xs text-slate-300 file:me-3 file:rounded-lg file:border-0 file:bg-slate-800 file:px-3 file:py-2 file:text-white">
                    @error('document') <p class="text-xs text-rose-300">{{ $message }}</p> @enderror
                    <input type="text" wire:model="seller_cnic" inputmode="numeric" maxlength="15" placeholder="{{ __('Seller CNIC (optional)') }}" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-amber-500 outline-none">
                    <button type="button" wire:click="readPdf" wire:loading.attr="disabled" wire:target="readPdf,document" class="px-4 py-2 bg-amber-500 hover:bg-amber-400 disabled:opacity-50 text-slate-950 rounded-xl text-xs font-bold cursor-pointer">
                        <span wire:loading.remove wire:target="readPdf">{{ __('Read PDF') }}</span>
                        <span wire:loading wire:target="readPdf">{{ __('Reading...') }}</span>
                    </button>

                    @if($pdfReport)
                        <div class="rounded-xl bg-slate-950 border border-slate-800 p-3 text-xs text-slate-300 space-y-1.5">
                            @if(! $pdfReport['has_text'])
                                <p class="text-amber-300">{{ __('No readable text found. This file is probably a scan, so please check it by hand.') }}</p>
                            @else
                                <p>{{ __('Khewat found') }}: <span class="font-mono text-white">{{ $pdfReport['khewat'] ?? __('Not found') }}</span></p>
                                <p>{{ __('Khasra found') }}: <span class="font-mono text-white">{{ $pdfReport['khasra'] ?? __('Not found') }}</span></p>
                                <p>{{ __('CNIC numbers found') }}: <span class="font-mono text-white">{{ $pdfReport['cnic_count'] }}</span></p>
                                @if($pdfReport['cnic_match'] === true)
                                    <p class="text-emerald-300">{{ __('The seller CNIC appears in this file.') }}</p>
                                @elseif($pdfReport['cnic_match'] === false)
                                    <p class="text-rose-300">{{ __('The seller CNIC was not found in this file. Check it carefully.') }}</p>
                                @endif
                                <p class="text-slate-500">{{ __('Compare these with the original paper. Urdu PDFs are not always read correctly.') }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <div class="lg:col-span-2 lg:sticky lg:top-24 space-y-4">
                <div class="rounded-2xl border p-5 space-y-2 {{ $levelClass }}">
                    <p class="font-black text-lg flex items-center gap-2"><i class="fa-solid {{ $levelIcon }}" aria-hidden="true"></i> {{ __($levelTitle) }}</p>
                    <p class="text-sm text-slate-200 leading-relaxed">{{ __($levelText) }}</p>
                    <p class="text-xs text-slate-400">{{ __(':done of :total checks cleared', ['done' => $result['cleared'], 'total' => $result['total']]) }}</p>
                </div>

                @foreach([['blockers', 'Must fix before paying', 'text-rose-300'], ['warnings', 'Should be cleared', 'text-amber-300'], ['open', 'Not checked yet', 'text-slate-300']] as [$group, $heading, $tone])
                    @if(count($result[$group]))
                        <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-4">
                            <h2 class="text-xs font-bold {{ $tone }} mb-2">{{ __($heading) }}</h2>
                            <ul class="space-y-1.5 text-xs text-slate-300 leading-relaxed list-disc ps-4">
                                @foreach($result[$group] as $item)
                                    <li>{{ __($item) }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                @endforeach

                <button type="button" wire:click="resetChecklist" class="w-full py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-xs font-bold text-white cursor-pointer">{{ __('Start again') }}</button>
            </div>
        </div>

        @include('livewire.tools.partials.tool-footer', ['toolKey' => 'scanner'])
    </div>
</div>
