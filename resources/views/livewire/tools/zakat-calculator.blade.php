<div class="min-h-screen bg-[#070a13] text-slate-100 py-10 px-4 sm:px-6 lg:px-8 w-full overflow-x-hidden">

    <style>
        .zakat-wrapper {
            display: flex;
            flex-direction: column;
            gap: 2rem;
            width: 100%;
        }
        @media (min-width: 1024px) {
            .zakat-wrapper {
                flex-direction: row;
                align-items: flex-start;
            }
            .zakat-form-col {
                width: 48% !important;
                flex-shrink: 0;
            }
            .zakat-output-col {
                width: 52% !important;
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

            #printable-zakat-sheet {
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

            #printable-zakat-sheet .bg-slate-900,
            #printable-zakat-sheet .bg-slate-950,
            #printable-zakat-sheet .bg-emerald-500\/10,
            #printable-zakat-sheet .bg-amber-500\/10 {
                background-color: #f8fafc !important;
                border: 1px solid #cbd5e1 !important;
                color: #000000 !important;
            }

            #printable-zakat-sheet h1, 
            #printable-zakat-sheet h2, 
            #printable-zakat-sheet h3, 
            #printable-zakat-sheet span, 
            #printable-zakat-sheet p, 
            #printable-zakat-sheet td {
                color: #000000 !important;
            }

            #printable-zakat-sheet .font-mono {
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
                <i class="fa-solid fa-hand-holding-heart text-sm"></i>
                <span>{{ __('Cash, gold, business and plots') }}</span>
            </div>
            <br><br>
            <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight">
                {{ __('Zakat Calculator') }}
            </h1>
            
            <p class="text-sm text-slate-400 max-w-2xl mx-auto leading-relaxed">
                {{ __('Calculate zakat on cash, gold, silver, business stock and plots bought for resale.') }}
            </p>
        </div>

        <!-- Nisab Settings Bar (No-Print) -->
        <div class="bg-slate-900/90 rounded-3xl border border-slate-800 p-4 sm:p-5 shadow-xl flex flex-col sm:flex-row items-center justify-between gap-4 no-print w-full">
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <span class="text-xs font-bold text-slate-400">{{ __('Nisab standard:') }}</span>
                <div class="flex items-center bg-slate-950 p-1 rounded-xl border border-slate-800">
                    <button type="button" wire:click="$set('nisab_standard', 'silver')" 
                            class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer {{ $nisab_standard === 'silver' ? 'bg-amber-500 text-slate-950 font-black' : 'text-slate-400 hover:text-white' }}">
                        {{ __('Silver (52.5 tola, the cautious choice)') }}
                    </button>
                    <button type="button" wire:click="$set('nisab_standard', 'gold')" 
                            class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer {{ $nisab_standard === 'gold' ? 'bg-amber-500 text-slate-950 font-black' : 'text-slate-400 hover:text-white' }}">
                        {{ __('Gold (7.5 tola)') }}
                    </button>
                </div>
            </div>

            <div class="flex items-center gap-3 text-xs font-mono text-slate-400">
                <span>{{ __('Gold per tola:') }} <strong class="text-amber-400">PKR {{ number_format($gold_rate_per_tola) }}</strong></span>
                <span class="text-slate-600">|</span>
                <span>{{ __('Silver per tola:') }} <strong class="text-slate-200">PKR {{ number_format($silver_rate_per_tola) }}</strong></span>
            </div>
        </div>

        <div class="zakat-wrapper w-full">

            <!-- LEFT COLUMN: INPUT WIZARD (No-Print) -->
            <div class="zakat-form-col w-full bg-slate-900/90 rounded-3xl border border-slate-800 p-6 shadow-2xl space-y-5 no-print">
                <h3 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2 border-b border-slate-800 pb-3">
                    <i class="fa-solid fa-coins text-amber-400"></i>
                    <span>{{ __('Enter your assets') }}</span>
                </h3>

                <!-- 1. Gold & Silver -->
                <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-3">
                    <span class="text-xs font-bold text-amber-400 uppercase tracking-wider block flex items-center justify-between">
                        <span>{{ __('1. Gold and silver') }}</span>
                        <span class="text-[10px] text-slate-500 lowercase font-normal">{{ __('Jewellery / coins') }}</span>
                    </span>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">{{ __('Gold (tola)') }}</label>
                            <input type="number" step="any" wire:model.live.debounce.300ms="gold_tola" placeholder="e.g. 5"
                                   class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold focus:border-amber-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">{{ __('Silver (tola)') }}</label>
                            <input type="number" step="any" wire:model.live.debounce.300ms="silver_tola" placeholder="e.g. 10"
                                   class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold focus:border-amber-500 outline-none">
                        </div>
                    </div>
                </div>

                <!-- 2. Cash & Liquid Savings -->
                <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-3">
                    <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider block">{{ __('2. Cash, bank and prize bonds') }}</span>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">{{ __('Cash in hand (PKR)') }}</label>
                            <input type="number" step="1000" wire:model.live.debounce.300ms="cash_in_hand" 
                                   class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold focus:border-amber-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">{{ __('Bank balances (PKR)') }}</label>
                            <input type="number" step="1000" wire:model.live.debounce.300ms="bank_savings" 
                                   class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold focus:border-amber-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">{{ __('Foreign currency (PKR value)') }}</label>
                            <input type="number" step="1000" wire:model.live.debounce.300ms="foreign_currency_pkr" 
                                   class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold focus:border-amber-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">{{ __('Prize bonds / committee received') }}</label>
                            <input type="number" step="1000" wire:model.live.debounce.300ms="prize_bonds" 
                                   class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold focus:border-amber-500 outline-none">
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-3">
                    <span class="text-xs font-bold text-cyan-400 uppercase tracking-wider block flex items-center justify-between">
                        <span>{{ __('3. Property for resale and business') }}</span>
                        <span class="text-[10px] text-amber-400 font-bold">{{ __('Important') }}</span>
                    </span>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">
                            {{ __('Market value of plots, files or houses bought for resale (PKR)') }}
                        </label>
                        <input type="number" step="10000" wire:model.live.debounce.300ms="trading_plots_files_value" placeholder="0"
                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-amber-400 font-mono font-bold focus:border-amber-500 outline-none">
                        <span class="text-[10px] text-slate-500 block mt-0.5">{{ __('Your own home and farmland are not counted, only plots bought with the intention to resell.') }}</span>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-1">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">{{ __('Saved rental income (PKR)') }}</label>
                            <input type="number" step="1000" wire:model.live.debounce.300ms="rental_income_retained" 
                                   class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold focus:border-amber-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">{{ __('Business stock') }}</label>
                            <input type="number" step="1000" wire:model.live.debounce.300ms="business_inventory" 
                                   class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold focus:border-amber-500 outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">{{ __('Loans given that you expect back') }}</label>
                        <input type="number" step="1000" wire:model.live.debounce.300ms="good_loans_receivable" 
                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold focus:border-amber-500 outline-none">
                    </div>
                </div>

                <!-- 4. Deductible Liabilities -->
                <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-3">
                    <span class="text-xs font-bold text-rose-400 uppercase tracking-wider block">{{ __('4. Debts to deduct') }}</span>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">{{ __('Debts due now (PKR)') }}</label>
                            <input type="number" step="1000" wire:model.live.debounce.300ms="immediate_debt_payable" 
                                   class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-rose-400 font-mono font-bold focus:border-rose-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">{{ __('Unpaid bills and salaries') }}</label>
                            <input type="number" step="1000" wire:model.live.debounce.300ms="unpaid_bills_salaries" 
                                   class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-rose-400 font-mono font-bold focus:border-rose-500 outline-none">
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: SHARIAT ZAKAT SUMMARY & STATEMENT -->
            <div class="zakat-output-col w-full space-y-5">
                
                <!-- Action Controls (No-Print) -->
                <div id="action-bar" class="no-print flex items-center justify-between gap-3 p-4 bg-slate-900 rounded-2xl border border-slate-800 w-full">
                    <span class="text-xs font-bold text-slate-400">{{ __('Zakat statement:') }}</span>
                    <div class="flex items-center gap-2">
                        <button onclick="window.print()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 transition border border-slate-700 cursor-pointer">
                            <i class="fa-solid fa-print text-amber-400"></i> {{ __('Print / PDF') }}
                        </button>

                        <a href="https://wa.me/?text={{ urlencode(__('Zakat calculator on Assan Zameen:') . ' ' . url()->current()) }}" 
                           target="_blank" 
                           class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 transition cursor-pointer">
                            <i class="fa-brands fa-whatsapp text-sm"></i> WhatsApp
                        </a>
                    </div>
                </div>

                <!-- Main Printable Sheet -->
                <div id="printable-zakat-sheet" class="bg-slate-900/90 rounded-3xl border border-slate-800 p-6 sm:p-8 shadow-2xl space-y-6 w-full">

                    <!-- Sheet Header -->
                    <div class="border-b border-slate-800 pb-4 flex items-center justify-between w-full">
                        <div>
                            <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest block">{{ __('For guidance only') }}</span>
                            <h2 class="text-xl font-black text-white">{{ __('Annual zakat statement') }}</h2>
                            <p class="text-xs text-slate-400 mt-0.5">
                                {{ __('Nisab:') }} <strong class="text-white">{{ $nisab_standard === 'silver' ? __('Silver (52.5 tola)') : __('Gold (7.5 tola)') }}</strong> &bull; {{ __('Rate: 2.5%') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold font-mono {{ $is_nisab_reached ? 'text-emerald-400 bg-emerald-500/10 border border-emerald-500/30' : 'text-slate-400 bg-slate-800' }} px-3 py-1 rounded-xl">
                                {{ $is_nisab_reached ? __('Zakat is due') : __('Zakat is not due') }}
                            </span>
                        </div>
                    </div>

                    <!-- Highlight Payable Zakat Box -->
                    <div class="p-6 bg-gradient-to-r from-emerald-500/15 via-slate-950 to-slate-950 rounded-2xl border border-emerald-500/30 flex items-center justify-between w-full">
                        <div>
                            <span class="text-xs font-bold text-slate-400 block uppercase tracking-wider">{{ __('Zakat due (2.5%):') }}</span>
                            <span class="text-3xl sm:text-4xl font-black text-emerald-400 font-mono mt-1 block">
                                PKR {{ number_format($zakat_due) }}
                            </span>
                            <span class="text-[11px] text-slate-400 mt-1 block">
                                {{ $is_nisab_reached ? __('Your net wealth is above the nisab of PKR :nisab.', ['nisab' => number_format($nisab_threshold_value)]) : __('Your net wealth is below the nisab of PKR :nisab.', ['nisab' => number_format($nisab_threshold_value)]) }}
                            </span>
                        </div>
                        <i class="fa-solid fa-hand-holding-dollar text-4xl text-emerald-400/40"></i>
                    </div>

                    <!-- Assets & Liabilities Breakdown Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 w-full">
                        <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800">
                            <span class="text-[10px] text-slate-400 uppercase font-bold block">{{ __('Zakatable assets') }}</span>
                            <span class="text-base font-black text-white font-mono mt-0.5 block">PKR {{ number_format($total_zakatable_assets) }}</span>
                        </div>

                        <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800">
                            <span class="text-[10px] text-rose-400 uppercase font-bold block">{{ __('Debts deducted') }}</span>
                            <span class="text-base font-black text-rose-400 font-mono mt-0.5 block">- PKR {{ number_format($total_deductible_liabilities) }}</span>
                        </div>

                        <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800">
                            <span class="text-[10px] text-amber-400 uppercase font-bold block">{{ __('Net zakatable wealth') }}</span>
                            <span class="text-base font-black text-amber-400 font-mono mt-0.5 block">PKR {{ number_format($net_wealth) }}</span>
                        </div>
                    </div>

                    <!-- Important Shariat Property Rules -->
                    <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-2 text-xs text-slate-400">
                        <span class="font-bold text-white flex items-center gap-1.5">
                            <i class="fa-solid fa-scale-balanced text-amber-400"></i> {{ __('Zakat on property: the basics') }}
                        </span>
                        <ul class="list-disc list-inside space-y-1.5 text-[11px] leading-relaxed pr-1">
                            <li><strong>{{ __('Your home and car:') }}</strong> {{ __('No zakat on the house you live in, your shop building or a car for personal use.') }}</li>
                            <li><strong>{{ __('Plots or files for resale:') }}</strong> {{ __('A plot or file bought to sell at a profit is counted at its current market value, with 2.5% due each year.') }}</li>
                            <li><strong>{{ __('Rented property:') }}</strong> {{ __('The property value itself is not counted, only the rent you still have saved when your zakat year completes.') }}</li>
                            <li><strong>{{ __('Farm produce:') }}</strong> {{ __('Instead of zakat on the land, at harvest there is') }} <strong>{{ __('ushr (10% if rain-fed, 5% if irrigated)') }}</strong> {{ __('due.') }}</li>
                        </ul>
                    </div>

                    <!-- Certified Footer Notes -->
                    <div class="pt-4 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between text-[11px] text-slate-500 gap-2">
                        <span>{{ __('This follows common Hanafi rules. Ask a scholar about your own case.') }}</span>
                        <span class="font-mono">Assan Zameen</span>
                    </div>

                </div>

            </div>

        </div>

    </div>
    @include('livewire.tools.partials.tool-footer', ['toolKey' => 'zakat'])
</div>