<div class="min-h-screen bg-[#070a13] text-slate-100 py-10 px-4 sm:px-6 lg:px-8 w-full overflow-x-hidden">

    <div class="max-w-7xl mx-auto space-y-8 w-full">

        <!-- Tool Header Banner -->
        <div class="text-center space-y-3 w-full">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-purple-500/10 border border-purple-500/30 text-purple-400 text-xs font-bold uppercase tracking-wider">
                <i class="fa-solid fa-building-circle-check text-sm"></i>
                <span>Punjab Housing Schemes Regulatory Verification Registry</span>
            </div>
            
            <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight" style="font-family: 'Noto Nastaliq Urdu', 'Noto Sans Arabic', serif; line-height: 1.8;">
                ہاؤسنگ سوسائٹی NOC و قانونی اسٹیٹس چیکر
            </h1>
            
            <p class="text-sm text-slate-400 max-w-2xl mx-auto leading-relaxed">
                پلاٹ فائل یا زمین میں سرمایہ کاری سے پہلے سوسائٹی کا TMA، LDA، FDA یا RDA سے منظور شدہ این او سی، لے آؤٹ پلان (LOP) اور رہن شدہ پلاٹس کی تفصیل لائیو چیک کریں۔
            </p>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-slate-900/90 rounded-3xl border border-slate-800 p-5 shadow-xl space-y-4 w-full">
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 w-full">
                
                <!-- Search Input -->
                <div class="sm:col-span-6 relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="سوسائٹی کا نام، NOC نمبر یا ڈویلپر سرچ کریں..."
                           class="w-full bg-slate-950 border border-slate-800 rounded-2xl pl-10 pr-4 py-3 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-amber-500 transition">
                </div>

                <!-- City Filter -->
                <div class="sm:col-span-3">
                    <select wire:model.live="filterCity" class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 text-xs text-white focus:outline-none focus:border-amber-500">
                        <option value="">تمام اضلاع / شہر (All Cities)</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}">{{ $city }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="sm:col-span-3">
                    <select wire:model.live="filterStatus" class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 text-xs text-white focus:outline-none focus:border-amber-500">
                        <option value="">تمام قانونی اسٹیٹس</option>
                        <option value="approved">منظور شدہ (Approved NOC ✓)</option>
                        <option value="under_process">زیرِ کارروائی (Under Process ⌛)</option>
                        <option value="illegal">غیر منظور شدہ / غیر قانونی (Illegal ⚠)</option>
                    </select>
                </div>

            </div>
        </div>

        <!-- Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 w-full">
            @forelse($societies as $item)
                @php
                    $statusColor = match($item->status) {
                        'approved' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30',
                        'under_process' => 'bg-amber-500/10 text-amber-400 border-amber-500/30',
                        'illegal' => 'bg-rose-500/10 text-rose-400 border-rose-500/30',
                    };

                    $statusBadgeText = match($item->status) {
                        'approved' => 'منظور شدہ (Approved)',
                        'under_process' => 'زیرِ جائزہ (Under Process)',
                        'illegal' => 'غیر قانونی (Illegal / Fake)',
                    };
                @endphp

                <div class="bg-slate-900/80 rounded-3xl border border-slate-800/80 p-5 shadow-xl hover:border-slate-700 transition flex flex-col justify-between space-y-4 relative overflow-hidden group">
                    
                    @if($item->is_featured)
                        <div class="absolute top-0 right-0 bg-gradient-to-l from-amber-500 to-amber-600 text-slate-950 text-[10px] font-black px-3 py-1 rounded-bl-xl uppercase tracking-wider">
                            Verified Partner
                        </div>
                    @endif

                    <div class="space-y-3">
                        <div class="flex items-start justify-between gap-2 pr-16">
                            <div>
                                <h3 class="text-base font-black text-white group-hover:text-amber-400 transition">
                                    {{ $item->name }}
                                </h3>
                                <p class="text-xs text-slate-400 mt-0.5">
                                    <i class="fa-solid fa-location-dot text-rose-400 mr-1"></i> {{ $item->city }} &bull; {{ $item->authority }}
                                </p>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold border {{ $statusColor }}">
                                <span class="w-2 h-2 rounded-full {{ $item->status === 'approved' ? 'bg-emerald-400' : ($item->status === 'under_process' ? 'bg-amber-400' : 'bg-rose-400') }}"></span>
                                <span>{{ $statusBadgeText }}</span>
                            </span>
                        </div>

                        <!-- Metrics Grid -->
                        <div class="grid grid-cols-2 gap-2 text-xs bg-slate-950/60 p-3 rounded-2xl border border-slate-800/80">
                            <div>
                                <span class="text-[10px] text-slate-500 block uppercase">NOC ریفرنس:</span>
                                <span class="font-mono text-slate-200 font-bold truncate block text-[11px]">{{ $item->noc_number ?: 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-500 block uppercase">منظور شدہ رقبہ:</span>
                                <span class="font-bold text-slate-200 block text-[11px]">{{ $item->total_sanctioned_area ?: 'نامعلوم' }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-500 block uppercase">کل منظور پلاٹس:</span>
                                <span class="font-mono font-bold text-slate-200 block text-[11px]">{{ number_format($item->sanctioned_plots_count) }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-500 block uppercase">رہن شدہ پلاٹس:</span>
                                <span class="font-mono font-bold text-amber-400 block text-[11px]">{{ number_format($item->mortgaged_plots_count) }}</span>
                            </div>
                        </div>

                        @if($item->risk_warning)
                            <div class="p-2.5 bg-rose-500/10 border border-rose-500/20 text-rose-300 rounded-xl text-[11px] leading-relaxed">
                                <i class="fa-solid fa-triangle-exclamation mr-1 text-rose-400"></i>
                                <span>{{ $item->risk_warning }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="pt-2 border-t border-slate-800/80 flex items-center justify-between">
                        <span class="text-[11px] text-slate-500">
                            ڈویلپر: <strong class="text-slate-300">{{ $item->developer_name ?: 'Private' }}</strong>
                        </span>

                        <button wire:click="viewDetails({{ $item->id }})" 
                                class="px-3.5 py-1.5 bg-slate-800 hover:bg-slate-700 text-amber-400 text-xs font-bold rounded-xl transition border border-slate-700 cursor-pointer">
                            مکمل تفصیلات
                        </button>
                    </div>

                </div>
            @empty
                <div class="col-span-full p-12 text-center bg-slate-900/50 rounded-3xl border border-slate-800 text-slate-400 text-xs">
                    مطلوبہ سرچ یا فلٹر کے مطابق کوئی ہاؤسنگ سکیم نہیں ملی۔
                </div>
            @endforelse
        </div>

        <div class="pt-4">
            {{ $societies->links() }}
        </div>

    </div>

    <!-- DETAIL MODAL -->
    @if($selectedSociety)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
            <div class="bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-2xl border border-slate-800 max-w-2xl w-full max-h-[90vh] overflow-y-auto space-y-6 relative">
                
                <div class="flex items-start justify-between border-b border-slate-800 pb-4">
                    <div>
                        <h2 class="text-xl font-black text-white flex items-center gap-2">
                            <span>{{ $selectedSociety->name }}</span>
                        </h2>
                        <p class="text-xs text-slate-400 mt-1">
                            ضلع {{ $selectedSociety->city }} &bull; منظوری اتھارٹی: <strong class="text-white">{{ $selectedSociety->authority }}</strong>
                        </p>
                    </div>

                    <button wire:click="closeDetails" class="p-2 text-slate-400 hover:text-white rounded-xl bg-slate-800">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <div class="space-y-4 text-xs">
                    <div class="grid grid-cols-2 gap-3 bg-slate-950 p-4 rounded-2xl border border-slate-800 font-mono">
                        <div>
                            <span class="text-slate-500 block text-[10px] uppercase">NOC نمبر</span>
                            <span class="text-amber-400 font-bold text-sm">{{ $selectedSociety->noc_number ?: 'NOT ALLOTTED' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[10px] uppercase">تاریخِ منظوری</span>
                            <span class="text-white font-bold">{{ $selectedSociety->approval_date ? $selectedSociety->approval_date->format('d M, Y') : 'زیرِ جائزہ' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[10px] uppercase">منظور شدہ رقبہ</span>
                            <span class="text-white font-bold">{{ $selectedSociety->total_sanctioned_area ?: 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[10px] uppercase">لے آؤٹ پلان (LOP) اسٹیٹس</span>
                            <span class="text-emerald-400 font-bold">{{ $selectedSociety->lop_status }}</span>
                        </div>
                    </div>

                    <div class="p-4 bg-amber-500/10 border border-amber-500/20 rounded-2xl space-y-1">
                        <span class="text-amber-400 font-bold block">رہن شدہ پلاٹس کا انتباہ (Mortgaged Plots Notice):</span>
                        <p class="text-[11px] text-amber-200 leading-relaxed">
                            اس سوسائٹی کے <strong>{{ number_format($selectedSociety->mortgaged_plots_count) }}</strong> پلاٹس ترقیاتی کاموں کی ضمانت کے طور پر سرکاری اتھارٹی کے پاس رہن ہیں۔ جب تک اتھارٹی این او سی جاری نہ کرے، ان مخصوص پلاٹس کی خرید و فروخت غیر قانونی سمجھی جاتی ہے۔
                        </p>
                    </div>

                    @if($selectedSociety->risk_warning)
                        <div class="p-4 bg-rose-500/10 border border-rose-500/20 rounded-2xl space-y-1">
                            <span class="text-rose-400 font-bold block">قانونی وارننگ (Regulatory Notice):</span>
                            <p class="text-[11px] text-rose-300 leading-relaxed">
                                {{ $selectedSociety->risk_warning }}
                            </p>
                        </div>
                    @endif
                </div>

                <div class="pt-4 border-t border-slate-800 flex justify-end">
                    <button wire:click="closeDetails" class="px-5 py-2 rounded-xl text-xs font-bold bg-slate-800 text-slate-300 hover:bg-slate-700">
                        بند کریں (Close)
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>