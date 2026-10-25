<div class="min-h-screen bg-[#070a13] text-slate-100 py-10 px-4 sm:px-6 lg:px-8 w-full overflow-x-hidden" style="font-family: 'Noto Nastaliq Urdu', 'Noto Sans Arabic', serif; line-height: 1.8;">

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
                <span>Islamic Inheritance Shariat Law Engine</span>
            </div>
            <br><br>
            <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight">
                شرعی وراثت و انتقالِ اراضی کیلکولیٹر
            </h1>
            
            <p class="text-sm text-slate-400 max-w-2xl mx-auto" style="margin-top: 40px;">
                قرآن و سنت کے عین مطابق اصحاب الفروض اور عصبات کے شرعی حصص کا حساب۔ مرحوم کی زرعی و شہری اراضی اور نقدی کی خودکار پٹوار تقسیم سلپ۔
            </p>
        </div>

        <div class="inherit-wrapper w-full">

            <!-- LEFT COLUMN: INPUT WIZARD (No-Print) -->
            <div class="inherit-form-col w-full bg-slate-900/90 rounded-3xl border border-slate-800 p-6 shadow-2xl space-y-5 no-print">
                <h3 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2 border-b border-slate-800 pb-3">
                    <i class="fa-solid fa-user-group text-amber-400"></i>
                    <span>مرحوم کا ترکہ و ورثاء کے کوائف</span>
                </h3>

                <!-- Deceased Gender -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-400 uppercase">مرحوم کی جنس (فوت ہونے والا)</label>
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" wire:click="$set('deceased_gender', 'male')" 
                                class="py-2.5 px-4 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 cursor-pointer {{ $deceased_gender === 'male' ? 'bg-amber-500 text-slate-950 font-black shadow-lg shadow-amber-500/20' : 'bg-slate-950 text-slate-400 border border-slate-800' }}">
                            <i class="fa-solid fa-mars text-sm"></i> مرد (شوہر / باپ)
                        </button>
                        <button type="button" wire:click="$set('deceased_gender', 'female')" 
                                class="py-2.5 px-4 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 cursor-pointer {{ $deceased_gender === 'female' ? 'bg-amber-500 text-slate-950 font-black shadow-lg shadow-amber-500/20' : 'bg-slate-950 text-slate-400 border border-slate-800' }}">
                            <i class="fa-solid fa-venus text-sm"></i> عورت (بیوی / ماں)
                        </button>
                    </div>
                </div>

                <!-- Estate: Land Details -->
                <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-3">
                    <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider block">اراضی ترکہ (Land to Distribute)</span>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">کنال (Kanals)</label>
                            <input type="number" step="any" wire:model.live.debounce.300ms="total_kanals" 
                                   class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold focus:border-amber-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">مرلہ (Marlas)</label>
                            <input type="number" step="any" wire:model.live.debounce.300ms="total_marlas" 
                                   class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold focus:border-amber-500 outline-none">
                        </div>
                    </div>
                    <div class="text-[11px] text-slate-400 font-mono flex justify-between bg-slate-900/60 p-2 rounded-xl">
                        <span>کل تقسیم شدہ رقبہ:</span>
                        <span class="text-amber-400 font-bold">{{ $net_marlas }} مرلہ</span>
                    </div>
                </div>

                <!-- Estate: Cash, Debts & Funeral -->
                <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-3">
                    <span class="text-xs font-bold text-sky-400 uppercase tracking-wider block">نقدی و قرض کی کٹوتی</span>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">کل نقدی / بینک بیلنس (PKR)</label>
                            <input type="number" step="1000" wire:model.live.debounce.300ms="cash_estate" 
                                   class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold focus:border-amber-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">قرض و تجہیز و تکفین (PKR)</label>
                            <input type="number" step="1000" wire:model.live.debounce.300ms="debts_funeral" 
                                   class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-rose-400 font-mono font-bold focus:border-rose-500 outline-none">
                        </div>
                    </div>
                    <div class="text-[11px] text-slate-400 font-mono flex justify-between bg-slate-900/60 p-2 rounded-xl">
                        <span>خالص قابلِ تقسیم نقدی:</span>
                        <span class="text-emerald-400 font-bold">PKR {{ number_format($net_cash) }}</span>
                    </div>
                </div>

                <!-- Heirs Selection (ورثاء) -->
                <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-3">
                    <span class="text-xs font-bold text-amber-400 uppercase tracking-wider block">زندہ ورثاء کی تعداد</span>

                    <!-- Spouse -->
                    @if($deceased_gender === 'male')
                        <div class="flex items-center justify-between p-2.5 bg-slate-900 rounded-xl border border-slate-800">
                            <span class="text-xs font-bold text-slate-300">زندہ بیوائیں (Wives):</span>
                            <div class="flex items-center gap-2">
                                <input type="number" min="0" max="4" wire:model.live="wives_count" 
                                       class="w-16 bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-xs text-center text-amber-400 font-mono font-bold">
                            </div>
                        </div>
                    @else
                        <div class="flex items-center justify-between p-2.5 bg-slate-900 rounded-xl border border-slate-800">
                            <span class="text-xs font-bold text-slate-300">کیا شوہر زندہ ہے؟</span>
                            <button type="button" wire:click="$toggle('has_husband')" 
                                    class="px-3 py-1 rounded-lg text-xs font-black uppercase {{ $has_husband ? 'bg-emerald-500 text-slate-950' : 'bg-slate-800 text-slate-400' }}">
                                {{ $has_husband ? 'ہاں (Yes)' : 'نہیں (No)' }}
                            </button>
                        </div>
                    @endif

                    <!-- Parents -->
                    <div class="grid grid-cols-2 gap-2">
                        <div class="flex items-center justify-between p-2.5 bg-slate-900 rounded-xl border border-slate-800">
                            <span class="text-xs font-bold text-slate-300">والد (باپ):</span>
                            <button type="button" wire:click="$toggle('has_father')" 
                                    class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $has_father ? 'bg-emerald-500 text-slate-950' : 'bg-slate-800 text-slate-400' }}">
                                {{ $has_father ? 'زندہ' : 'وفات' }}
                            </button>
                        </div>

                        <div class="flex items-center justify-between p-2.5 bg-slate-900 rounded-xl border border-slate-800">
                            <span class="text-xs font-bold text-slate-300">والدہ (ماں):</span>
                            <button type="button" wire:click="$toggle('has_mother')" 
                                    class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $has_mother ? 'bg-emerald-500 text-slate-950' : 'bg-slate-800 text-slate-400' }}">
                                {{ $has_mother ? 'زندہ' : 'وفات' }}
                            </button>
                        </div>
                    </div>

                    <!-- Children -->
                    <div class="grid grid-cols-2 gap-3 pt-1">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">بیٹوں کی تعداد (Sons)</label>
                            <input type="number" min="0" wire:model.live="sons_count" 
                                   class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold text-center">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">بیٹیوں کی تعداد (Daughters)</label>
                            <input type="number" min="0" wire:model.live="daughters_count" 
                                   class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold text-center">
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: OFFICIAL DISTRIBUTION REPORT -->
            <div class="inherit-output-col w-full space-y-5">
                
                <!-- Quick Print / Share Actions -->
                <div id="action-bar" class="no-print flex items-center justify-between gap-3 p-4 bg-slate-900 rounded-2xl border border-slate-800 w-full">
                    <span class="text-xs font-bold text-slate-400">تقسیم نامہ وراثت:</span>
                    <div class="flex items-center gap-2">
                        <button onclick="window.print()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 transition border border-slate-700 cursor-pointer">
                            <i class="fa-solid fa-print text-amber-400"></i> پرنٹ / PDF ڈاؤن لوڈ
                        </button>

                        <a href="https://wa.me/?text={{ urlencode('اسلامی شرعی وراثت تقسیم نامہ: کل ترکہ ' . $net_marlas . ' مرلہ اراضی و نقدی۔ آسان زمین پورٹل پر چیک کریں: ' . url()->current()) }}" 
                           target="_blank" 
                           class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 transition cursor-pointer">
                            <i class="fa-brands fa-whatsapp text-sm"></i> WhatsApp شیئر
                        </a>
                    </div>
                </div>

                <!-- Main Printable Slip -->
                <div id="printable-inheritance-slip" class="bg-slate-900/90 rounded-3xl border border-slate-800 p-6 sm:p-8 shadow-2xl space-y-6 w-full">

                    <!-- Sheet Header -->
                    <div class="border-b border-slate-800 pb-4 flex items-center justify-between w-full">
                        <div>
                            <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest block">Official Shariat Succession Statement</span>
                            <h2 class="text-xl font-black text-white">انتقالِ وراثت و شرعی تقسیم نامہ</h2>
                            <p class="text-xs text-slate-400 mt-0.5">
                                متوفیٰ: <strong class="text-white">{{ $deceased_gender === 'male' ? 'مرد (مرحوم)' : 'عورت (مرحومہ)' }}</strong> &bull; کل اراضی: {{ $net_marlas }} مرلہ &bull; خالص نقدی: PKR {{ number_format($net_cash) }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold text-amber-400 bg-amber-500/10 border border-amber-500/20 px-2.5 py-1 rounded-lg">Quranic Shares</span>
                        </div>
                    </div>

                    <!-- Itemized Heirs Table -->
                    <div class="overflow-x-auto w-full">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="border-b border-slate-800 text-slate-400 uppercase font-bold text-[10px]">
                                    <th class="py-2.5 px-3">وارث کا رشتہ</th>
                                    <th class="py-2.5 px-3">قرآنی حصہ</th>
                                    <th class="py-2.5 px-3">فیصد (%)</th>
                                    <th class="py-2.5 px-3 text-right">اراضی حصہ (کنال - مرلہ)</th>
                                    <th class="py-2.5 px-3 text-right">نقدی حصہ (PKR)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/60 text-slate-200 font-medium">
                                @forelse($heirs_shares as $share)
                                    <tr class="hover:bg-slate-800/30">
                                        <td class="py-3 px-3 font-bold text-white flex items-center gap-1.5">
                                            <i class="fa-solid fa-user text-[11px] text-amber-400"></i>
                                            <span>{{ $share['relation'] }}</span>
                                        </td>
                                        <td class="py-3 px-3 font-mono font-bold text-emerald-400">{{ $share['fraction'] }}</td>
                                        <td class="py-3 px-3 font-mono text-slate-300">{{ $share['percentage'] }}%</td>
                                        <td class="py-3 px-3 text-right font-mono font-bold text-amber-400">{{ $share['formatted_land'] }}</td>
                                        <td class="py-3 px-3 text-right font-mono font-bold text-white">{{ number_format($share['cash_amount']) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-6 text-center text-slate-500">کوئی وارث منتخب نہیں کیا گیا</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary Rule Notice -->
                    <div class="p-3.5 bg-slate-950 rounded-2xl border border-slate-800 text-xs text-slate-400 space-y-1">
                        <span class="font-bold text-white flex items-center gap-1.5">
                            <i class="fa-solid fa-book-quran text-amber-400"></i> شرعی اصول و ضوابط:
                        </span>
                        <p class="text-[11px] leading-relaxed">
                            مرحوم کے ترکے میں سے سب سے پہلے قرض اور تجہیز و تکفین کے اخراجات ادا کیے گئے۔ اس کے بعد اصحاب الفروض کے مقررہ حصے نکال کر باقی ماندہ اراضی و مال بیٹوں اور بیٹیوں میں (للذکر مثل حظ الانثیین - بیٹے کو دوگنا، بیٹی کو اکہرا) تقسیم کیا گیا ہے۔
                        </p>
                    </div>

                    <!-- Certified Footer -->
                    <div class="pt-4 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between text-[11px] text-slate-500 gap-2">
                        <span>نوٹ: یہ گوشوارہ پٹوار و ریونیو کورٹ میں انتقالِ وراثت درج کروانے کے لیے بطورِ گائیڈ کارآمد ہے۔</span>
                        <span class="font-mono">AssanZameen Shariat Engine</span>
                    </div>

                </div>

            </div>

        </div>

    </div>
</div>