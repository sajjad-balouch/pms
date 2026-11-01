<div class="min-h-screen bg-[#070a13] text-slate-100 py-10 px-4 sm:px-6 lg:px-8 w-full overflow-x-hidden">

    
    <style>
        /* Force full width on flex containers */
        .calculator-wrapper {
            display: flex;
            flex-direction: column;
            gap: 2rem;
            width: 100%;
        }
        @media (min-width: 1024px) {
            .calculator-wrapper {
                flex-direction: row;
                align-items: flex-start;
            }
            .calculator-form {
                width: 48% !important;
                flex-shrink: 0;
            }
            .calculator-output {
                width: 52% !important;
                flex-grow: 1;
            }
        }

        /* Print Media Query */
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

            #printable-challan {
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

            #printable-challan .bg-slate-900,
            #printable-challan .bg-slate-950,
            #printable-challan .bg-amber-500\/10,
            #printable-challan .bg-emerald-500\/10,
            #printable-challan .bg-rose-500\/10 {
                background-color: #f8fafc !important;
                border: 1px solid #cbd5e1 !important;
                color: #000000 !important;
            }

            #printable-challan h1, 
            #printable-challan h2, 
            #printable-challan h3, 
            #printable-challan span, 
            #printable-challan p,
            #printable-challan td {
                color: #000000 !important;
            }

            #printable-challan .font-mono {
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
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs font-bold uppercase tracking-wider">
                <i class="fa-solid fa-users-gear text-sm"></i>
                <span>{{ __('FBR and Punjab charges') }}</span>
            </div>
            
            <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight">
                {{ __('Punjab Registry & Tax Calculator') }}
            </h1>
            
            <p class="text-sm text-slate-400 max-w-2xl mx-auto leading-relaxed">
                {{ __('Estimate stamp duty, TMA tax, registry fees and FBR 236-K and 236-C tax for each buyer and seller.') }}
            </p>
        </div>

        <!-- MAIN LAYOUT CONTAINER (FLEXBOX REPLACED GRID) -->
        <div class="calculator-wrapper w-full">
            
            <!-- LEFT COLUMN: INPUT WIZARD -->
            <div class="calculator-form w-full bg-slate-900/90 rounded-3xl border border-slate-800 p-6 sm:p-7 shadow-2xl space-y-6 no-print">
                
                <h3 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2 border-b border-slate-800 pb-3">
                    <i class="fa-solid fa-sliders text-amber-400"></i>
                    <span>{{ __('Property and value') }}</span>
                </h3>

                <!-- District & Property Type -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 w-full">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1.5">{{ __('District') }}</label>
                        <select wire:model.live="city" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none focus:border-amber-500">
                            <option value="Rahim Yar Khan">{{ __('Rahim Yar Khan') }}</option>
                            <option value="Lahore">{{ __('Lahore') }}</option>
                            <option value="Rawalpindi">{{ __('Rawalpindi') }}</option>
                            <option value="Faisalabad">{{ __('Faisalabad') }}</option>
                            <option value="Multan">{{ __('Multan') }}</option>
                            <option value="Gujranwala">{{ __('Gujranwala') }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1.5">{{ __('Property type') }}</label>
                        <select wire:model.live="property_type" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none focus:border-amber-500 font-bold">
                            <option value="commercial">{{ __('Commercial') }}</option>
                            <option value="residential">{{ __('Residential') }}</option>
                            <option value="agriculture">{{ __('Agriculture') }}</option>
                        </select>
                    </div>
                </div>

                <!-- Area Size, Unit & DC Rate -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 w-full">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">{{ __('Unit') }}</label>
                        <select wire:model.live="area_unit" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-2.5 py-2 text-xs text-white focus:border-amber-500 outline-none">
                            <option value="marla">{{ __('Marla') }}</option>
                            <option value="kanal">{{ __('Kanal') }}</option>
                            <option value="sqft">{{ __('Sq ft') }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">{{ __('Total area') }}</label>
                        <input type="number" step="any" wire:model.live.debounce.300ms="area_size" 
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-amber-400 font-mono font-bold focus:border-amber-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">{{ __('DC rate per unit') }}</label>
                        <input type="number" step="1000" wire:model.live.debounce.300ms="rate_per_unit" 
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold focus:border-amber-500 outline-none">
                    </div>
                </div>

                <!-- Commercial Construction Assessment -->
                @if($property_type !== 'agriculture')
                    <div class="p-4 bg-slate-950 rounded-2xl border border-amber-500/30 space-y-3 w-full">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-amber-400 flex items-center gap-1.5">
                                <i class="fa-solid fa-building"></i>
                                <span>{{ __('Is there a building on it?') }}</span>
                            </span>
                            <input type="checkbox" wire:model.live="is_constructed" class="w-4 h-4 rounded text-amber-500 bg-slate-900 border-slate-700">
                        </div>

                        @if($is_constructed)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-800">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">{{ __('Covered area (sq ft)') }}</label>
                                    <input type="number" wire:model.live.debounce.300ms="covered_area_sqft" 
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold focus:border-amber-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">{{ __('Building DC rate per sq ft') }}</label>
                                    <input type="number" wire:model.live.debounce.300ms="structure_dc_rate_sqft" 
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold focus:border-amber-500 outline-none">
                                </div>
                            </div>

                            <label class="flex items-center gap-2 p-2 bg-slate-900 rounded-xl border border-slate-800 cursor-pointer">
                                <input type="checkbox" wire:model.live="has_approved_map" class="w-4 h-4 rounded text-emerald-500 bg-slate-950 border-slate-700">
                                <span class="text-[11px] text-slate-300">{{ __('Approved building plan exists (otherwise a 2% fee is added)') }}</span>
                            </label>
                        @endif
                    </div>
                @endif

                <!-- Total Declared Display -->
                <div class="p-3.5 bg-slate-950 rounded-2xl border border-slate-800 flex items-center justify-between w-full">
                    <span class="text-xs font-bold text-slate-400">{{ __('Total value (land + building):') }}</span>
                    <span class="text-base font-black text-amber-400 font-mono">PKR {{ number_format($total_declared_value) }}</span>
                </div>

                <div>
                        <label class="block text-xs font-bold text-slate-400 mb-1.5">{{ __('Stamp duty rate (%)') }}</label>
                        <input type="number" min="0" max="10" step="0.1" wire:model.live.debounce.300ms="stamp_duty_pct" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold focus:border-amber-500 outline-none">
                        <p class="text-[11px] text-slate-500 mt-1">{{ __('Confirm the current rate for your area at the registry office and change it here if needed.') }}</p>
                    </div>

                    <!-- Checkbox: TMA Limit -->
                <label class="flex items-center gap-3 p-3 bg-slate-950/60 rounded-xl border border-slate-800 cursor-pointer w-full">
                    <input type="checkbox" wire:model.live="is_within_tma" class="w-4 h-4 rounded text-amber-500 bg-slate-900 border-slate-700 focus:ring-0">
                    <div class="text-xs">
                        <span class="font-bold text-white block">{{ __('Inside TMA or municipal limits') }}</span>
                        <span class="text-[10px] text-slate-500">{{ __('Adds 1% local council tax') }}</span>
                    </div>
                </label>

                <!-- MULTI-BUYERS SECTION -->
                <div class="space-y-3 pt-3 border-t border-slate-800 w-full">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black text-emerald-400 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-user-plus"></i> {{ __('Buyers, shares and filer status') }}
                        </span>
                        <button type="button" wire:click="addBuyer" class="px-2.5 py-1 bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 rounded-lg text-[11px] font-bold hover:bg-emerald-500 hover:text-slate-950 transition cursor-pointer">
                            {{ __('+ Add buyer') }}
                        </button>
                    </div>

                    <div class="space-y-2 w-full">
                        @foreach($buyers as $bIdx => $buyer)
                            <div class="p-3 bg-slate-950 rounded-2xl border border-slate-800 space-y-2 w-full">
                                <div class="flex items-center justify-between gap-2 w-full">
                                    <input type="text" wire:model.live="buyers.{{ $bIdx }}.name" placeholder="{{ __('Buyer name') }}"
                                           class="bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-xs text-white flex-grow">
                                    
                                    <div class="flex items-center gap-1.5 shrink-0">
                                        <div class="flex items-center gap-1">
                                            <input type="number" wire:model.live="buyers.{{ $bIdx }}.share" placeholder="{{ __('Share') }}"
                                                   class="bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-xs text-amber-400 font-mono w-14 text-center">
                                            <span class="text-xs text-slate-400">%</span>
                                        </div>

                                        <button type="button" wire:click="$toggle('buyers.{{ $bIdx }}.is_filer')"
                                                class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase cursor-pointer {{ $buyer['is_filer'] ? 'bg-emerald-500 text-slate-950' : 'bg-rose-500 text-white' }}">
                                            {{ $buyer['is_filer'] ? __('Filer') : __('Non-filer') }}
                                        </button>

                                        @if(count($buyers) > 1)
                                            <button type="button" wire:click="removeBuyer({{ $bIdx }})" class="text-slate-500 hover:text-rose-400 px-1 text-xs cursor-pointer">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- MULTI-SELLERS SECTION -->
                <div class="space-y-3 pt-3 border-t border-slate-800 w-full">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black text-sky-400 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-user-minus"></i> {{ __('Sellers, shares and filer status') }}
                        </span>
                        <button type="button" wire:click="addSeller" class="px-2.5 py-1 bg-sky-500/20 text-sky-400 border border-sky-500/30 rounded-lg text-[11px] font-bold hover:bg-sky-500 hover:text-slate-950 transition cursor-pointer">
                            {{ __('+ Add seller') }}
                        </button>
                    </div>

                    <div class="space-y-2 w-full">
                        @foreach($sellers as $sIdx => $seller)
                            <div class="p-3 bg-slate-950 rounded-2xl border border-slate-800 space-y-2 w-full">
                                <div class="flex items-center justify-between gap-2 w-full">
                                    <input type="text" wire:model.live="sellers.{{ $sIdx }}.name" placeholder="{{ __('Seller name') }}"
                                           class="bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-xs text-white flex-grow">
                                    
                                    <div class="flex items-center gap-1.5 shrink-0">
                                        <div class="flex items-center gap-1">
                                            <input type="number" wire:model.live="sellers.{{ $sIdx }}.share" placeholder="{{ __('Share') }}"
                                                   class="bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-xs text-amber-400 font-mono w-14 text-center">
                                            <span class="text-xs text-slate-400">%</span>
                                        </div>

                                        <button type="button" wire:click="$toggle('sellers.{{ $sIdx }}.is_filer')"
                                                class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase cursor-pointer {{ $seller['is_filer'] ? 'bg-emerald-500 text-slate-950' : 'bg-rose-500 text-white' }}">
                                            {{ $seller['is_filer'] ? __('Filer') : __('Non-filer') }}
                                        </button>

                                        @if(count($sellers) > 1)
                                            <button type="button" wire:click="removeSeller({{ $sIdx }})" class="text-slate-500 hover:text-rose-400 px-1 text-xs cursor-pointer">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: OFFICIAL CHALLAN BREAKDOWN -->
            <div class="calculator-output w-full space-y-5">
                
                <!-- Quick Print / Share Actions -->
                <div id="action-bar" class="no-print flex items-center justify-between gap-3 p-4 bg-slate-900 rounded-2xl border border-slate-800 w-full">
                    <span class="text-xs font-bold text-slate-400">{{ __('Tax summary:') }}</span>
                    <div class="flex items-center gap-2">
                        <button onclick="window.print()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 transition border border-slate-700 cursor-pointer">
                            <i class="fa-solid fa-print text-amber-400"></i> {{ __('Print / PDF') }}
                        </button>

                        <a href="https://wa.me/?text={{ urlencode(__('Registry cost estimate:') . ' PKR ' . number_format($grand_total_taxes) . ' - ' . url()->current()) }}" 
                           target="_blank" 
                           class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 transition cursor-pointer">
                            <i class="fa-brands fa-whatsapp text-sm"></i> WhatsApp
                        </a>
                    </div>
                </div>

                <!-- Main Printable Challan Report -->
                <div id="printable-challan" class="bg-slate-900/90 rounded-3xl border border-slate-800 p-6 sm:p-8 shadow-2xl space-y-6 w-full">

                    <!-- Sheet Header -->
                    <div class="border-b border-slate-800 pb-4 flex items-center justify-between w-full">
                        <div>
                            <span class="text-[10px] font-bold text-amber-400 uppercase tracking-widest block">{{ __('Estimate only') }}</span>
                            <h2 class="text-xl font-black text-white">{{ __('Registry cost estimate') }}</h2>
                            <p class="text-xs text-slate-400 mt-0.5">
                                {{ __(ucfirst($property_type)) }} &bull; {{ __('District') }}: {{ __($city) }} &bull; {{ __('Total value') }}: PKR {{ number_format($total_declared_value) }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-2.5 py-1 rounded-lg">{{ __('Rates checked: :date', ['date' => config('tools.registry.rates_as_of')]) }}</span>
                        </div>
                    </div>

                    @if($buyer_share_total != 100 || $seller_share_total != 100)
                        <div class="p-3 rounded-2xl border border-rose-500/40 bg-rose-500/10 text-xs text-rose-200">
                            {{ __('Shares must total 100% on each side. Buyers: :buyers%, sellers: :sellers%. The result below is not complete until this is fixed.', ['buyers' => $buyer_share_total, 'sellers' => $seller_share_total]) }}
                        </div>
                    @endif

                    <!-- Grand Total Banner -->
                    <div class="p-5 bg-gradient-to-r from-amber-500/10 via-slate-950 to-slate-950 rounded-2xl border border-amber-500/30 flex items-center justify-between w-full">
                        <div>
                            <span class="text-xs font-bold text-slate-400 block">{{ __('Estimated total taxes and fees:') }}</span>
                            <span class="text-3xl font-black text-amber-400 font-mono mt-1 block">PKR {{ number_format($grand_total_taxes) }}</span>
                        </div>
                        <i class="fa-solid fa-receipt text-3xl text-amber-400/40"></i>
                    </div>

                    <!-- Fixed Provincial Charges -->
                    <div class="space-y-2 w-full">
                        <span class="text-xs font-bold text-slate-400 uppercase block tracking-wider">{{ __('Provincial and local charges') }}</span>
                        <div class="overflow-x-auto w-full">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="border-b border-slate-800 text-slate-400 uppercase font-bold text-[10px]">
                                        <th class="py-2.5 px-3">{{ __('Charge') }}</th>
                                        <th class="py-2.5 px-3">{{ __('Rate') }}</th>
                                        <th class="py-2.5 px-3 text-right">{{ __('Amount (PKR)') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/60 text-slate-200 font-medium">
                                    <tr>
                                        <td class="py-2.5 px-3 font-bold text-white">{{ __('Stamp duty') }}</td>
                                        <td class="py-2.5 px-3 font-mono">{{ $stamp_duty_pct }}%</td>
                                        <td class="py-2.5 px-3 text-right font-mono font-bold text-white">{{ number_format($stamp_duty) }}</td>
                                    </tr>
                                    @if($map_approval_fee > 0)
                                        <tr class="bg-amber-500/10">
                                            <td class="py-2.5 px-3 font-bold text-amber-400">{{ __('Building plan fee') }}</td>
                                            <td class="py-2.5 px-3 font-mono font-bold text-amber-400">2.0%</td>
                                            <td class="py-2.5 px-3 text-right font-mono font-black text-amber-400">{{ number_format($map_approval_fee) }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td class="py-2.5 px-3 font-bold text-white">{{ __('TMA tax') }}</td>
                                        <td class="py-2.5 px-3 font-mono">{{ $is_within_tma ? '1.0%' : '0%' }}</td>
                                        <td class="py-2.5 px-3 text-right font-mono font-bold text-white">{{ number_format($tma_tax) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2.5 px-3 font-bold text-white">{{ __('PLRA, registration and mutation fees') }}</td>
                                        <td class="py-2.5 px-3 font-mono">{{ __('Fixed slabs') }}</td>
                                        <td class="py-2.5 px-3 text-right font-mono font-bold text-white">{{ number_format($plra_fee + $sub_registrar_fee + $mutation_comparative_fee) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- FBR Section 236-K Person-by-Person Breakdown (Buyers) -->
                    <div class="space-y-2 pt-2 border-t border-slate-800 w-full">
                        <span class="text-xs font-bold text-emerald-400 uppercase block tracking-wider">{{ __('FBR section 236-K: tax for each buyer') }}</span>
                        <div class="overflow-x-auto w-full">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="border-b border-slate-800 text-slate-400 uppercase font-bold text-[10px]">
                                        <th class="py-2 px-3">{{ __('Buyer') }}</th>
                                        <th class="py-2 px-3">{{ __('Share / value') }}</th>
                                        <th class="py-2 px-3">{{ __('Status (rate)') }}</th>
                                        <th class="py-2 px-3 text-right">{{ __('Tax (PKR)') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/60 text-slate-200">
                                    @foreach($buyer_tax_details as $bDetail)
                                        <tr>
                                            <td class="py-2.5 px-3 font-bold text-white">{{ $bDetail['name'] ?: __('Buyer') . ' ' . $bDetail['number'] }}</td>
                                            <td class="py-2.5 px-3 font-mono text-[11px] text-slate-400">{{ $bDetail['share'] }}% ({{ number_format($bDetail['valuation']) }})</td>
                                            <td class="py-2.5 px-3 font-mono">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $bDetail['is_filer'] ? 'bg-emerald-500/20 text-emerald-400' : 'bg-rose-500/20 text-rose-400' }}">
                                                    {{ ($bDetail['is_filer'] ? __('Filer') : __('Non-filer')) . ' (' . $bDetail['rate'] . ')' }}
                                                </span>
                                            </td>
                                            <td class="py-2.5 px-3 text-right font-mono font-bold text-emerald-400">{{ number_format($bDetail['tax']) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- FBR Section 236-C Person-by-Person Breakdown (Sellers) -->
                    <div class="space-y-2 pt-2 border-t border-slate-800 w-full">
                        <span class="text-xs font-bold text-sky-400 uppercase block tracking-wider">{{ __('FBR section 236-C: tax for each seller') }}</span>
                        <div class="overflow-x-auto w-full">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="border-b border-slate-800 text-slate-400 uppercase font-bold text-[10px]">
                                        <th class="py-2 px-3">{{ __('Seller') }}</th>
                                        <th class="py-2 px-3">{{ __('Share / value') }}</th>
                                        <th class="py-2 px-3">{{ __('Status (rate)') }}</th>
                                        <th class="py-2 px-3 text-right">{{ __('Tax (PKR)') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/60 text-slate-200">
                                    @foreach($seller_tax_details as $sDetail)
                                        <tr>
                                            <td class="py-2.5 px-3 font-bold text-white">{{ $sDetail['name'] ?: __('Seller') . ' ' . $sDetail['number'] }}</td>
                                            <td class="py-2.5 px-3 font-mono text-[11px] text-slate-400">{{ $sDetail['share'] }}% ({{ number_format($sDetail['valuation']) }})</td>
                                            <td class="py-2.5 px-3 font-mono">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $sDetail['is_filer'] ? 'bg-emerald-500/20 text-emerald-400' : 'bg-rose-500/20 text-rose-400' }}">
                                                    {{ ($sDetail['is_filer'] ? __('Filer') : __('Non-filer')) . ' (' . $sDetail['rate'] . ')' }}
                                                </span>
                                            </td>
                                            <td class="py-2.5 px-3 text-right font-mono font-bold text-sky-400">{{ number_format($sDetail['tax']) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <p class="text-xs text-slate-400 leading-relaxed">{{ __('This is an estimate. Filer status is taken from what you selected, and the slab is based on the total property value. Confirm the final challan before paying.') }}</p>

                    <!-- Final Split Summary -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-3 border-t border-slate-800 w-full">
                        <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800">
                            <span class="text-xs font-bold text-slate-400 block">{{ __('All buyers pay:') }}</span>
                            <span class="text-xl font-black text-emerald-400 font-mono mt-1 block">
                                PKR {{ number_format($stamp_duty + $tma_tax + $map_approval_fee + $sub_registrar_fee + $mutation_comparative_fee + $fbr_buyer_tax_total) }}
                            </span>
                        </div>

                        <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800">
                            <span class="text-xs font-bold text-slate-400 block">{{ __('All sellers pay:') }}</span>
                            <span class="text-xl font-black text-sky-400 font-mono mt-1 block">
                                PKR {{ number_format($fbr_seller_tax_total) }}
                            </span>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>
    @include('livewire.tools.partials.tool-footer', ['toolKey' => 'tax'])
</div>