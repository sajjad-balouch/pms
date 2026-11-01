<div class="min-h-screen bg-[#070a13] text-slate-100 py-10 px-4 sm:px-6 lg:px-8 w-full overflow-x-hidden">

    <style>
        .inherit-wrapper {
            display: flex;
            flex-direction: column;
            gap: 2rem;
            width: 100%;
        }
        @media (min-width: 1024px) {
            .inherit-wrapper {
                flex-direction: row;
                align-items: flex-start;
            }
            .inherit-form-col {
                width: 44% !important;
                flex-shrink: 0;
            }
            .inherit-output-col {
                width: 56% !important;
                flex-grow: 1;
            }
        }

        @media print {
            header, footer, nav, .glass-nav, button, 
            .no-print, [wire\:loading], #action-bar {
                display: none !important;
            }

            body, html {
                background-color: #ffffff !important;
                color: #000000 !important;
                padding: 0 !important;
                margin: 0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            #printable-inheritance-slip {
                display: block !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 20px !important;
                background: #ffffff !important;
                color: #000000 !important;
                border: 2px solid #0f172a !important;
                border-radius: 16px !important;
                box-shadow: none !important;
            }

            #printable-inheritance-slip .bg-slate-900,
            #printable-inheritance-slip .bg-slate-950,
            #printable-inheritance-slip .bg-emerald-500\/10,
            #printable-inheritance-slip .bg-amber-500\/10 {
                background-color: #f8fafc !important;
                border: 1px solid #cbd5e1 !important;
                color: #000000 !important;
            }

            #printable-inheritance-slip h1, 
            #printable-inheritance-slip h2, 
            #printable-inheritance-slip h3, 
            #printable-inheritance-slip span, 
            #printable-inheritance-slip p, 
            #printable-inheritance-slip td {
                color: #000000 !important;
            }

            #printable-inheritance-slip .font-mono {
                color: #000000 !important;
                font-weight: 800 !important;
            }

            @page {
                size: A4 portrait;
                margin: 10mm;
            }
        }
    </style>

    <div class="max-w-7xl mx-auto space-y-8 w-full">

        <!-- Tool Header Banner (No-Print) -->
        <div class="text-center space-y-3 no-print w-full">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-bold uppercase tracking-wider">
                <i class="fa-solid fa-scale-unbalanced-flip text-sm"></i>
                <span>{{ __('Hanafi method') }}</span>
            </div>
            <br><br>
            <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight">
                {{ __('Islamic Inheritance Calculator') }}
            </h1>
            
            <p class="text-sm text-slate-400 max-w-2xl mx-auto" style="margin-top: 40px;">
                {{ __('Work out Islamic inheritance shares of land and cash for spouse, parents, children and full siblings.') }}
            </p>
        </div>

        <div class="inherit-wrapper w-full">

            <!-- LEFT COLUMN: INPUT WIZARD (No-Print) -->
            <div class="inherit-form-col w-full bg-slate-900/90 rounded-3xl border border-slate-800 p-6 shadow-2xl space-y-5 no-print">
                <h3 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2 border-b border-slate-800 pb-3">
                    <i class="fa-solid fa-user-group text-amber-400"></i>
                    <span>{{ __('Estate and heirs') }}</span>
                </h3>

                <!-- Deceased Gender -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-400 uppercase">{{ __('Gender of the deceased') }}</label>
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" wire:click="$set('deceased_gender', 'male')" 
                                class="py-2.5 px-4 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 cursor-pointer {{ $deceased_gender === 'male' ? 'bg-amber-500 text-slate-950 font-black shadow-lg shadow-amber-500/20' : 'bg-slate-950 text-slate-400 border border-slate-800' }}">
                            <i class="fa-solid fa-mars text-sm"></i> {{ __('Male') }}
                        </button>
                        <button type="button" wire:click="$set('deceased_gender', 'female')" 
                                class="py-2.5 px-4 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 cursor-pointer {{ $deceased_gender === 'female' ? 'bg-amber-500 text-slate-950 font-black shadow-lg shadow-amber-500/20' : 'bg-slate-950 text-slate-400 border border-slate-800' }}">
                            <i class="fa-solid fa-venus text-sm"></i> {{ __('Female') }}
                        </button>
                    </div>
                </div>

                <!-- Estate: Land Details -->
                <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-3">
                    <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider block">{{ __('Land to distribute') }}</span>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">{{ __('Kanal') }}</label>
                            <input type="number" step="any" wire:model.live.debounce.300ms="total_kanals" 
                                   class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold focus:border-amber-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">{{ __('Marla') }}</label>
                            <input type="number" step="any" wire:model.live.debounce.300ms="total_marlas" 
                                   class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold focus:border-amber-500 outline-none">
                        </div>
                    </div>
                    <div class="text-[11px] text-slate-400 font-mono flex justify-between bg-slate-900/60 p-2 rounded-xl">
                        <span>{{ __('Total land:') }}</span>
                        <span class="text-amber-400 font-bold">{{ $net_marlas }} {{ __('Marla') }}</span>
                    </div>
                </div>

                <!-- Estate: Cash, Debts & Funeral -->
                <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-3">
                    <span class="text-xs font-bold text-sky-400 uppercase tracking-wider block">{{ __('Cash and deductions') }}</span>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">{{ __('Total cash and bank balance (PKR)') }}</label>
                            <input type="number" step="1000" wire:model.live.debounce.300ms="cash_estate" 
                                   class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold focus:border-amber-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">{{ __('Debts and funeral costs (PKR)') }}</label>
                            <input type="number" step="1000" wire:model.live.debounce.300ms="debts_funeral" 
                                   class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-rose-400 font-mono font-bold focus:border-rose-500 outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">{{ __('Will for non-heirs, up to one third (PKR)') }}</label>
                        <input type="number" min="0" step="1000" wire:model.live.debounce.300ms="will_amount" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold focus:border-amber-500 outline-none">
                    </div>
                    <div class="text-[11px] text-slate-400 font-mono flex justify-between bg-slate-900/60 p-2 rounded-xl">
                        <span>{{ __('Net cash to distribute:') }}</span>
                        <span class="text-emerald-400 font-bold">PKR {{ number_format($net_cash) }}</span>
                    </div>
                </div>

                <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-3">
                    <span class="text-xs font-bold text-amber-400 uppercase tracking-wider block">{{ __('Living heirs') }}</span>

                    <!-- Spouse -->
                    @if($deceased_gender === 'male')
                        <div class="flex items-center justify-between p-2.5 bg-slate-900 rounded-xl border border-slate-800">
                            <span class="text-xs font-bold text-slate-300">{{ __('Wives:') }}</span>
                            <div class="flex items-center gap-2">
                                <input type="number" min="0" max="4" wire:model.live="wives_count" 
                                       class="w-16 bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-xs text-center text-amber-400 font-mono font-bold">
                            </div>
                        </div>
                    @else
                        <div class="flex items-center justify-between p-2.5 bg-slate-900 rounded-xl border border-slate-800">
                            <span class="text-xs font-bold text-slate-300">{{ __('Is the husband alive?') }}</span>
                            <button type="button" wire:click="$toggle('has_husband')" 
                                    class="px-3 py-1 rounded-lg text-xs font-black uppercase {{ $has_husband ? 'bg-emerald-500 text-slate-950' : 'bg-slate-800 text-slate-400' }}">
                                {{ $has_husband ? __('Yes') : __('No') }}
                            </button>
                        </div>
                    @endif

                    <!-- Parents -->
                    <div class="grid grid-cols-2 gap-2">
                        <div class="flex items-center justify-between p-2.5 bg-slate-900 rounded-xl border border-slate-800">
                            <span class="text-xs font-bold text-slate-300">{{ __('Father:') }}</span>
                            <button type="button" wire:click="$toggle('has_father')" 
                                    class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $has_father ? 'bg-emerald-500 text-slate-950' : 'bg-slate-800 text-slate-400' }}">
                                {{ $has_father ? __('Alive') : __('Deceased') }}
                            </button>
                        </div>

                        <div class="flex items-center justify-between p-2.5 bg-slate-900 rounded-xl border border-slate-800">
                            <span class="text-xs font-bold text-slate-300">{{ __('Mother:') }}</span>
                            <button type="button" wire:click="$toggle('has_mother')" 
                                    class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $has_mother ? 'bg-emerald-500 text-slate-950' : 'bg-slate-800 text-slate-400' }}">
                                {{ $has_mother ? __('Alive') : __('Deceased') }}
                            </button>
                        </div>
                    </div>

                    <!-- Children -->
                    <div class="grid grid-cols-2 gap-3 pt-1">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">{{ __('Sons') }}</label>
                            <input type="number" min="0" wire:model.live="sons_count" 
                                   class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold text-center">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">{{ __('Daughters') }}</label>
                            <input type="number" min="0" wire:model.live="daughters_count" 
                                   class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold text-center">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">{{ __('Full brothers') }}</label>
                            <input type="number" min="0" max="50" wire:model.live="brothers_count" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold text-center">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">{{ __('Full sisters') }}</label>
                            <input type="number" min="0" max="50" wire:model.live="sisters_count" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold text-center">
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: OFFICIAL DISTRIBUTION REPORT -->
            <div class="inherit-output-col w-full space-y-5">
                
                <!-- Quick Print / Share Actions -->
                <div id="action-bar" class="no-print flex items-center justify-between gap-3 p-4 bg-slate-900 rounded-2xl border border-slate-800 w-full">
                    <span class="text-xs font-bold text-slate-400">{{ __('Inheritance statement:') }}</span>
                    <div class="flex items-center gap-2">
                        <button onclick="window.print()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 transition border border-slate-700 cursor-pointer">
                            <i class="fa-solid fa-print text-amber-400"></i> {{ __('Print / Save PDF') }}
                        </button>

                        <a href="https://wa.me/?text={{ urlencode(__('Inheritance calculator on Assan Zameen:') . ' ' . url()->current()) }}" 
                           target="_blank" 
                           class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 transition cursor-pointer">
                            <i class="fa-brands fa-whatsapp text-sm"></i> {{ __('Share on WhatsApp') }}
                        </a>
                    </div>
                </div>

                <!-- Main Printable Slip -->
                <div id="printable-inheritance-slip" class="bg-slate-900/90 rounded-3xl border border-slate-800 p-6 sm:p-8 shadow-2xl space-y-6 w-full">

                    <!-- Sheet Header -->
                    <div class="border-b border-slate-800 pb-4 flex items-center justify-between w-full">
                        <div>
                            <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest block">{{ __('For guidance only') }}</span>
                            <h2 class="text-xl font-black text-white">{{ __('Inheritance share statement') }}</h2>
                            <p class="text-xs text-slate-400 mt-0.5">
                                {{ __('Deceased') }}: <strong class="text-white">{{ $deceased_gender === 'male' ? __('Male') : __('Female') }}</strong> &bull; {{ __('Land') }}: {{ $net_marlas }} {{ __('Marla') }} &bull; {{ __('Net cash') }}: PKR {{ number_format($net_cash) }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold text-amber-400 bg-amber-500/10 border border-amber-500/20 px-2.5 py-1 rounded-lg">{{ __('Shares') }}</span>
                        </div>
                    </div>

                    <!-- Itemized Heirs Table -->
                    <div class="overflow-x-auto w-full">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="border-b border-slate-800 text-slate-400 uppercase font-bold text-[10px]">
                                    <th class="py-2.5 px-3">{{ __('Heir') }}</th>
                                    <th class="py-2.5 px-3">{{ __('Share') }}</th>
                                    <th class="py-2.5 px-3">{{ __('Each (%)') }}</th>
                                    <th class="py-2.5 px-3 text-right">{{ __('Land each (kanal - marla)') }}</th>
                                    <th class="py-2.5 px-3 text-right">{{ __('Cash each (PKR)') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/60 text-slate-200 font-medium">
                                @forelse($heirs_shares as $share)
                                    <tr class="hover:bg-slate-800/30">
                                        <td class="py-3 px-3 font-bold text-white flex items-center gap-1.5">
                                            <i class="fa-solid fa-user text-[11px] text-amber-400"></i>
                                            <span>{{ __(ucfirst($share['key'])) }}@if($share['count'] > 1) × {{ $share['count'] }}@endif</span>
                                        </td>
                                        <td class="py-3 px-3 font-mono font-bold text-emerald-400">{{ __($share['basis']) }}</td>
                                        <td class="py-3 px-3 font-mono text-slate-300">{{ $share['percentage'] }}%</td>
                                        <td class="py-3 px-3 text-right font-mono font-bold text-amber-400">{{ $share['kanals'] }} {{ __('Kanal') }} - {{ $share['marlas'] }} {{ __('Marla') }}</td>
                                        <td class="py-3 px-3 text-right font-mono font-bold text-white">{{ number_format($share['cash_amount']) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-6 text-center text-slate-500">{{ __('No heir selected') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($has_awl || $has_radd || $unassigned_pct > 0 || $debt_shortfall > 0 || $will_capped)
                        <div class="p-3.5 bg-amber-500/10 rounded-2xl border border-amber-500/30 text-xs text-amber-100 space-y-1.5 leading-relaxed">
                            @if($has_awl)<p>{{ __('The fixed shares add up to more than the whole estate, so each share was reduced in proportion (awl).') }}</p>@endif
                            @if($has_radd)<p>{{ __('Some estate was left over, so it was returned to the heirs in proportion, except the spouse (radd).') }}</p>@endif
                            @if($unassigned_pct > 0)<p>{{ __(':pct% of the estate has no heir among the relatives listed here. Other relatives may inherit it, so please ask a scholar.', ['pct' => $unassigned_pct]) }}</p>@endif
                            @if($debt_shortfall > 0)<p>{{ __('Debts are more than the cash by PKR :amount. This must be paid from the land or other assets before dividing them.', ['amount' => number_format($debt_shortfall)]) }}</p>@endif
                            @if($will_capped)<p>{{ __('The will was limited to one third of the estate after debts.') }}</p>@endif
                        </div>
                    @endif

                    <!-- Summary Rule Notice -->
                    <div class="p-3.5 bg-slate-950 rounded-2xl border border-slate-800 text-xs text-slate-400 space-y-1">
                        <span class="font-bold text-white flex items-center gap-1.5">
                            <i class="fa-solid fa-book-quran text-amber-400"></i> {{ __('How it was worked out:') }}
                        </span>
                        <p class="text-[11px] leading-relaxed">
                            {{ __('Debts and funeral costs are paid first, then a will of up to one third. Fixed shares are then given, and the remainder goes to the residuary heirs, with a son or brother receiving twice the share of a daughter or sister.') }}
                        </p>
                    </div>

                    <!-- Certified Footer -->
                    <div class="pt-4 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between text-[11px] text-slate-500 gap-2">
                        <span>{{ __('This is a guide only. It is not an official mutation or a court document.') }}</span>
                        <span class="font-mono">Assan Zameen</span>
                    </div>

                </div>

            </div>

        </div>

    </div>
    @include('livewire.tools.partials.tool-footer', ['toolKey' => 'inheritance'])
</div>