<div class="min-h-screen bg-[#070a13] text-slate-100 py-10 px-4 sm:px-6 lg:px-8 w-full overflow-x-hidden" style="font-family: Noto Nastaliq Urdu">

    <!-- FontAwesome Fallback CDN & Print Styling -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

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
                <span>Multi-Party Proportionate Tax Engine</span>
            </div>
            
            <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight" style="font-family: 'Noto Nastaliq Urdu', 'Noto Sans Arabic', serif; line-height: 1.8;">
                پنجاب رجسٹری و ملٹی فائلر چالان کیلکولیٹر
            </h1>
            
            <p class="text-sm text-slate-400 max-w-2xl mx-auto leading-relaxed">
                اراضی یا کمرشل عمارت کے مشترکہ خریداروں اور فروخت کنندگان کا حصہ بہ حصہ (Proportionate Share) اور فائلر بمقابلہ نان فائلر چالان شیڈول۔
            </p>
        </div>

        <!-- MAIN LAYOUT CONTAINER (FLEXBOX REPLACED GRID) -->
        <div class="calculator-wrapper w-full">
            
            <!-- LEFT COLUMN: INPUT WIZARD -->
            <div class="calculator-form w-full bg-slate-900/90 rounded-3xl border border-slate-800 p-6 sm:p-7 shadow-2xl space-y-6 no-print">
                
                <h3 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2 border-b border-slate-800 pb-3">
                    <i class="fa-solid fa-sliders text-amber-400"></i>
                    <span>پراپرٹی و مالیت کوائف</span>
                </h3>

                <!-- District & Property Type -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 w-full">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1.5">ضلع منتخب کریں</label>
                        <select wire:model.live="city" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none focus:border-amber-500">
                            <option value="Rahim Yar Khan">Rahim Yar Khan (رحیم یار خان)</option>
                            <option value="Lahore">Lahore (لاہور)</option>
                            <option value="Rawalpindi">Rawalpindi (راولپنڈی)</option>
                            <option value="Faisalabad">Faisalabad (فیصل آباد)</option>
                            <option value="Multan">Multan (ملتان)</option>
                            <option value="Gujranwala">Gujranwala (گوجرانوالہ)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1.5">نوعیت جائیداد</label>
                        <select wire:model.live="property_type" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none focus:border-amber-500 font-bold">
                            <option value="commercial">کمرشل (Commercial)</option>
                            <option value="residential">رہائشی (Residential)</option>
                            <option value="agriculture">زرعی (Agriculture)</option>
                        </select>
                    </div>
                </div>

                <!-- Area Size, Unit & DC Rate -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 w-full">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">پیمائش اکائی</label>
                        <select wire:model.live="area_unit" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-2.5 py-2 text-xs text-white focus:border-amber-500 outline-none">
                            <option value="marla">مرلہ (Marla)</option>
                            <option value="kanal">کنال (Kanal)</option>
                            <option value="sqft">مربع فٹ (SqFt)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">کل رقبہ</label>
                        <input type="number" step="any" wire:model.live.debounce.300ms="area_size" 
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-amber-400 font-mono font-bold focus:border-amber-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">DC ریٹ / {{ ucfirst($area_unit) }}</label>
                        <input type="number" step="1000" wire:model.live.debounce.300ms="rate_per_unit" 
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold focus:border-amber-500 outline-none">
                    </div>
                </div>

                <!-- Commercial Construction Assessment -->
                @if($property_type === 'commercial')
                    <div class="p-4 bg-slate-950 rounded-2xl border border-amber-500/30 space-y-3 w-full">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-amber-400 flex items-center gap-1.5">
                                <i class="fa-solid fa-building"></i>
                                <span>تعمیر شدہ کمرشل عمارت ہے؟</span>
                            </span>
                            <input type="checkbox" wire:model.live="is_constructed" class="w-4 h-4 rounded text-amber-500 bg-slate-900 border-slate-700">
                        </div>

                        @if($is_constructed)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-800">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">کورڈ ایریا (Sq Ft)</label>
                                    <input type="number" wire:model.live.debounce.300ms="covered_area_sqft" 
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold focus:border-amber-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">تعمیر کا DC ریٹ / SqFt</label>
                                    <input type="number" wire:model.live.debounce.300ms="structure_dc_rate_sqft" 
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold focus:border-amber-500 outline-none">
                                </div>
                            </div>

                            <label class="flex items-center gap-2 p-2 bg-slate-900 rounded-xl border border-slate-800 cursor-pointer">
                                <input type="checkbox" wire:model.live="has_approved_map" class="w-4 h-4 rounded text-emerald-500 bg-slate-950 border-slate-700">
                                <span class="text-[11px] text-slate-300">منظور شدہ نقشہ موجود ہے (اگر نہیں ہے تو 2% فیس لاگو ہو گی)</span>
                            </label>
                        @endif
                    </div>
                @endif

                <!-- Total Declared Display -->
                <div class="p-3.5 bg-slate-950 rounded-2xl border border-slate-800 flex items-center justify-between w-full">
                    <span class="text-xs font-bold text-slate-400">کل ڈیکلیئرڈ مالیت (زمین + تعمیر):</span>
                    <span class="text-base font-black text-amber-400 font-mono">PKR {{ number_format($total_declared_value) }}</span>
                </div>

                <!-- Checkbox: TMA Limit -->
                <label class="flex items-center gap-3 p-3 bg-slate-950/60 rounded-xl border border-slate-800 cursor-pointer w-full">
                    <input type="checkbox" wire:model.live="is_within_tma" class="w-4 h-4 rounded text-amber-500 bg-slate-900 border-slate-700 focus:ring-0">
                    <div class="text-xs">
                        <span class="font-bold text-white block">TMA / میونسپل کمیٹی حدود میں واقع ہے</span>
                        <span class="text-[10px] text-slate-500">1% لوکل کونسل ٹیکس چالان شامل ہوگا</span>
                    </div>
                </label>

                <!-- MULTI-BUYERS SECTION -->
                <div class="space-y-3 pt-3 border-t border-slate-800 w-full">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black text-emerald-400 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-user-plus"></i> خریداران معہ حصہ و فائلر اسٹیٹس
                        </span>
                        <button type="button" wire:click="addBuyer" class="px-2.5 py-1 bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 rounded-lg text-[11px] font-bold hover:bg-emerald-500 hover:text-slate-950 transition cursor-pointer">
                            + خریدار شامل کریں
                        </button>
                    </div>

                    <div class="space-y-2 w-full">
                        @foreach($buyers as $bIdx => $buyer)
                            <div class="p-3 bg-slate-950 rounded-2xl border border-slate-800 space-y-2 w-full">
                                <div class="flex items-center justify-between gap-2 w-full">
                                    <input type="text" wire:model.live="buyers.{{ $bIdx }}.name" placeholder="خریدار کا نام"
                                           class="bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-xs text-white flex-grow">
                                    
                                    <div class="flex items-center gap-1.5 shrink-0">
                                        <div class="flex items-center gap-1">
                                            <input type="number" wire:model.live="buyers.{{ $bIdx }}.share" placeholder="حصہ"
                                                   class="bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-xs text-amber-400 font-mono w-14 text-center">
                                            <span class="text-xs text-slate-400">%</span>
                                        </div>

                                        <button type="button" wire:click="$toggle('buyers.{{ $bIdx }}.is_filer')"
                                                class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase cursor-pointer {{ $buyer['is_filer'] ? 'bg-emerald-500 text-slate-950' : 'bg-rose-500 text-white' }}">
                                            {{ $buyer['is_filer'] ? 'Filer' : 'Non-Filer' }}
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
                            <i class="fa-solid fa-user-minus"></i> فروخت کنندگان معہ حصہ و فائلر اسٹیٹس
                        </span>
                        <button type="button" wire:click="addSeller" class="px-2.5 py-1 bg-sky-500/20 text-sky-400 border border-sky-500/30 rounded-lg text-[11px] font-bold hover:bg-sky-500 hover:text-slate-950 transition cursor-pointer">
                            + بیچنے والا شامل کریں
                        </button>
                    </div>

                    <div class="space-y-2 w-full">
                        @foreach($sellers as $sIdx => $seller)
                            <div class="p-3 bg-slate-950 rounded-2xl border border-slate-800 space-y-2 w-full">
                                <div class="flex items-center justify-between gap-2 w-full">
                                    <input type="text" wire:model.live="sellers.{{ $sIdx }}.name" placeholder="بیچنے والے کا نام"
                                           class="bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-xs text-white flex-grow">
                                    
                                    <div class="flex items-center gap-1.5 shrink-0">
                                        <div class="flex items-center gap-1">
                                            <input type="number" wire:model.live="sellers.{{ $sIdx }}.share" placeholder="حصہ"
                                                   class="bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-xs text-amber-400 font-mono w-14 text-center">
                                            <span class="text-xs text-slate-400">%</span>
                                        </div>

                                        <button type="button" wire:click="$toggle('sellers.{{ $sIdx }}.is_filer')"
                                                class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase cursor-pointer {{ $seller['is_filer'] ? 'bg-emerald-500 text-slate-950' : 'bg-rose-500 text-white' }}">
                                            {{ $seller['is_filer'] ? 'Filer' : 'Non-Filer' }}
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
                    <span class="text-xs font-bold text-slate-400">چالان سمری شیٹ:</span>
                    <div class="flex items-center gap-2">
                        <button onclick="window.print()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 transition border border-slate-700 cursor-pointer">
                            <i class="fa-solid fa-print text-amber-400"></i> پرنٹ / PDF
                        </button>

                        <a href="https://wa.me/?text={{ urlencode('پنجاب رجسٹری چالان تخمینہ: کل سرکاری اخراجات PKR ' . number_format($grand_total_taxes) . '۔ آسان زمین پر چیک کریں: ' . url()->current()) }}" 
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
                            <span class="text-[10px] font-bold text-amber-400 uppercase tracking-widest block">Official Estimate Sheet</span>
                            <h2 class="text-xl font-black text-white">پنجاب اراضی رجسٹری و چالان شیڈول</h2>
                            <p class="text-xs text-slate-400 mt-0.5">
                                {{ $property_type === 'commercial' ? 'کمرشل' : 'رہائشی' }} &bull; ضلع: {{ $city }} &bull; کل سرکاری مالیت: PKR {{ number_format($total_declared_value) }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-2.5 py-1 rounded-lg">Multi-Filer Enabled</span>
                        </div>
                    </div>

                    <!-- Grand Total Banner -->
                    <div class="p-5 bg-gradient-to-r from-amber-500/10 via-slate-950 to-slate-950 rounded-2xl border border-amber-500/30 flex items-center justify-between w-full">
                        <div>
                            <span class="text-xs font-bold text-slate-400 block">تمام سرکاری چالانز کی کل رقم (Grand Total Taxes):</span>
                            <span class="text-3xl font-black text-amber-400 font-mono mt-1 block">PKR {{ number_format($grand_total_taxes) }}</span>
                        </div>
                        <i class="fa-solid fa-receipt text-3xl text-amber-400/40"></i>
                    </div>

                    <!-- Fixed Provincial Charges -->
                    <div class="space-y-2 w-full">
                        <span class="text-xs font-bold text-slate-400 uppercase block tracking-wider">صوبائی و بلدیاتی چالانز (Provincial Dues)</span>
                        <div class="overflow-x-auto w-full">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="border-b border-slate-800 text-slate-400 uppercase font-bold text-[10px]">
                                        <th class="py-2.5 px-3">چالان کا نام</th>
                                        <th class="py-2.5 px-3">شرح</th>
                                        <th class="py-2.5 px-3 text-right">رقم (PKR)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/60 text-slate-200 font-medium">
                                    <tr>
                                        <td class="py-2.5 px-3 font-bold text-white">اسٹامپ ڈیوٹی (e-Stamp Paper)</td>
                                        <td class="py-2.5 px-3 font-mono">1.0%</td>
                                        <td class="py-2.5 px-3 text-right font-mono font-bold text-white">{{ number_format($stamp_duty) }}</td>
                                    </tr>
                                    @if($map_approval_fee > 0)
                                        <tr class="bg-amber-500/10">
                                            <td class="py-2.5 px-3 font-bold text-amber-400">کمرشل نقشہ اپروول فیس (Covered Area)</td>
                                            <td class="py-2.5 px-3 font-mono font-bold text-amber-400">2.0%</td>
                                            <td class="py-2.5 px-3 text-right font-mono font-black text-amber-400">{{ number_format($map_approval_fee) }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td class="py-2.5 px-3 font-bold text-white">ٹی ایم اے ٹیکس (TMA Local Tax)</td>
                                        <td class="py-2.5 px-3 font-mono">{{ $is_within_tma ? '1.0%' : '0%' }}</td>
                                        <td class="py-2.5 px-3 text-right font-mono font-bold text-white">{{ number_format($tma_tax) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2.5 px-3 font-bold text-white">PLRA سروسز، رجسٹریشن و انتقال فیس</td>
                                        <td class="py-2.5 px-3 font-mono">Govt Slabs</td>
                                        <td class="py-2.5 px-3 text-right font-mono font-bold text-white">{{ number_format($plra_fee + $sub_registrar_fee + $mutation_comparative_fee) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- FBR Section 236-K Person-by-Person Breakdown (Buyers) -->
                    <div class="space-y-2 pt-2 border-t border-slate-800 w-full">
                        <span class="text-xs font-bold text-emerald-400 uppercase block tracking-wider">FBR سیکشن 236-K: خریداران کے انفرادی چالانز</span>
                        <div class="overflow-x-auto w-full">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="border-b border-slate-800 text-slate-400 uppercase font-bold text-[10px]">
                                        <th class="py-2 px-3">خریدار</th>
                                        <th class="py-2 px-3">حصہ / مالیت</th>
                                        <th class="py-2 px-3">اسٹیٹس (شرح)</th>
                                        <th class="py-2 px-3 text-right">ٹیکس چالان (PKR)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/60 text-slate-200">
                                    @foreach($buyer_tax_details as $bDetail)
                                        <tr>
                                            <td class="py-2.5 px-3 font-bold text-white">{{ $bDetail['name'] }}</td>
                                            <td class="py-2.5 px-3 font-mono text-[11px] text-slate-400">{{ $bDetail['share'] }}% ({{ number_format($bDetail['valuation']) }})</td>
                                            <td class="py-2.5 px-3 font-mono">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $bDetail['is_filer'] ? 'bg-emerald-500/20 text-emerald-400' : 'bg-rose-500/20 text-rose-400' }}">
                                                    {{ $bDetail['is_filer'] ? 'Filer (' . $bDetail['rate'] . ')' : 'Non-Filer (' . $bDetail['rate'] . ')' }}
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
                        <span class="text-xs font-bold text-sky-400 uppercase block tracking-wider">FBR سیکشن 236-C: فروخت کنندگان کے انفرادی چالانز</span>
                        <div class="overflow-x-auto w-full">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="border-b border-slate-800 text-slate-400 uppercase font-bold text-[10px]">
                                        <th class="py-2 px-3">فروخت کنندہ</th>
                                        <th class="py-2 px-3">حصہ / مالیت</th>
                                        <th class="py-2 px-3">اسٹیٹس (شرح)</th>
                                        <th class="py-2 px-3 text-right">ٹیکس چالان (PKR)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/60 text-slate-200">
                                    @foreach($seller_tax_details as $sDetail)
                                        <tr>
                                            <td class="py-2.5 px-3 font-bold text-white">{{ $sDetail['name'] }}</td>
                                            <td class="py-2.5 px-3 font-mono text-[11px] text-slate-400">{{ $sDetail['share'] }}% ({{ number_format($sDetail['valuation']) }})</td>
                                            <td class="py-2.5 px-3 font-mono">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $sDetail['is_filer'] ? 'bg-emerald-500/20 text-emerald-400' : 'bg-rose-500/20 text-rose-400' }}">
                                                    {{ $sDetail['is_filer'] ? 'Filer (' . $sDetail['rate'] . ')' : 'Non-Filer (' . $sDetail['rate'] . ')' }}
                                                </span>
                                            </td>
                                            <td class="py-2.5 px-3 text-right font-mono font-bold text-sky-400">{{ number_format($sDetail['tax']) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Final Split Summary -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-3 border-t border-slate-800 w-full">
                        <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800">
                            <span class="text-xs font-bold text-slate-400 block">خریداران کا کل حصہ (All Buyers Total):</span>
                            <span class="text-xl font-black text-emerald-400 font-mono mt-1 block">
                                PKR {{ number_format($stamp_duty + $tma_tax + $map_approval_fee + $sub_registrar_fee + $mutation_comparative_fee + $fbr_buyer_tax_total) }}
                            </span>
                        </div>

                        <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800">
                            <span class="text-xs font-bold text-slate-400 block">فروخت کنندگان کا کل حصہ (All Sellers Total):</span>
                            <span class="text-xl font-black text-sky-400 font-mono mt-1 block">
                                PKR {{ number_format($fbr_seller_tax_total) }}
                            </span>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>
</div>