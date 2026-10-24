<div class="min-h-screen bg-[#070a13] text-slate-100 py-10 px-4 sm:px-6 lg:px-8 w-full overflow-x-hidden"  style="font-family: 'Noto Nastaliq Urdu', 'Noto Sans Arabic', serif; line-height: 1.8;">

    <!-- Print & Flexbox Layout Styling -->
    <style>
        .converter-wrapper {
            display: flex;
            flex-direction: column;
            gap: 2rem;
            width: 100%;
        }
        @media (min-width: 1024px) {
            .converter-wrapper {
                flex-direction: row;
                align-items: flex-start;
            }
            .converter-form {
                width: 48% !important;
                flex-shrink: 0;
            }
            .converter-output {
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

            #printable-land-report {
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

            #printable-land-report .bg-slate-900,
            #printable-land-report .bg-slate-950,
            #printable-land-report .bg-amber-500\/10,
            #printable-land-report .bg-emerald-500\/10 {
                background-color: #f8fafc !important;
                border: 1px solid #cbd5e1 !important;
                color: #000000 !important;
            }

            #printable-land-report h1, 
            #printable-land-report h2, 
            #printable-land-report h3, 
            #printable-land-report span, 
            #printable-land-report p, 
            #printable-land-report td {
                color: #000000 !important;
            }

            #printable-land-report .font-mono {
                color: #000000 !important;
                font-weight: 800 !important;
            }

            @page {
                size: A4 portrait;
                margin: 12mm;
            }
        }
    </style>

    <div class="max-w-7xl mx-auto space-y-8 w-full">

        <!-- Tool Header Banner (No-Print) -->
        <div class="text-center space-y-3 no-print w-full">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-bold uppercase tracking-wider">
                <i class="fa-solid fa-ruler-combined text-sm"></i>
                <span>Patwari Land Cadastral & Irregular Area Engine</span>
            </div>
            
            <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight" style="font-family: 'Noto Nastaliq Urdu', 'Noto Sans Arabic', serif; line-height: 1.8;">
                پٹواری اراضی کنورٹر و ٹیڑھے پلاٹ کا رقبہ
            </h1>
            
            <p class="text-sm text-slate-400 max-w-2xl mx-auto leading-relaxed">
                4 مختلف اضلاع کے ٹیڑھے پلاٹ کا دقیق مرلہ اور لائیو خاکہ نکالیں یا کرم، سرساہی، گز، مرلہ، کنال اور ایکڑ کی دو طرفہ پیمائش کو تبدیل کریں۔
            </p>
        </div>

        <!-- Mode Toggle & Standard Selection (No-Print) -->
        <div class="bg-slate-900 p-3.5 rounded-2xl border border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4 no-print w-full">
            
            <!-- Tab Selector -->
            <div class="flex items-center bg-slate-950 p-1.5 rounded-xl border border-slate-800 w-full sm:w-auto">
                <button type="button" wire:click="$set('activeTab', 'irregular')" 
                        class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 cursor-pointer w-1/2 sm:w-auto justify-center {{ $activeTab === 'irregular' ? 'bg-amber-500 text-slate-950 font-black shadow-md' : 'text-slate-400 hover:text-white' }}">
                    <i class="fa-solid fa-draw-polygon"></i>
                    <span>ٹیڑھا پلاٹ (4 Sides)</span>
                </button>
                <button type="button" wire:click="$set('activeTab', 'converter')" 
                        class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 cursor-pointer w-1/2 sm:w-auto justify-center {{ $activeTab === 'converter' ? 'bg-amber-500 text-slate-950 font-black shadow-md' : 'text-slate-400 hover:text-white' }}">
                    <i class="fa-solid fa-arrows-rotate"></i>
                    <span>اکائی کنورٹر (Units)</span>
                </button>
            </div>

            <!-- Standard Selector (225 vs 272.25) -->
            <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                <span class="text-xs font-bold text-slate-400">مرلہ پیمائش:</span>
                <div class="flex items-center bg-slate-950 p-1 rounded-xl border border-slate-800">
                    <button type="button" wire:click="$set('standard', 'society')" 
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer {{ $standard === 'society' ? 'bg-emerald-500 text-slate-950' : 'text-slate-400 hover:text-white' }}">
                        سوسائٹی (225 Sq Ft)
                    </button>
                    <button type="button" wire:click="$set('standard', 'revenue')" 
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer {{ $standard === 'revenue' ? 'bg-emerald-500 text-slate-950' : 'text-slate-400 hover:text-white' }}">
                        پٹواری / سرکاری (272.25 Sq Ft)
                    </button>
                </div>
            </div>

        </div>

        <!-- ============================================== -->
        <!-- MAIN CALCULATOR LAYOUT WRAPPER (FLEXBOX) -->
        <!-- ============================================== -->
        <div class="converter-wrapper w-full">

            <!-- LEFT COLUMN: INPUT WIZARDS -->
            <div class="converter-form w-full bg-slate-900/90 rounded-3xl border border-slate-800 p-6 sm:p-7 shadow-2xl space-y-6 no-print">

                @if($activeTab === 'irregular')
                    <!-- 4-SIDED PLOT INPUTS -->
                    <div class="space-y-4 w-full">
                        <div class="border-b border-slate-800 pb-3 flex items-center justify-between">
                            <h3 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2">
                                <i class="fa-solid fa-compass-drafting text-amber-400"></i>
                                <span>چاروں اطراف کی لمبائی فٹ میں درج کریں</span>
                            </h3>
                            <span class="text-[11px] text-slate-400 font-mono">1 Marla = {{ $marlaSqFt }} Sq Ft</span>
                        </div>

                        <!-- 4 Sides Grid -->
                        <div class="grid grid-cols-2 gap-3 w-full">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">شمال / اوپر کی لمبائی (فٹ)</label>
                                <input type="number" step="any" wire:model.live.debounce.300ms="side_north" placeholder="e.g. 50"
                                       class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-white font-mono font-bold focus:border-amber-500 outline-none">
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">جنوب / نیچے کی لمبائی (فٹ)</label>
                                <input type="number" step="any" wire:model.live.debounce.300ms="side_south" placeholder="e.g. 45"
                                       class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-white font-mono font-bold focus:border-amber-500 outline-none">
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">مشرق / دائیں چوڑائی (فٹ)</label>
                                <input type="number" step="any" wire:model.live.debounce.300ms="side_east" placeholder="e.g. 30"
                                       class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-white font-mono font-bold focus:border-amber-500 outline-none">
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">مغرب / بائیں چوڑائی (فٹ)</label>
                                <input type="number" step="any" wire:model.live.debounce.300ms="side_west" placeholder="e.g. 35"
                                       class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-white font-mono font-bold focus:border-amber-500 outline-none">
                            </div>
                        </div>

                        <!-- Optional Diagonal (Kona / Watar) -->
                        <div class="p-3.5 bg-slate-950 rounded-2xl border border-slate-800 space-y-1.5 w-full">
                            <label class="block text-xs font-bold text-amber-400 flex items-center justify-between">
                                <span>ترچھا وتر / ڈائگنل فاصلہ (Diagonal - اختیاری)</span>
                                <span class="text-[10px] text-slate-500 font-normal">100% ہیرون فارمولا</span>
                            </label>
                            <input type="number" step="any" wire:model.live.debounce.300ms="diagonal" placeholder="e.g. 60 (ایک کونے سے مخالف کونے کا فاصلہ)"
                                   class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white font-mono font-bold focus:border-amber-500 outline-none">
                            <span class="text-[10px] text-slate-500 block">اگر وتر معلوم نہ ہو تو اوسط فارمولے سے تخمینہ ہو گا، وتر درج کرنے سے رقبہ سو فیصد درست آئے گا۔</span>
                        </div>

                        <!-- Visual SVG Polygon Plot Canvas (Live Coordinates Based) -->
                        <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 text-center space-y-2 w-full">
                            <div class="flex items-center justify-between px-1">
                                <span class="text-[11px] font-bold text-amber-400 uppercase tracking-wider flex items-center gap-1.5">
                                    <i class="fa-solid fa-draw-polygon"></i> لائیو پلاٹ خاکہ (Dynamic Live Geometry)
                                </span>
                                <span class="text-[10px] text-slate-500 font-mono">Proportionate Map</span>
                            </div>

                            <div class="w-full h-52 bg-slate-900/60 rounded-xl border border-slate-800/80 flex items-center justify-center p-2 relative overflow-hidden">
                                <svg viewBox="0 0 320 200" class="w-full h-full max-w-[340px] drop-shadow-xl select-none transition-all duration-300">
                                    <defs>
                                        <pattern id="plotGrid" width="20" height="20" patternUnits="userSpaceOnUse">
                                            <path d="M 20 0 L 0 0 0 20" fill="none" stroke="rgba(51, 65, 85, 0.2)" stroke-width="0.5" />
                                        </pattern>
                                    </defs>
                                    <rect width="100%" height="100%" fill="url(#plotGrid)" />

                                    <!-- Dynamic Polygon Drawn from Real Trigonometric Coordinates -->
                                    <polygon points="{{ $svgCoordinates['polygon'] }}" 
                                             fill="rgba(245, 158, 11, 0.12)" 
                                             stroke="#f59e0b" 
                                             stroke-width="2.5" 
                                             stroke-linejoin="round"
                                             class="transition-all duration-300" />

                                    <!-- Optional Diagonal Line -->
                                    @if($plotResult['is_diagonal_used'])
                                        <line x1="{{ $svgCoordinates['p1']['x'] }}" y1="{{ $svgCoordinates['p1']['y'] }}" 
                                              x2="{{ $svgCoordinates['p3']['x'] }}" y2="{{ $svgCoordinates['p3']['y'] }}" 
                                              stroke="#ef4444" stroke-width="1.8" stroke-dasharray="4,4" />
                                        
                                        <text x="{{ ($svgCoordinates['p1']['x'] + $svgCoordinates['p3']['x']) / 2 }}" 
                                              y="{{ ($svgCoordinates['p1']['y'] + $svgCoordinates['p3']['y']) / 2 - 5 }}" 
                                              fill="#ef4444" font-size="10" font-weight="bold" text-anchor="middle" font-family="monospace">
                                            وتر: {{ $diagonal }}'
                                        </text>
                                    @endif

                                    <!-- 4 Corner Dots -->
                                    <circle cx="{{ $svgCoordinates['p1']['x'] }}" cy="{{ $svgCoordinates['p1']['y'] }}" r="4" fill="#10b981" />
                                    <circle cx="{{ $svgCoordinates['p2']['x'] }}" cy="{{ $svgCoordinates['p2']['y'] }}" r="4" fill="#10b981" />
                                    <circle cx="{{ $svgCoordinates['p3']['x'] }}" cy="{{ $svgCoordinates['p3']['y'] }}" r="4" fill="#10b981" />
                                    <circle cx="{{ $svgCoordinates['p4']['x'] }}" cy="{{ $svgCoordinates['p4']['y'] }}" r="4" fill="#10b981" />

                                    <!-- Dynamic Live Dimension Labels -->
                                    <text x="{{ $svgCoordinates['northLabel']['x'] }}" y="{{ $svgCoordinates['northLabel']['y'] }}" 
                                          fill="#ffffff" font-size="11" font-weight="bold" text-anchor="middle" font-family="monospace">
                                        شمال: {{ $side_north }}'
                                    </text>

                                    <text x="{{ $svgCoordinates['southLabel']['x'] }}" y="{{ $svgCoordinates['southLabel']['y'] }}" 
                                          fill="#ffffff" font-size="11" font-weight="bold" text-anchor="middle" font-family="monospace">
                                        جنوب: {{ $side_south }}'
                                    </text>

                                    <text x="{{ $svgCoordinates['eastLabel']['x'] }}" y="{{ $svgCoordinates['eastLabel']['y'] }}" 
                                          fill="#ffffff" font-size="11" font-weight="bold" text-anchor="start" font-family="monospace">
                                        مشرق: {{ $side_east }}'
                                    </text>

                                    <text x="{{ $svgCoordinates['westLabel']['x'] }}" y="{{ $svgCoordinates['westLabel']['y'] }}" 
                                          fill="#ffffff" font-size="11" font-weight="bold" text-anchor="end" font-family="monospace">
                                        مغرب: {{ $side_west }}'
                                    </text>
                                </svg>
                            </div>
                        </div>

                    </div>
                @else
                    <!-- UNIT CONVERTER INPUTS -->
                    <div class="space-y-4 w-full">
                        <div class="border-b border-slate-800 pb-3 flex items-center justify-between">
                            <h3 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2">
                                <i class="fa-solid fa-arrows-rotate text-emerald-400"></i>
                                <span>مقدار اور اکائی درج کریں</span>
                            </h3>
                            <span class="text-[11px] text-slate-400 font-mono">1 Marla = {{ $marlaSqFt }} Sq Ft</span>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase mb-1.5">مقدار (Value to Convert)</label>
                            <input type="number" step="any" wire:model.live.debounce.300ms="input_value" 
                                   class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 text-sm text-amber-400 font-mono font-bold focus:border-amber-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase mb-1.5">موجودہ اکائی (From Unit)</label>
                            <select wire:model.live="from_unit" class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 text-xs text-white focus:border-amber-500 outline-none font-bold">
                                <option value="marla">مرلہ (Marla)</option>
                                <option value="kanal">کنال (Kanal)</option>
                                <option value="sqft">مربع فٹ (Square Feet)</option>
                                <option value="sqyards">مربع گز (Square Yards / Gaz)</option>
                                <option value="sarsahi">سرساہی (Sarsahi)</option>
                                <option value="karam">مربع کرم (Square Karam)</option>
                                <option value="acre">ایکڑ / قلعہ (Acre / Killa)</option>
                                <option value="murabba">مربع اراضی (Murabba - 25 Acres)</option>
                            </select>
                        </div>

                        <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-2 text-xs text-slate-400">
                            <span class="font-bold text-white block">پٹواری اراضی پیمائش کا مروجہ نظام:</span>
                            <ul class="list-disc list-inside space-y-1 text-[11px]">
                                <li>1 مرلہ = 9 سرساہی</li>
                                <li>1 کنال = 20 مرلے</li>
                                <li>1 ایکڑ (قلعہ) = 8 کنال (160 مرلے)</li>
                                <li>1 مربع = 25 ایکڑ (200 کنال)</li>
                                <li>1 کرم (سرکاری) = 5.5 فٹ (66 انچ)</li>
                            </ul>
                        </div>
                    </div>
                @endif

            </div>

            <!-- RIGHT COLUMN: OFFICIAL RESULTS REPORT -->
            <div class="converter-output w-full space-y-5">
                
                <!-- Quick Print / Share Actions -->
                <div id="action-bar" class="no-print flex items-center justify-between gap-3 p-4 bg-slate-900 rounded-2xl border border-slate-800 w-full">
                    <span class="text-xs font-bold text-slate-400">پیمائش سمری و تصدیق:</span>
                    <div class="flex items-center gap-2">
                        <button onclick="window.print()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 transition border border-slate-700 cursor-pointer">
                            <i class="fa-solid fa-print text-amber-400"></i> پرنٹ / PDF
                        </button>

                        <a href="https://wa.me/?text={{ urlencode('اراضی پیمائش و پلاٹ رقبہ رپورٹ: ' . ($activeTab === 'irregular' ? $plotResult['patwari_formatted'] . ' (' . $plotResult['marlas'] . ' مرلہ)' : $convertedUnits['patwari_formatted']) . '۔ آسان زمین ٹول پر تصدیق کریں: ' . url()->current()) }}" 
                           target="_blank" 
                           class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 transition cursor-pointer">
                            <i class="fa-brands fa-whatsapp text-sm"></i> WhatsApp
                        </a>
                    </div>
                </div>

                <!-- Printable Report Sheet -->
                <div id="printable-land-report" class="bg-slate-900/90 rounded-3xl border border-slate-800 p-6 sm:p-8 shadow-2xl space-y-6 w-full">

                    <!-- Sheet Header -->
                    <div class="border-b border-slate-800 pb-4 flex items-center justify-between w-full">
                        <div>
                            <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest block">Official Measurement Certificate</span>
                            <h2 class="text-xl font-black text-white">پٹواری اراضی پیمائش و رقبہ شیڈول</h2>
                            <p class="text-xs text-slate-400 mt-0.5">
                                اسٹینڈرڈ: <strong class="text-white">{{ $standard === 'society' ? 'ہاؤسنگ سوسائٹیز (225 فٹ فی مرلہ)' : 'سرکاری ریونیو پٹواری (272.25 فٹ فی مرلہ)' }}</strong>
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold text-amber-400 bg-amber-500/10 border border-amber-500/20 px-2.5 py-1 rounded-lg">Verified Math</span>
                        </div>
                    </div>

                    @if($activeTab === 'irregular')
                        <!-- IRREGULAR PLOT REPORT OUTPUT -->
                        <div class="p-5 bg-gradient-to-r from-emerald-500/10 via-slate-950 to-slate-950 rounded-2xl border border-emerald-500/30 flex items-center justify-between w-full">
                            <div>
                                <span class="text-xs font-bold text-slate-400 block">پٹواری مساحت فارمیٹ (کنال - مرلہ - سرساہی):</span>
                                <span class="text-2xl sm:text-3xl font-black text-emerald-400 font-mono mt-1 block">
                                    {{ $plotResult['patwari_formatted'] }}
                                </span>
                            </div>
                            <i class="fa-solid fa-shapes text-3xl text-emerald-400/40"></i>
                        </div>

                        <!-- Plot Dimensions Breakdown Grid -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 w-full">
                            <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800">
                                <span class="text-[10px] text-slate-400 uppercase font-bold block">کل رقبہ مرلہ</span>
                                <span class="text-base font-black text-white font-mono mt-0.5 block">{{ $plotResult['marlas'] }}</span>
                            </div>

                            <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800">
                                <span class="text-[10px] text-slate-400 uppercase font-bold block">کل مربع فٹ (Sq Ft)</span>
                                <span class="text-base font-black text-amber-400 font-mono mt-0.5 block">{{ number_format($plotResult['sqft']) }}</span>
                            </div>

                            <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800">
                                <span class="text-[10px] text-slate-400 uppercase font-bold block">مربع گز (Square Yards)</span>
                                <span class="text-base font-black text-sky-400 font-mono mt-0.5 block">{{ number_format($plotResult['sqyards']) }}</span>
                            </div>

                            <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800">
                                <span class="text-[10px] text-slate-400 uppercase font-bold block">کنال شیئر</span>
                                <span class="text-base font-black text-indigo-400 font-mono mt-0.5 block">{{ $plotResult['kanals'] }}</span>
                            </div>
                        </div>

                        <!-- Method Notice -->
                        <div class="p-3.5 rounded-xl border text-xs {{ $plotResult['is_diagonal_used'] ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-300' : 'bg-amber-500/10 border-amber-500/30 text-amber-300' }}">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid {{ $plotResult['is_diagonal_used'] ? 'fa-circle-check text-emerald-400' : 'fa-triangle-exclamation text-amber-400' }}"></i>
                                <span class="font-bold">
                                    {{ $plotResult['is_diagonal_used'] ? 'ہیرون کا تثلیثی فارمولا (100% دقیق و مستند رقبہ)' : 'اوسط فارمولا (Average Length x Average Width)' }}
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">
                                {{ $plotResult['is_diagonal_used'] ? 'آپ نے ترچھا وتر درج کیا ہے جس سے چاروں اضلاع کے زاویوں کا درست رقبہ نکالا گیا ہے۔' : 'پلاٹ اگر زیادہ ترچھا ہو تو وتر درج کرنے سے رقبے میں چند انچ کا بھی فرق نہیں رہتا۔' }}
                            </p>
                        </div>

                    @else
                        <!-- UNIT CONVERTER REPORT OUTPUT -->
                        <div class="p-5 bg-gradient-to-r from-amber-500/10 via-slate-950 to-slate-950 rounded-2xl border border-amber-500/30 flex items-center justify-between w-full">
                            <div>
                                <span class="text-xs font-bold text-slate-400 block">{{ $input_value }} {{ ucfirst($from_unit) }} کی پٹواری تقسیم:</span>
                                <span class="text-2xl sm:text-3xl font-black text-amber-400 font-mono mt-1 block">
                                    {{ $convertedUnits['patwari_formatted'] }}
                                </span>
                            </div>
                            <i class="fa-solid fa-calculator text-3xl text-amber-400/40"></i>
                        </div>

                        <!-- All Equivalent Units Table -->
                        <div class="overflow-x-auto w-full">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="border-b border-slate-800 text-slate-400 uppercase font-bold text-[10px]">
                                        <th class="py-2.5 px-3">پیمائش کی اکائی</th>
                                        <th class="py-2.5 px-3">مساوی مقدار</th>
                                        <th class="py-2.5 px-3 text-right">فارمولا معیار</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/60 text-slate-200 font-medium">
                                    <tr>
                                        <td class="py-2.5 px-3 font-bold text-white">مرلہ (Marla)</td>
                                        <td class="py-2.5 px-3 font-mono font-bold text-emerald-400 text-sm">{{ $convertedUnits['marla'] }}</td>
                                        <td class="py-2.5 px-3 text-right text-slate-400 font-mono">{{ $marlaSqFt }} Sq Ft</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2.5 px-3 font-bold text-white">کنال (Kanal)</td>
                                        <td class="py-2.5 px-3 font-mono font-bold text-white">{{ $convertedUnits['kanal'] }}</td>
                                        <td class="py-2.5 px-3 text-right text-slate-400 font-mono">20 Marla</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2.5 px-3 font-bold text-white">مربع فٹ (Square Feet)</td>
                                        <td class="py-2.5 px-3 font-mono font-bold text-amber-400">{{ number_format($convertedUnits['sqft']) }}</td>
                                        <td class="py-2.5 px-3 text-right text-slate-400 font-mono">Sq Ft</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2.5 px-3 font-bold text-white">مربع گز (Square Yards / Gaz)</td>
                                        <td class="py-2.5 px-3 font-mono font-bold text-white">{{ number_format($convertedUnits['sqyards']) }}</td>
                                        <td class="py-2.5 px-3 text-right text-slate-400 font-mono">9 Sq Ft</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2.5 px-3 font-bold text-white">سرساہی (Sarsahi)</td>
                                        <td class="py-2.5 px-3 font-mono font-bold text-sky-400">{{ $convertedUnits['sarsahi'] }}</td>
                                        <td class="py-2.5 px-3 text-right text-slate-400 font-mono">1/9 Marla</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2.5 px-3 font-bold text-white">مربع کرم (Square Karam)</td>
                                        <td class="py-2.5 px-3 font-mono font-bold text-white">{{ $convertedUnits['karam'] }}</td>
                                        <td class="py-2.5 px-3 text-right text-slate-400 font-mono">{{ $standard === 'society' ? "5.0' x 5.0'" : "5.5' x 5.5'" }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2.5 px-3 font-bold text-white">ایکڑ / قلعہ (Acre / Killa)</td>
                                        <td class="py-2.5 px-3 font-mono font-bold text-white">{{ $convertedUnits['acre'] }}</td>
                                        <td class="py-2.5 px-3 text-right text-slate-400 font-mono">8 Kanals</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <!-- Certified Footer Notes -->
                    <div class="pt-4 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between text-[11px] text-slate-500 gap-2">
                        <span>نوٹ: یہ پیمائش پنجاب لینڈ ریونیو ایکٹ اور منظور شدہ جیومیٹرک اصولوں کے عین مطابق ہے۔</span>
                        <span class="font-mono">AssanZameen Cadastral Engine</span>
                    </div>

                </div>

            </div>

        </div>

    </div>
</div>