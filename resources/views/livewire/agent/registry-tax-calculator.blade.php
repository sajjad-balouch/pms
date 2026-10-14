<div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

    <!-- Header Section -->
    <div class="bg-slate-800/90 p-6 rounded-2xl shadow-md border border-slate-700/80 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-black text-white flex items-center gap-2">
                <i class="fa-solid fa-stamp text-amber-400"></i>
                <span>پنجاب ریونیو سرکاری فیس شیڈول کیلکولیٹر</span>
            </h2>
            <p class="text-xs text-slate-400 mt-1">
                سرکاری شیڈول کے مطابق اسٹامپ ڈیوٹی، نقشہ پاس نہ ہونے پر 3%، ایف بی آر (236-C/K)، پی ایل آر اے اور کسٹم اخراجات کا جامع حساب
            </p>
        </div>

        <div class="flex items-center gap-2">
            <span class="px-3.5 py-1.5 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-black rounded-xl">
                <i class="fa-solid fa-calculator mr-1"></i> Grand Total Enabled
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
                    <span>پراپرٹی و ٹیکس پیرامیٹرز</span>
                </span>
                <span class="text-[10px] bg-amber-500/10 text-amber-400 px-2 py-0.5 rounded font-bold">Updated Rates</span>
            </div>

            <!-- Property Value -->
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">مالیت / ڈی سی ریٹ (PKR) *</label>
                <input type="number" wire:model.live.debounce.150ms="propertyValue" placeholder="5000000"
                       class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm font-bold text-white focus:outline-none focus:border-amber-500 font-mono">
                <span class="text-[11px] text-slate-400 mt-1 block">پراپرٹی کی کل سرکاری ڈی سی / ایف بی آر مالیت درج کریں۔</span>
            </div>

            <!-- Qisam (Property / Transfer Type) -->
            <div>
                <label class="block text-[11px] font-bold text-amber-400 uppercase mb-1">نوعیت اراضی / دستاویز</label>
                <select wire:model.live="propertyType" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500 font-bold">
                    <option value="urban_residential">اسٹامپ ڈیوٹی اربن ایریا / رہائشی (1%)</option>
                    <option value="rural_zari">اسٹامپ ڈیوٹی رورل / زرعی (1%)</option>
                    <option value="mukhtarnama_blood">مختیار نامہ بحق خونی رشتہ (Rs. 3,025 Flat)</option>
                    <option value="mukhtarnama_general">مختیار نامہ مالیٹ بمطابق ویلیو ایشن ٹیبل (2%)</option>
                </select>
            </div>

            <!-- Unapproved Map Toggle (3% Stamp Duty) -->
            <div class="p-3 bg-slate-950/80 rounded-xl border {{ $isMapUnapproved ? 'border-rose-500/50 bg-rose-950/10' : 'border-slate-800' }} transition">
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" wire:model.live="isMapUnapproved" class="rounded bg-slate-800 border-slate-600 text-rose-500 focus:ring-0">
                    <div>
                        <span class="text-xs font-bold {{ $isMapUnapproved ? 'text-rose-400' : 'text-slate-200' }}">مکان / دکان کا نقشہ پاس نہیں ہے (اسٹامپ ڈیوٹی 3%)</span>
                        <span class="text-[10px] text-slate-400 block">اگر نقشہ پاس نہ ہو تو اسٹامپ ڈیوٹی 1% سے بڑھ کر 3% لاگو ہو گی۔</span>
                    </div>
                </label>
            </div>

            <!-- TMA Tax Toggle -->
            <div>
                <label class="flex items-center gap-2 cursor-pointer p-3 bg-slate-900/80 border border-slate-700 rounded-xl text-xs font-semibold text-slate-300">
                    <input type="checkbox" wire:model.live="hasTmaTax" class="rounded bg-slate-800 border-slate-600 text-amber-500 focus:ring-0">
                    <span>TMA ٹیکس (1%) شامل کریں (ٹاؤن کمیٹی / میونسپل حدود)</span>
                </label>
            </div>

            <!-- Filer Status (Buyer & Seller) -->
            <div class="grid grid-cols-2 gap-3 pt-1">
                <div>
                    <label class="block text-[11px] font-bold text-emerald-400 uppercase mb-1">خریدار (236-K)</label>
                    <select wire:model.live="buyerFilerStatus" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-emerald-500 font-bold">
                        <option value="filer">فائلر (1.25%)</option>
                        <option value="non_filer">نان فائلر (10.5% - 18.5%)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-rose-400 uppercase mb-1">فروخت کنندہ (236-C)</label>
                    <select wire:model.live="sellerFilerStatus" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-rose-500 font-bold">
                        <option value="filer">فائلر (2.75%)</option>
                        <option value="non_filer">نان فائلر (11.5%)</option>
                    </select>
                </div>
            </div>

            <!-- Extra / Custom Miscellaneous Expenses Input -->
            <div class="p-3 bg-slate-950/80 rounded-xl border border-slate-800 space-y-2">
                <span class="text-[11px] font-bold text-amber-400 uppercase block">اضافی / متفرق اخراجات (Custom Expenses)</span>
                
                <input type="text" wire:model.live.debounce.300ms="extraExpensesName" placeholder="تفصیل مثلاً: وکیل و پٹواری فیس" 
                       class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-slate-300 focus:outline-none focus:border-amber-500">
                
                <div class="relative">
                    <span class="absolute left-3 top-2 text-xs text-slate-500 font-mono">PKR</span>
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
                        <span>سرکاری چالان و اخراجات سلپ</span>
                    </h3>
                    <p class="text-[11px] text-slate-400">
                        خریدار اور فروخت کنندہ کا الگ الگ اور مجموعی گرینڈ ٹوٹل
                    </p>
                </div>

                <button type="button"
                        @click="
                            let msg = `📜 *حکومت پنجاب اراضی و رجسٹری چالان تخمینہ*\n` +
                                      `━━━━━━━━━━━━━━━━━━━━\n` +
                                      `💰 *پراپرٹی مالیت:* PKR {{ number_format($propertyValue) }}\n\n` +
                                      `🔹 *اسٹامپ ڈیوٹی ({{ $stampDutyPercent }}%):* PKR {{ number_format($stampDuty) }}\n` +
                                      `🔹 *TMA ٹیکس ({{ $hasTmaTax ? '1%' : '0%' }}):* PKR {{ number_format($tmaTax) }}\n` +
                                      `🔹 *FBR ٹیکس 236-K (خریدار {{ $buyerFbrPercent }}%):* PKR {{ number_format($buyerFbrTax) }}\n` +
                                      `🔹 *PLRA چارجز:* PKR {{ number_format($plraCharges) }}\n` +
                                      `🔹 *رجسٹریشن چارجز:* PKR {{ number_format($registrationCharges) }}\n` +
                                      `🔹 *مقابلہ فیس:* PKR {{ number_format($muqablaFee) }}\n` +
                                      `🔹 *انتقال فیس وغیرہ:* PKR {{ number_format($inteqalFee) }}\n` +
                                      @if($extraExpensesAmount > 0)
                                      `🔹 *{{ $extraExpensesName }}:* PKR {{ number_format($extraExpensesAmount) }}\n` +
                                      @endif
                                      `━━━━━━━━━━━━━━━━━━━━\n` +
                                      `💳 *خریدار کے کل اخراجات:* PKR {{ number_format($totalBuyerExpenses) }}\n\n` +
                                      `📌 *فروخت کنندہ FBR ٹیکس (236-C {{ $sellerFbrPercent }}%):* PKR {{ number_format($sellerFbrTax) }}\n` +
                                      `━━━━━━━━━━━━━━━━━━━━\n` +
                                      `🏛️ *سودے کا مجموعی گرینڈ ٹوٹل (Buyer + Seller):* PKR {{ number_format($grandTotalDealExpenses) }}\n\n` +
                                      `تخمینہ بذریعہ: {{ Auth::user()->agency_name ?: Auth::user()->name }}\n` +
                                      `⚡ AssanZameen.com`;
                            navigator.clipboard.writeText(msg);
                            alert('مکمل چالان سلپ مع گرینڈ ٹوٹل کاپی ہو گئی! واٹس ایپ پر کسٹمر کو بھیجیں۔');
                        "
                        class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-md shadow-emerald-600/20 cursor-pointer">
                    <i class="fa-brands fa-whatsapp text-sm"></i>
                    <span>واٹس ایپ سلپ کاپی کریں</span>
                </button>
            </div>

            <!-- Tax Breakdown Items -->
            <div class="space-y-2 text-xs">
                
                <!-- Stamp Duty -->
                <div class="p-3 bg-slate-950/80 rounded-xl border border-slate-800 flex justify-between items-center">
                    <div>
                        <span class="font-bold text-white block">1. اسٹامپ ڈیوٹی (Stamp Duty) — {{ $stampDutyPercent }}%</span>
                        <span class="text-[10px] text-slate-500">
                            {{ $isMapUnapproved ? 'نقشہ پاس نہ ہونے پر 3% اسٹامپ ڈیوٹی' : 'عام رہائشی / زرعی اسٹامپ ڈیوٹی 1%' }}
                        </span>
                    </div>
                    <span class="font-bold text-amber-400 font-mono text-sm">PKR {{ number_format($stampDuty) }}</span>
                </div>

                <!-- TMA Tax -->
                <div class="p-3 bg-slate-950/80 rounded-xl border border-slate-800 flex justify-between items-center">
                    <div>
                        <span class="font-bold text-white block">2. TMA ٹیکس — {{ $hasTmaTax ? '1%' : '0%' }}</span>
                        <span class="text-[10px] text-slate-500">ٹاؤن کمیٹی یا میونسپل حدود کی لوکل کونسل فیس</span>
                    </div>
                    <span class="font-bold text-slate-200 font-mono text-sm">PKR {{ number_format($tmaTax) }}</span>
                </div>

                <!-- Buyer FBR 236-K -->
                <div class="p-3 bg-slate-950/80 rounded-xl border border-slate-800 flex justify-between items-center">
                    <div>
                        <span class="font-bold text-white block">3. FBR ٹیکس 236-K (خریدار) — {{ $buyerFbrPercent }}%</span>
                        <span class="text-[10px] text-slate-500">{{ $buyerFilerStatus === 'filer' ? 'Active Filer (1.25%)' : 'Non-Filer Slabs (10.5% - 18.5%)' }}</span>
                    </div>
                    <span class="font-bold text-rose-400 font-mono text-sm">PKR {{ number_format($buyerFbrTax) }}</span>
                </div>

                <!-- PLRA Charges -->
                <div class="p-3 bg-slate-950/80 rounded-xl border border-slate-800 flex justify-between items-center">
                    <div>
                        <span class="font-bold text-white block">4. PLRA چارجز</span>
                        <span class="text-[10px] text-slate-500">{{ $propertyValue <= 3000000 ? '30 لاکھ تک فلیٹ 3300 روپے' : '30 لاکھ سے زائد پر 0.1%' }}</span>
                    </div>
                    <span class="font-bold text-indigo-400 font-mono text-sm">PKR {{ number_format($plraCharges) }}</span>
                </div>

                <!-- Registration Charges -->
                <div class="p-3 bg-slate-950/80 rounded-xl border border-slate-800 flex justify-between items-center">
                    <div>
                        <span class="font-bold text-white block">5. رجسٹریشن چارجز (Registration Charges)</span>
                        <span class="text-[10px] text-slate-500">{{ $propertyValue <= 500000 ? '5 لاکھ تک: 500 روپے' : '5 لاکھ سے زائد: 1000 روپے' }}</span>
                    </div>
                    <span class="font-bold text-slate-200 font-mono text-sm">PKR {{ number_format($registrationCharges) }}</span>
                </div>

                <!-- Muqabla & Inteqal Fees -->
                <div class="grid grid-cols-2 gap-2">
                    <div class="p-2.5 bg-slate-950/80 rounded-xl border border-slate-800 flex justify-between items-center">
                        <div>
                            <span class="font-bold text-white block text-[11px]">مقابلہ فیس</span>
                            <span class="text-[10px] text-slate-500">سرکاری موازنہ فیس</span>
                        </div>
                        <span class="font-bold text-slate-300 font-mono text-xs">PKR 100</span>
                    </div>

                    <div class="p-2.5 bg-slate-950/80 rounded-xl border border-slate-800 flex justify-between items-center">
                        <div>
                            <span class="font-bold text-white block text-[11px]">انتقال فیس وغیرہ</span>
                            <span class="text-[10px] text-slate-500">پٹواری و اراضی سنٹر</span>
                        </div>
                        <span class="font-bold text-slate-300 font-mono text-xs">PKR 600</span>
                    </div>
                </div>

                <!-- Custom Extra Expense Row (If added) -->
                @if($extraExpensesAmount > 0)
                <div class="p-3 bg-amber-500/10 rounded-xl border border-amber-500/20 flex justify-between items-center">
                    <div>
                        <span class="font-bold text-amber-400 block">{{ $extraExpensesName ?: 'اضافی متفرق اخراجات' }}</span>
                        <span class="text-[10px] text-slate-400">کسٹم فیس یا کچہری کے متفرق اخراجات</span>
                    </div>
                    <span class="font-bold text-amber-400 font-mono text-sm">PKR {{ number_format($extraExpensesAmount) }}</span>
                </div>
                @endif

            </div>

            <!-- Buyer Total Card -->
            <div class="p-4 bg-emerald-950/40 border border-emerald-500/30 rounded-2xl flex justify-between items-center">
                <div>
                    <span class="text-xs text-emerald-300 font-bold uppercase block">خریدار کے کل اخراجات (Purchaser Total)</span>
                    <span class="text-[10px] text-slate-400">اسٹامپ، TMA، FBR 236-K اور تمام فیسیں ملا کر</span>
                </div>
                <div class="text-xl font-black text-emerald-400 font-mono">
                    PKR {{ number_format($totalBuyerExpenses) }}
                </div>
            </div>

            <!-- Seller Total Card (FBR 236-C) -->
            <div class="p-3.5 bg-rose-950/30 border border-rose-500/30 rounded-2xl flex justify-between items-center">
                <div>
                    <span class="text-xs text-rose-300 font-bold uppercase block">فروخت کنندہ کا ٹیکس (Seller Total — 236-C)</span>
                    <span class="text-[10px] text-slate-400">شرح: {{ $sellerFbrPercent }}% (مالک کے اپنے ذمہ ادا کرنے کی رقم)</span>
                </div>
                <div class="text-lg font-black text-rose-400 font-mono">
                    PKR {{ number_format($totalSellerExpenses) }}
                </div>
            </div>

            <!-- Combined Grand Total (Buyer + Seller) -->
            <div class="p-4 bg-gradient-to-r from-amber-500/20 via-slate-900 to-amber-500/20 border-2 border-amber-400/50 rounded-2xl flex justify-between items-center shadow-xl">
                <div>
                    <span class="text-xs text-amber-300 font-black uppercase tracking-wider block">سودے کا مجموعی گرینڈ ٹوٹل (Combined Deal Grand Total)</span>
                    <span class="text-[10px] text-slate-300">خریدار کے تمام اخراجات + بیچنے والے کا ٹیکس ملا کر</span>
                </div>
                <div class="text-2xl font-black text-amber-400 font-mono">
                    PKR {{ number_format($grandTotalDealExpenses) }}
                </div>
            </div>

        </div>

    </div>

</div>