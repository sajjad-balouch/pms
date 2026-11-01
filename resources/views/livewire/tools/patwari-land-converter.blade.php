<div class="min-h-screen bg-[#070a13] text-slate-100 py-10 px-4 sm:px-6 lg:px-8 w-full overflow-x-hidden">

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
                <span>{{ __('Marla, kanal, acre and more') }}</span>
            </div>
            
            <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight">
                {{ __('Land Unit Converter & Plot Area') }}
            </h1>
            
            <p class="text-sm text-slate-400 max-w-2xl mx-auto leading-relaxed">
                {{ __('Convert marla, kanal, acre, karam and sarsahi, and measure the area of an irregular plot.') }}
            </p>
        </div>

        <!-- Mode Toggle & Standard Selection (No-Print) -->
        <div class="bg-slate-900 p-3.5 rounded-2xl border border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4 no-print w-full">
            
            <!-- Tab Selector -->
            <div class="flex items-center bg-slate-950 p-1.5 rounded-xl border border-slate-800 w-full sm:w-auto">
                <button type="button" wire:click="$set('activeTab', 'irregular')" 
                        class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 cursor-pointer w-1/2 sm:w-auto justify-center {{ $activeTab === 'irregular' ? 'bg-amber-500 text-slate-950 font-black shadow-md' : 'text-slate-400 hover:text-white' }}">
                    <i class="fa-solid fa-draw-polygon"></i>
                    <span>{{ __('Irregular plot') }}</span>
                </button>
                <button type="button" wire:click="$set('activeTab', 'converter')" 
                        class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 cursor-pointer w-1/2 sm:w-auto justify-center {{ $activeTab === 'converter' ? 'bg-amber-500 text-slate-950 font-black shadow-md' : 'text-slate-400 hover:text-white' }}">
                    <i class="fa-solid fa-arrows-rotate"></i>
                    <span>{{ __('Unit converter') }}</span>
                </button>
            </div>

            <!-- Standard Selector (225 vs 272.25) -->
            <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                <span class="text-xs font-bold text-slate-400">{{ __('Marla size:') }}</span>
                <div class="flex items-center bg-slate-950 p-1 rounded-xl border border-slate-800">
                    <button type="button" wire:click="$set('standard', 'society')" 
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer {{ $standard === 'society' ? 'bg-emerald-500 text-slate-950' : 'text-slate-400 hover:text-white' }}">
                        {{ __('Society (225 sq ft)') }}
                    </button>
                    <button type="button" wire:click="$set('standard', 'revenue')" 
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer {{ $standard === 'revenue' ? 'bg-emerald-500 text-slate-950' : 'text-slate-400 hover:text-white' }}">
                        {{ __('Revenue (272.25 sq ft)') }}
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
                                <span>{{ __('Enter all four sides in feet') }}</span>
                            </h3>
                            <span class="text-[11px] text-slate-400 font-mono">1 Marla = {{ $marlaSqFt }} Sq Ft</span>
                        </div>

                        <!-- 4 Sides Grid -->
                        <div class="grid grid-cols-2 gap-3 w-full">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">{{ __('North side (ft)') }}</label>
                                <input type="number" step="any" wire:model.live.debounce.300ms="side_north" placeholder="e.g. 50"
                                       class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-white font-mono font-bold focus:border-amber-500 outline-none">
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">{{ __('South side (ft)') }}</label>
                                <input type="number" step="any" wire:model.live.debounce.300ms="side_south" placeholder="e.g. 45"
                                       class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-white font-mono font-bold focus:border-amber-500 outline-none">
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">{{ __('East side (ft)') }}</label>
                                <input type="number" step="any" wire:model.live.debounce.300ms="side_east" placeholder="e.g. 30"
                                       class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-white font-mono font-bold focus:border-amber-500 outline-none">
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">{{ __('West side (ft)') }}</label>
                                <input type="number" step="any" wire:model.live.debounce.300ms="side_west" placeholder="e.g. 35"
                                       class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-white font-mono font-bold focus:border-amber-500 outline-none">
                            </div>
                        </div>

                        <!-- Optional Diagonal (Kona / Watar) -->
                        <div class="p-3.5 bg-slate-950 rounded-2xl border border-slate-800 space-y-1.5 w-full">
                            <label class="block text-xs font-bold text-amber-400 flex items-center justify-between">
                                <span>{{ __('Diagonal (optional)') }}</span>
                                <span class="text-[10px] text-slate-500 font-normal">{{ __('More accurate') }}</span>
                            </label>
                            <input type="number" step="any" wire:model.live.debounce.300ms="diagonal" placeholder="{{ __('North-west corner to south-east corner') }}"
                                   class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white font-mono font-bold focus:border-amber-500 outline-none">
                            <span class="text-[10px] text-slate-500 block">{{ __('Without the diagonal, the area is an average-based estimate. Adding it gives a much more accurate result.') }}</span>
                        </div>

                        <!-- Visual SVG Polygon Plot Canvas (Live Coordinates Based) -->
                        <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 text-center space-y-2 w-full">
                            <div class="flex items-center justify-between px-1">
                                <span class="text-[11px] font-bold text-amber-400 uppercase tracking-wider flex items-center gap-1.5">
                                    <i class="fa-solid fa-draw-polygon"></i> {{ __('Plot sketch') }}
                                </span>
                                <span class="text-[10px] text-slate-500 font-mono">{{ __('Sketch only') }}</span>
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
                                            {{ __('Diagonal:') }} {{ $diagonal }}'
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
                                        {{ __('N:') }} {{ $side_north }}'
                                    </text>

                                    <text x="{{ $svgCoordinates['southLabel']['x'] }}" y="{{ $svgCoordinates['southLabel']['y'] }}" 
                                          fill="#ffffff" font-size="11" font-weight="bold" text-anchor="middle" font-family="monospace">
                                        {{ __('S:') }} {{ $side_south }}'
                                    </text>

                                    <text x="{{ $svgCoordinates['eastLabel']['x'] }}" y="{{ $svgCoordinates['eastLabel']['y'] }}" 
                                          fill="#ffffff" font-size="11" font-weight="bold" text-anchor="start" font-family="monospace">
                                        {{ __('E:') }} {{ $side_east }}'
                                    </text>

                                    <text x="{{ $svgCoordinates['westLabel']['x'] }}" y="{{ $svgCoordinates['westLabel']['y'] }}" 
                                          fill="#ffffff" font-size="11" font-weight="bold" text-anchor="end" font-family="monospace">
                                        {{ __('W:') }} {{ $side_west }}'
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
                                <span>{{ __('Enter a value and unit') }}</span>
                            </h3>
                            <span class="text-[11px] text-slate-400 font-mono">1 Marla = {{ $marlaSqFt }} Sq Ft</span>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase mb-1.5">{{ __('Value') }}</label>
                            <input type="number" step="any" wire:model.live.debounce.300ms="input_value" 
                                   class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 text-sm text-amber-400 font-mono font-bold focus:border-amber-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase mb-1.5">{{ __('From unit') }}</label>
                            <select wire:model.live="from_unit" class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 text-xs text-white focus:border-amber-500 outline-none font-bold">
                                <option value="marla">{{ __('Marla') }}</option>
                                <option value="kanal">{{ __('Kanal') }}</option>
                                <option value="sqft">{{ __('Square feet') }}</option>
                                <option value="sqyards">{{ __('Square yards') }}</option>
                                <option value="sarsahi">{{ __('Sarsahi') }}</option>
                                <option value="karam">{{ __('Square karam') }}</option>
                                <option value="sqm">{{ __('Square meters') }}</option>
                                    <option value="acre">{{ __('Acre') }}</option>
                                <option value="murabba">{{ __('Murabba (25 acres)') }}</option>
                            </select>
                        </div>

                        <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-2 text-xs text-slate-400">
                            <span class="font-bold text-white block">{{ __('Revenue measurement system:') }}</span>
                            <ul class="list-disc list-inside space-y-1 text-[11px]">
                                <li>{{ __('1 marla = 9 sarsahi') }}</li>
                                <li>{{ __('1 kanal = 20 marla') }}</li>
                                <li>{{ __('1 acre = 8 kanal (160 revenue marla)') }}</li>
                                <li>{{ __('1 murabba = 25 acres (200 revenue kanal)') }}</li>
                                <li>{{ __('1 revenue karam = 5.5 ft (66 in)') }}</li>
                            </ul>
                        </div>
                    </div>
                @endif

            </div>

            <!-- RIGHT COLUMN: OFFICIAL RESULTS REPORT -->
            <div class="converter-output w-full space-y-5">
                
                <!-- Quick Print / Share Actions -->
                <div id="action-bar" class="no-print flex items-center justify-between gap-3 p-4 bg-slate-900 rounded-2xl border border-slate-800 w-full">
                    <span class="text-xs font-bold text-slate-400">{{ __('Measurement summary:') }}</span>
                    <div class="flex items-center gap-2">
                        <button onclick="window.print()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 transition border border-slate-700 cursor-pointer">
                            <i class="fa-solid fa-print text-amber-400"></i> {{ __('Print / PDF') }}
                        </button>

                        <a href="https://wa.me/?text={{ urlencode(__('Land measurement:') . ' ' . ($activeTab === 'irregular' ? $plotResult['patwari_formatted'] : $convertedUnits['patwari_formatted']) . ' - ' . url()->current()) }}" 
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
                            <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest block">{{ __('Estimate only') }}</span>
                            <h2 class="text-xl font-black text-white">{{ __('Land measurement report') }}</h2>
                            <p class="text-xs text-slate-400 mt-0.5">
                                {{ __('Standard:') }} <strong class="text-white">{{ $standard === 'society' ? __('Society (225 sq ft)') : __('Revenue (272.25 sq ft)') }}</strong>
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold text-amber-400 bg-amber-500/10 border border-amber-500/20 px-2.5 py-1 rounded-lg">{{ __('Calculated') }}</span>
                        </div>
                    </div>

                    @if($activeTab === 'irregular')
                        <!-- IRREGULAR PLOT REPORT OUTPUT -->
                        <div class="p-5 bg-gradient-to-r from-emerald-500/10 via-slate-950 to-slate-950 rounded-2xl border border-emerald-500/30 flex items-center justify-between w-full">
                            <div>
                                <span class="text-xs font-bold text-slate-400 block">{{ __('Kanal - marla - sarsahi:') }}</span>
                                <span class="text-2xl sm:text-3xl font-black text-emerald-400 font-mono mt-1 block">
                                    {{ $plotResult['patwari_formatted'] }}
                                </span>
                            </div>
                            <i class="fa-solid fa-shapes text-3xl text-emerald-400/40"></i>
                        </div>

                        <!-- Plot Dimensions Breakdown Grid -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 w-full">
                            <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800">
                                <span class="text-[10px] text-slate-400 uppercase font-bold block">{{ __('Total marla') }}</span>
                                <span class="text-base font-black text-white font-mono mt-0.5 block">{{ $plotResult['marlas'] }}</span>
                            </div>

                            <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800">
                                <span class="text-[10px] text-slate-400 uppercase font-bold block">{{ __('Total sq ft') }}</span>
                                <span class="text-base font-black text-amber-400 font-mono mt-0.5 block">{{ number_format($plotResult['sqft']) }}</span>
                            </div>

                            <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800">
                                <span class="text-[10px] text-slate-400 uppercase font-bold block">{{ __('Square yards') }}</span>
                                <span class="text-base font-black text-sky-400 font-mono mt-0.5 block">{{ number_format($plotResult['sqyards']) }}</span>
                            </div>

                            <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800">
                                <span class="text-[10px] text-slate-400 uppercase font-bold block">{{ __('Kanal') }}</span>
                                <span class="text-base font-black text-indigo-400 font-mono mt-0.5 block">{{ $plotResult['kanals'] }}</span>
                            </div>
                        </div>

                        <!-- Method Notice -->
                        <div class="p-3.5 rounded-xl border text-xs {{ $plotResult['is_diagonal_used'] ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-300' : 'bg-amber-500/10 border-amber-500/30 text-amber-300' }}">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid {{ $plotResult['is_diagonal_used'] ? 'fa-circle-check text-emerald-400' : 'fa-triangle-exclamation text-amber-400' }}"></i>
                                <span class="font-bold">
                                    {{ $plotResult['is_diagonal_used'] ? __('Worked out from the four sides and the diagonal') : __('Estimate: average length x average width') }}
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">
                                {{ $plotResult['is_diagonal_used'] ? __('The diagonal splits the plot into two triangles, which gives an accurate area if the measurements are right.') : __('If the plot is crooked, add the diagonal for a more accurate area.') }}
                            </p>
                        </div>

                    @else
                        <!-- UNIT CONVERTER REPORT OUTPUT -->
                        <div class="p-5 bg-gradient-to-r from-amber-500/10 via-slate-950 to-slate-950 rounded-2xl border border-amber-500/30 flex items-center justify-between w-full">
                            <div>
                                <span class="text-xs font-bold text-slate-400 block">{{ $input_value }} {{ __(ucfirst($from_unit)) }} {{ __('converts to:') }}</span>
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
                                        <th class="py-2.5 px-3">{{ __('Unit') }}</th>
                                        <th class="py-2.5 px-3">{{ __('Equal to') }}</th>
                                        <th class="py-2.5 px-3 text-right">{{ __('Formula') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/60 text-slate-200 font-medium">
                                    <tr>
                                        <td class="py-2.5 px-3 font-bold text-white">{{ __('Marla') }}</td>
                                        <td class="py-2.5 px-3 font-mono font-bold text-emerald-400 text-sm">{{ $convertedUnits['marla'] }}</td>
                                        <td class="py-2.5 px-3 text-right text-slate-400 font-mono">{{ $marlaSqFt }} Sq Ft</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2.5 px-3 font-bold text-white">{{ __('Kanal') }}</td>
                                        <td class="py-2.5 px-3 font-mono font-bold text-white">{{ $convertedUnits['kanal'] }}</td>
                                        <td class="py-2.5 px-3 text-right text-slate-400 font-mono">20 Marla</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2.5 px-3 font-bold text-white">{{ __('Square feet') }}</td>
                                        <td class="py-2.5 px-3 font-mono font-bold text-amber-400">{{ number_format($convertedUnits['sqft']) }}</td>
                                        <td class="py-2.5 px-3 text-right text-slate-400 font-mono">{{ __('sq ft') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2.5 px-3 font-bold text-white">{{ __('Square yards') }}</td>
                                        <td class="py-2.5 px-3 font-mono font-bold text-white">{{ number_format($convertedUnits['sqyards']) }}</td>
                                        <td class="py-2.5 px-3 text-right text-slate-400 font-mono">9 Sq Ft</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2.5 px-3 font-bold text-white">{{ __('Sarsahi') }}</td>
                                        <td class="py-2.5 px-3 font-mono font-bold text-sky-400">{{ $convertedUnits['sarsahi'] }}</td>
                                        <td class="py-2.5 px-3 text-right text-slate-400 font-mono">1/9 Marla</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2.5 px-3 font-bold text-white">{{ __('Square karam') }}</td>
                                        <td class="py-2.5 px-3 font-mono font-bold text-white">{{ $convertedUnits['karam'] }}</td>
                                        <td class="py-2.5 px-3 text-right text-slate-400 font-mono">{{ $standard === 'society' ? "5.0' x 5.0'" : "5.5' x 5.5'" }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2.5 px-3 font-bold text-white">{{ __('Acre') }}</td>
                                        <td class="py-2.5 px-3 font-mono font-bold text-white">{{ $convertedUnits['acre'] }}</td>
                                        <td class="py-2.5 px-3 text-right text-slate-400 font-mono">43,560 {{ __('sq ft') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2.5 px-3 font-bold text-white">{{ __('Murabba (25 acres)') }}</td>
                                        <td class="py-2.5 px-3 font-mono font-bold text-white">{{ $convertedUnits['murabba'] }}</td>
                                        <td class="py-2.5 px-3 text-right text-slate-400 font-mono">25 {{ __('Acre') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2.5 px-3 font-bold text-white">{{ __('Square meters') }}</td>
                                        <td class="py-2.5 px-3 font-mono font-bold text-white">{{ $convertedUnits['sqm'] }}</td>
                                        <td class="py-2.5 px-3 text-right text-slate-400 font-mono">10.764 {{ __('sq ft') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <!-- Certified Footer Notes -->
                    <div class="pt-4 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between text-[11px] text-slate-500 gap-2">
                        <span>{{ __('This is a calculated estimate. Get the final measurement from the patwari or a licensed surveyor.') }}</span>
                        <span class="font-mono">Assan Zameen</span>
                    </div>

                </div>

            </div>

        </div>

    </div>
    @include('livewire.tools.partials.tool-footer', ['toolKey' => 'land'])
</div>