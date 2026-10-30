<div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

    <!-- Header Section -->
    <div class="bg-slate-800/90 p-6 rounded-2xl shadow-md border border-slate-700/80 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-black text-white flex items-center gap-2">
                <i class="fa-solid fa-stamp text-amber-400"></i>
                <span>{{ __('Punjab Revenue Official Fee Schedule Calculator') }}</span>
            </h2>
            <p class="text-xs text-slate-400 mt-1">
                {{ __('Comprehensive computation of Stamp Duty, 3% unapproved map fee, FBR (236-C/K), PLRA charges, and custom deed expenses') }}
            </p>
        </div>

        <div class="flex items-center gap-2">
            <span class="px-3.5 py-1.5 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-black rounded-xl">
                <i class="fa-solid fa-calculator mr-1"></i> {{ __('Grand Total Enabled') }}
            </span>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Controls Column (Left 5 Cols) -->
        <div class="lg:col-span-5 bg-slate-800/90 p-6 rounded-2xl border border-slate-700/80 shadow-md space-y-4">
            
            <div class="border-b border-slate-700/80 pb-3 flex justify-between items-center">
                <span class="text-xs font-bold text-white uppercase flex items-center gap-1.5">
                    <i class="fa-solid fa-sliders text-indigo-400"></i>
                    <span>{{ __('Property & Tax Parameters') }}</span>
                </span>
                <span class="text-[10px] bg-amber-500/10 text-amber-400 px-2 py-0.5 rounded font-bold">{{ __('Updated Rates') }}</span>
            </div>

            <!-- Property Value -->
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">{{ __('Valuation / DC Rate (PKR)') }} *</label>
                <input type="number" wire:model.live.debounce.150ms="propertyValue" placeholder="5000000"
                       class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm font-bold text-white focus:outline-none focus:border-amber-500 font-mono">
                <span class="text-[11px] text-slate-400 mt-1 block">{{ __('Enter official DC or FBR valuation of the property.') }}</span>
            </div>

            <!-- Qisam (Property / Transfer Type) -->
            <div>
                <label class="block text-[11px] font-bold text-amber-400 uppercase mb-1">{{ __('Land / Document Type') }}</label>
                <select wire:model.live="propertyType" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500 font-bold">
                    <option value="urban_residential">{{ __('Stamp Duty Urban Area / Residential (1%)') }}</option>
                    <option value="rural_zari">{{ __('Stamp Duty Rural / Agricultural (1%)') }}</option>
                    <option value="mukhtarnama_blood">{{ __('Power of Attorney - Blood Relation (Rs. 3,025 Flat)') }}</option>
                    <option value="mukhtarnama_general">{{ __('Power of Attorney - Valuation Table (2%)') }}</option>
                </select>
            </div>

            <!-- Unapproved Map Toggle (3% Stamp Duty) -->
            <div class="p-3 bg-slate-950/80 rounded-xl border {{ $isMapUnapproved ? 'border-rose-500/50 bg-rose-950/10' : 'border-slate-800' }} transition">
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" wire:model.live="isMapUnapproved" class="rounded bg-slate-800 border-slate-600 text-rose-500 focus:ring-0">
                    <div>
                        <span class="text-xs font-bold {{ $isMapUnapproved ? 'text-rose-400' : 'text-slate-200' }}">{{ __('Building map is unapproved (Stamp Duty 3%)') }}</span>
                        <span class="text-[10px] text-slate-400 block">{{ __('Stamp Duty increases from 1% to 3% if building layout/map is not approved.') }}</span>
                    </div>
                </label>
            </div>

            <!-- TMA Tax Toggle -->
            <div>
                <label class="flex items-center gap-2 cursor-pointer p-3 bg-slate-900/80 border border-slate-700 rounded-xl text-xs font-semibold text-slate-300">
                    <input type="checkbox" wire:model.live="hasTmaTax" class="rounded bg-slate-800 border-slate-600 text-amber-500 focus:ring-0">
                    <span>{{ __('Include TMA Tax (1%) (Town Committee / Municipal Limits)') }}</span>
                </label>
            </div>

            <!-- Filer Status (Buyer & Seller) -->
            <div class="grid grid-cols-2 gap-3 pt-1">
                <div>
                    <label class="block text-[11px] font-bold text-emerald-400 uppercase mb-1">{{ __('Buyer (236-K)') }}</label>
                    <select wire:model.live="buyerFilerStatus" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-emerald-500 font-bold">
                        <option value="filer">{{ __('Filer (1.25%)') }}</option>
                        <option value="non_filer">{{ __('Non-Filer (10.5% - 18.5%)') }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-rose-400 uppercase mb-1">{{ __('Seller (236-C)') }}</label>
                    <select wire:model.live="sellerFilerStatus" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-rose-500 font-bold">
                        <option value="filer">{{ __('Filer (2.75%)') }}</option>
                        <option value="non_filer">{{ __('Non-Filer (11.5%)') }}</option>
                    </select>
                </div>
            </div>

            <!-- Extra / Custom Miscellaneous Expenses Input -->
            <div class="p-3 bg-slate-950/80 rounded-xl border border-slate-800 space-y-2">
                <span class="text-[11px] font-bold text-amber-400 uppercase block">{{ __('Additional / Miscellaneous Expenses (Custom)') }}</span>
                
                <input type="text" wire:model.live.debounce.300ms="extraExpensesName" placeholder="{{ __('Description e.g. Legal & Patwari Fee') }}" 
                       class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-slate-300 focus:outline-none focus:border-amber-500">
                
                <div class="relative">
                    <span class="absolute left-3 top-2 text-xs text-slate-500 font-mono">{{ __('PKR') }}</span>
                    <input type="number" wire:model.live.debounce.150ms="extraExpensesAmount" placeholder="0" 
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl pl-12 pr-3 py-1.5 text-xs text-white font-mono font-bold focus:outline-none focus:border-amber-500">
                </div>
            </div>

        </div>

        <!-- Right Side: Official Breakdown Slip (7 Cols) -->
        <div class="lg:col-span-7 bg-slate-900/90 p-6 rounded-3xl border border-slate-800 shadow-2xl space-y-4" x-data>
            
            <div class="border-b border-slate-800 pb-3 flex justify-between items-center">
                <div>
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-receipt text-emerald-400"></i>
                        <span>{{ __('Official Fee Challan & Expense Slip') }}</span>
                    </h3>
                    <p class="text-[11px] text-slate-400">
                        {{ __('Itemized breakdown for Buyer, Seller, and Combined Grand Total') }}
                    </p>
                </div>

                <button type="button"
                        @click="
                            let msg = `📜 *Government of Punjab Land & Registry Challan Estimate*\n` +
                                      `━━━━━━━━━━━━━━━━━━━━\n` +
                                      `💰 *Property Valuation:* PKR {{ number_format($propertyValue) }}\n\n` +
                                      `🔹 *Stamp Duty ({{ $stampDutyPercent }}%):* PKR {{ number_format($stampDuty) }}\n` +
                                      `🔹 *TMA Tax ({{ $hasTmaTax ? '1%' : '0%' }}):* PKR {{ number_format($tmaTax) }}\n` +
                                      `🔹 *FBR Tax 236-K (Buyer {{ $buyerFbrPercent }}%):* PKR {{ number_format($buyerFbrTax) }}\n` +
                                      `🔹 *PLRA Charges:* PKR {{ number_format($plraCharges) }}\n` +
                                      `🔹 *Registration Charges:* PKR {{ number_format($registrationCharges) }}\n` +
                                      `🔹 *Comparison Fee:* PKR {{ number_format($muqablaFee) }}\n` +
                                      `🔹 *Mutation & Misc Fee:* PKR {{ number_format($inteqalFee) }}\n` +
                                      @if($extraExpensesAmount > 0)
                                      `🔹 *{{ $extraExpensesName }}:* PKR {{ number_format($extraExpensesAmount) }}\n` +
                                      @endif
                                      `━━━━━━━━━━━━━━━━━━━━\n` +
                                      `💳 *Total Buyer Expenses:* PKR {{ number_format($totalBuyerExpenses) }}\n\n` +
                                      `📌 *Seller FBR Tax (236-C {{ $sellerFbrPercent }}%):* PKR {{ number_format($sellerFbrTax) }}\n` +
                                      `━━━━━━━━━━━━━━━━━━━━\n` +
                                      `🏛️ *Combined Deal Grand Total (Buyer + Seller):* PKR {{ number_format($grandTotalDealExpenses) }}\n\n` +
                                      `Estimate by: {{ Auth::user()->agency_name ?: Auth::user()->name }}\n` +
                                      `⚡ AssanZameen.com`;
                            navigator.clipboard.writeText(msg);
                            alert('Challan breakdown slip copied to clipboard! Ready to share via WhatsApp.');
                        "
                        class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-md shadow-emerald-600/20 cursor-pointer">
                    <i class="fa-brands fa-whatsapp text-sm"></i>
                    <span>{{ __('Copy WhatsApp Slip') }}</span>
                </button>
            </div>

            <!-- Tax Breakdown Items -->
            <div class="space-y-2 text-xs">
                
                <!-- Stamp Duty -->
                <div class="p-3 bg-slate-950/80 rounded-xl border border-slate-800 flex justify-between items-center">
                    <div>
                        <span class="font-bold text-white block">1. {{ __('Stamp Duty') }} — {{ $stampDutyPercent }}%</span>
                        <span class="text-[10px] text-slate-500">
                            {{ $isMapUnapproved ? __('3% Stamp Duty applied for unapproved building map') : __('Standard 1% residential / rural agricultural duty') }}
                        </span>
                    </div>
                    <span class="font-bold text-amber-400 font-mono text-sm">PKR {{ number_format($stampDuty) }}</span>
                </div>

                <!-- TMA Tax -->
                <div class="p-3 bg-slate-950/80 rounded-xl border border-slate-800 flex justify-between items-center">
                    <div>
                        <span class="font-bold text-white block">2. {{ __('TMA Tax') }} — {{ $hasTmaTax ? '1%' : '0%' }}</span>
                        <span class="text-[10px] text-slate-500">{{ __('Town committee or municipal corporation local council fee') }}</span>
                    </div>
                    <span class="font-bold text-slate-200 font-mono text-sm">PKR {{ number_format($tmaTax) }}</span>
                </div>

                <!-- Buyer FBR 236-K -->
                <div class="p-3 bg-slate-950/80 rounded-xl border border-slate-800 flex justify-between items-center">
                    <div>
                        <span class="font-bold text-white block">3. {{ __('FBR Advance Tax 236-K (Buyer)') }} — {{ $buyerFbrPercent }}%</span>
                        <span class="text-[10px] text-slate-500">{{ $buyerFilerStatus === 'filer' ? __('Active Filer (1.25%)') : __('Non-Filer Slabs (10.5% - 18.5%)') }}</span>
                    </div>
                    <span class="font-bold text-rose-400 font-mono text-sm">PKR {{ number_format($buyerFbrTax) }}</span>
                </div>

                <!-- PLRA Charges -->
                <div class="p-3 bg-slate-950/80 rounded-xl border border-slate-800 flex justify-between items-center">
                    <div>
                        <span class="font-bold text-white block">4. {{ __('PLRA Charges') }}</span>
                        <span class="text-[10px] text-slate-500">{{ $propertyValue <= 3000000 ? __('Flat Rs. 3,300 up to 3 Million') : __('0.1% for valuations above 3 Million') }}</span>
                    </div>
                    <span class="font-bold text-indigo-400 font-mono text-sm">PKR {{ number_format($plraCharges) }}</span>
                </div>

                <!-- Registration Charges -->
                <div class="p-3 bg-slate-950/80 rounded-xl border border-slate-800 flex justify-between items-center">
                    <div>
                        <span class="font-bold text-white block">5. {{ __('Registration Charges') }}</span>
                        <span class="text-[10px] text-slate-500">{{ $propertyValue <= 500000 ? __('Up to 500k: Rs. 500') : __('Above 500k: Rs. 1,000') }}</span>
                    </div>
                    <span class="font-bold text-slate-200 font-mono text-sm">PKR {{ number_format($registrationCharges) }}</span>
                </div>

                <!-- Muqabla & Inteqal Fees -->
                <div class="grid grid-cols-2 gap-2">
                    <div class="p-2.5 bg-slate-950/80 rounded-xl border border-slate-800 flex justify-between items-center">
                        <div>
                            <span class="font-bold text-white block text-[11px]">{{ __('Comparison Fee') }}</span>
                            <span class="text-[10px] text-slate-500">{{ __('Official verification fee') }}</span>
                        </div>
                        <span class="font-bold text-slate-300 font-mono text-xs">{{ __('PKR 100') }}</span>
                    </div>

                    <div class="p-2.5 bg-slate-950/80 rounded-xl border border-slate-800 flex justify-between items-center">
                        <div>
                            <span class="font-bold text-white block text-[11px]">{{ __('Mutation / Misc Fee') }}</span>
                            <span class="text-[10px] text-slate-500">{{ __('Patwari & Land Record Center') }}</span>
                        </div>
                        <span class="font-bold text-slate-300 font-mono text-xs">{{ __('PKR 600') }}</span>
                    </div>
                </div>

                <!-- Custom Extra Expense Row (If added) -->
                @if($extraExpensesAmount > 0)
                <div class="p-3 bg-amber-500/10 rounded-xl border border-amber-500/20 flex justify-between items-center">
                    <div>
                        <span class="font-bold text-amber-400 block">{{ $extraExpensesName ?: 'Additional Miscellaneous Expenses' }}</span>
                        <span class="text-[10px] text-slate-400">{{ __('Custom or court miscellaneous expenses') }}</span>
                    </div>
                    <span class="font-bold text-amber-400 font-mono text-sm">PKR {{ number_format($extraExpensesAmount) }}</span>
                </div>
                @endif

            </div>

            <!-- Buyer Total Card -->
            <div class="p-4 bg-emerald-950/40 border border-emerald-500/30 rounded-2xl flex justify-between items-center">
                <div>
                    <span class="text-xs text-emerald-300 font-bold uppercase block">{{ __('Total Buyer Expenses (Purchaser Total)') }}</span>
                    <span class="text-[10px] text-slate-400">{{ __('Including Stamp Duty, TMA, FBR 236-K, and government fees') }}</span>
                </div>
                <div class="text-xl font-black text-emerald-400 font-mono">
                    PKR {{ number_format($totalBuyerExpenses) }}
                </div>
            </div>

            <!-- Seller Total Card (FBR 236-C) -->
            <div class="p-3.5 bg-rose-950/30 border border-rose-500/30 rounded-2xl flex justify-between items-center">
                <div>
                    <span class="text-xs text-rose-300 font-bold uppercase block">{{ __('Seller Tax Liability (236-C)') }}</span>
                    <span class="text-[10px] text-slate-400">{{ __('Rate: :percent% (payable by seller / owner)', ['percent' => $sellerFbrPercent]) }}</span>
                </div>
                <div class="text-lg font-black text-rose-400 font-mono">
                    PKR {{ number_format($totalSellerExpenses) }}
                </div>
            </div>

            <!-- Combined Grand Total (Buyer + Seller) -->
            <div class="p-4 bg-gradient-to-r from-amber-500/20 via-slate-900 to-amber-500/20 border-2 border-amber-400/50 rounded-2xl flex justify-between items-center shadow-xl">
                <div>
                    <span class="text-xs text-amber-300 font-black uppercase tracking-wider block">{{ __('Combined Deal Grand Total') }}</span>
                    <span class="text-[10px] text-slate-300">{{ __('All buyer registration expenses + seller tax combined') }}</span>
                </div>
                <div class="text-2xl font-black text-amber-400 font-mono">
                    PKR {{ number_format($grandTotalDealExpenses) }}
                </div>
            </div>

        </div>

    </div>

</div>