<div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-slate-800/90 p-6 rounded-2xl shadow-md border border-slate-700/80 gap-4">
        <div>
            <h2 class="text-2xl font-black text-white flex items-center gap-2">
                <i class="fa-solid fa-calculator text-amber-400"></i>
                <span>پٹواری و لینڈ کیلکولیٹر (Land & Installment Tools)</span>
            </h2>
            <p class="text-xs text-slate-400 mt-1">
                کرم، سرساہی، مرلہ، کنال، مربع اور قسطوں کا باضابطہ شیڈول سیکنڈوں میں نکالیں
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1 bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs font-bold rounded-full">
                <i class="fa-solid fa-compass-drafting mr-1"></i> پنجاب و ریونیو سٹینڈرڈ
            </span>
        </div>
    </div>

    <!-- Main Grid: Land Converter (Left) & Installment Calculator (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- 1. LAND AREA CONVERTER -->
        <div class="bg-slate-800/90 rounded-2xl p-6 border border-slate-700/80 shadow-md space-y-5">
            <div class="flex items-center justify-between border-b border-slate-700/80 pb-3">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-ruler-combined text-indigo-400"></i>
                    <span>رقبہ کنورٹر (Land Measurement)</span>
                </h3>
                
                <!-- Standard Selection -->
                <div class="flex items-center gap-1 bg-slate-900 p-1 rounded-xl border border-slate-700 text-[11px]">
                    <button type="button" wire:click="$set('marlaStandard', 225)"
                            class="px-2.5 py-1 rounded-lg font-bold transition {{ $marlaStandard == 225 ? 'bg-indigo-600 text-white' : 'text-slate-400' }}">
                        225 Sq Ft (سوسائٹی)
                    </button>
                    <button type="button" wire:click="$set('marlaStandard', 272.25)"
                            class="px-2.5 py-1 rounded-lg font-bold transition {{ $marlaStandard == 272.25 ? 'bg-indigo-600 text-white' : 'text-slate-400' }}">
                        272.25 Sq Ft (پٹواری)
                    </button>
                </div>
            </div>

            <!-- Input Fields -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">پیمائش درج کریں (Value)</label>
                    <input type="number" step="any" wire:model.live.debounce.150ms="inputValue"
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm font-bold text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">یونٹ منتخب کریں (Unit)</label>
                    <select wire:model.live="inputUnit"
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm font-bold text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="marla">مرلہ (Marla)</option>
                        <option value="kanal">کنال (Kanal)</option>
                        <option value="sq_ft">مربع فٹ (Sq Feet)</option>
                        <option value="sq_yards">مربع گز (Sq Yards / Gaz)</option>
                        <option value="sarsahi">سرساہی (Sarsahi)</option>
                        <option value="karam">مربع کرم (Sq Karam)</option>
                        <option value="murabba">مربع (Murabba - 25 Kanal)</option>
                    </select>
                </div>
            </div>

            <!-- Conversion Results Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 pt-2">
                <div class="bg-slate-900/90 p-3.5 rounded-xl border border-slate-700/60">
                    <span class="text-[10px] text-slate-400 font-bold block uppercase">کل مرلہ (Marla)</span>
                    <span class="text-lg font-black text-amber-400">{{ $marla }}</span>
                </div>

                <div class="bg-slate-900/90 p-3.5 rounded-xl border border-slate-700/60">
                    <span class="text-[10px] text-slate-400 font-bold block uppercase">کنال (Kanal)</span>
                    <span class="text-lg font-black text-indigo-400">{{ $kanal }}</span>
                </div>

                <div class="bg-slate-900/90 p-3.5 rounded-xl border border-slate-700/60">
                    <span class="text-[10px] text-slate-400 font-bold block uppercase">مربع فٹ (Sq Ft)</span>
                    <span class="text-lg font-black text-emerald-400 font-mono">{{ number_format($sqFt, 1) }}</span>
                </div>

                <div class="bg-slate-900/90 p-3.5 rounded-xl border border-slate-700/60">
                    <span class="text-[10px] text-slate-400 font-bold block uppercase">مربع گز (Sq Yards)</span>
                    <span class="text-lg font-black text-cyan-400 font-mono">{{ number_format($sqYards, 1) }}</span>
                </div>

                <div class="bg-slate-900/90 p-3.5 rounded-xl border border-slate-700/60">
                    <span class="text-[10px] text-slate-400 font-bold block uppercase">سرساہی (Sarsahi)</span>
                    <span class="text-lg font-black text-purple-400 font-mono">{{ $sarsahi }}</span>
                </div>

                <div class="bg-slate-900/90 p-3.5 rounded-xl border border-slate-700/60">
                    <span class="text-[10px] text-slate-400 font-bold block uppercase">مربع کرم (Sq Karam)</span>
                    <span class="text-lg font-black text-rose-400 font-mono">{{ $karam }}</span>
                </div>
            </div>

            <!-- WhatsApp Copy Result Helper -->
            <div class="pt-2" x-data>
                <button type="button"
                        @click="
                            let msg = `📐 *پراپرٹی رقبہ تفصیل (Land Area Details)*\n\n` +
                                      `🔹 مرلہ: {{ $marla }} Marla\n` +
                                      `🔹 کنال: {{ $kanal }} Kanal\n` +
                                      `🔹 مربع فٹ: {{ number_format($sqFt, 1) }} Sq Ft\n` +
                                      `🔹 مربع گز: {{ number_format($sqYards, 1) }} Gaz\n` +
                                      `🔹 سٹینڈرڈ: {{ $marlaStandard }} Sq Ft فی مرلہ\n\n` +
                                      `حساب بذریعہ AssanZameen.com`;
                            navigator.clipboard.writeText(msg);
                            alert('رقبے کا حساب کاپی ہو گیا! گاہک کو واٹس ایپ پر بھیج دیں۔');
                        "
                        class="w-full py-2.5 bg-slate-900 hover:bg-slate-700 border border-slate-700 text-amber-400 font-bold text-xs rounded-xl transition flex items-center justify-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-copy"></i>
                    <span>یہ رقبہ تفصیل کاپی کریں (برائے واٹس ایپ)</span>
                </button>
            </div>
        </div>

        <!-- 2. INSTALLMENT CALCULATOR -->
        <div class="bg-slate-800/90 rounded-2xl p-6 border border-slate-700/80 shadow-md space-y-5">
            <div class="border-b border-slate-700/80 pb-3">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-file-invoice-dollar text-emerald-400"></i>
                    <span>قسطوں کا پلان کیلکولیٹر (Installment Schedule)</span>
                </h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">پلاٹ کی کل قیمت (PKR)</label>
                    <input type="number" wire:model.live.debounce.150ms="totalPrice"
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm font-bold text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">ڈاؤن پیمنٹ / بیعانہ (%)</label>
                    <select wire:model.live="downPaymentPercent"
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm font-bold text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="10">10%</option>
                        <option value="15">15%</option>
                        <option value="20">20%</option>
                        <option value="25">25%</option>
                        <option value="30">30%</option>
                        <option value="50">50%</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">مدت (سال)</label>
                    <select wire:model.live="durationYears"
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm font-bold text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="1">1 سال (12 ماہ)</option>
                        <option value="2">2 سال (24 ماہ)</option>
                        <option value="3">3 سال (36 ماہ)</option>
                        <option value="4">4 سال (48 ماہ)</option>
                        <option value="5">5 سال (60 ماہ)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">قسط کا وقفہ (Frequency)</label>
                    <select wire:model.live="installmentFrequency"
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm font-bold text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="1">ماہانہ (Monthly)</option>
                        <option value="3">سہ ماہی (Quarterly - ہر 3 ماہ بعد)</option>
                        <option value="6">چھ ماہی (Bi-Annually - ہر 6 ماہ بعد)</option>
                    </select>
                </div>
            </div>

            <!-- Installment Results Summary -->
            <div class="p-4 bg-slate-900/90 rounded-2xl border border-slate-700/80 space-y-3">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-400">ڈاؤن پیمنٹ / بیعانہ رقم:</span>
                    <span class="font-bold text-amber-400 font-mono text-sm">PKR {{ number_format($downPaymentAmount) }}</span>
                </div>

                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-400">بقیہ واجب الادا رقم:</span>
                    <span class="font-bold text-slate-200 font-mono text-sm">PKR {{ number_format($remainingBalance) }}</span>
                </div>

                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-400">کل اقساط کی تعداد:</span>
                    <span class="font-bold text-indigo-400 font-mono text-sm">{{ $totalInstallments }} اقساط</span>
                </div>

                <div class="pt-2 border-t border-slate-800 flex justify-between items-center">
                    <span class="text-sm font-bold text-white">ہر قسط کی رقم:</span>
                    <span class="text-lg font-black text-emerald-400 font-mono">PKR {{ number_format($installmentAmount) }}</span>
                </div>
            </div>

            <!-- WhatsApp Installment Plan Copy -->
            <div x-data>
                <button type="button"
                        @click="
                            let plan = `📊 *آسان اقساط پلان (Installment Schedule)*\n\n` +
                                       `💰 *کل قیمت:* PKR {{ number_format($totalPrice) }}\n` +
                                       `💵 *ڈاؤن پیمنٹ ({{ $downPaymentPercent }}%):* PKR {{ number_format($downPaymentAmount) }}\n` +
                                       `⏳ *مدت:* {{ $durationYears }} سال\n` +
                                       `📅 *اقساط کی تعداد:* {{ $totalInstallments }}\n` +
                                       `💳 *ہر قسط کی رقم:* PKR {{ number_format($installmentAmount) }}\n\n` +
                                       `تفصیلات بذریعہ AssanZameen.com`;
                            navigator.clipboard.writeText(plan);
                            alert('اقساط کا مکمل پلان کاپی ہو گیا! گاہک کو واٹس ایپ کریں۔');
                        "
                        class="w-full py-2.5 bg-emerald-600/20 hover:bg-emerald-600 border border-emerald-500/30 text-emerald-400 hover:text-white font-bold text-xs rounded-xl transition flex items-center justify-center gap-2 cursor-pointer">
                    <i class="fa-brands fa-whatsapp text-sm"></i>
                    <span>اقساط پلان کاپی کریں (برائے واٹس ایپ)</span>
                </button>
            </div>
        </div>

    </div>

</div>