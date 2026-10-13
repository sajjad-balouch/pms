<div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

    <!-- Flash Alert -->
    @if (session()->has('message'))
        <div class="flex items-center gap-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-3 rounded-xl shadow-sm">
            <i class="fa-solid fa-circle-check text-lg"></i>
            <span class="text-xs font-bold">{{ session('message') }}</span>
        </div>
    @endif

    <!-- Header Section -->
    <div class="bg-slate-800/90 p-6 rounded-2xl shadow-md border border-slate-700/80 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-black text-white flex items-center gap-2">
                <i class="fa-solid fa-handshake-angle text-amber-400"></i>
                <span>B2B ڈیلر ایکسچینج (Co-Broking Network)</span>
            </h2>
            <p class="text-xs text-slate-400 mt-1">
                "میرا گاہک، تیرا مال" — دوسرے ایجنٹس کی ڈیمانڈز دیکھیں اور 50/50 کمیشن پر مشترکہ ڈیلز کلوز کریں
            </p>
        </div>

        <button wire:click="createDemand" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-450 text-slate-950 font-black text-xs rounded-xl transition flex items-center gap-2 shadow-lg shadow-amber-500/20 cursor-pointer">
            <i class="fa-solid fa-bullhorn"></i>
            <span>اپنی ڈیمانڈ پوسٹ کریں</span>
        </button>
    </div>

    <!-- Navigation Tabs & Search Filters -->
    <div class="bg-slate-800/90 p-4 rounded-2xl border border-slate-700/80 shadow-md space-y-4">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
            
            <!-- Tabs -->
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button type="button" wire:click="$set('activeTab', 'all')"
                        class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $activeTab === 'all' ? 'bg-amber-500 text-slate-950 font-black shadow-md' : 'bg-slate-900 text-slate-300 hover:text-white' }}">
                    <i class="fa-solid fa-earth-asia"></i>
                    <span>تمام ڈیلرز کی ڈیمانڈز ({{ $totalOpen }})</span>
                </button>

                <button type="button" wire:click="$set('activeTab', 'my_demands')"
                        class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $activeTab === 'my_demands' ? 'bg-amber-500 text-slate-950 font-black shadow-md' : 'bg-slate-900 text-slate-300 hover:text-white' }}">
                    <i class="fa-solid fa-user-check"></i>
                    <span>میری پوسٹ کردہ ({{ $myDemandsCount }})</span>
                </button>
            </div>

            <!-- Search Inputs -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 w-full sm:w-auto">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="تلاش کریں (علاقہ یا پلاٹ)..." 
                       class="bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-1.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-amber-500">
                
                <select wire:model.live="filterType" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-white focus:outline-none focus:border-amber-500">
                    <option value="">تمام اقسام</option>
                    <option value="plot">پلاٹ (Plot)</option>
                    <option value="house">مکان (House)</option>
                    <option value="commercial">کمرشل (Commercial)</option>
                    <option value="agricultural">زرعی زمین</option>
                </select>
            </div>

        </div>
    </div>

    <!-- Demands Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($demands as $demand)
            <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-5 shadow-xl hover:border-slate-700 transition flex flex-col justify-between space-y-4">
                
                <div class="space-y-3">
                    <!-- Card Top Header -->
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-md {{ $demand->urgency === 'urgent' ? 'bg-rose-500/10 text-rose-400 border border-rose-500/20' : 'bg-blue-500/10 text-blue-400 border border-blue-500/20' }}">
                                {{ $demand->urgency === 'urgent' ? '🔥 فوری کیش خریدار' : 'معمول کی طلب' }}
                            </span>
                            <h3 class="text-sm font-black text-white mt-2 line-clamp-2">{{ $demand->title }}</h3>
                        </div>
                        <span class="text-[10px] font-bold uppercase px-2 py-0.5 bg-slate-800 text-slate-300 rounded border border-slate-700 shrink-0">
                            {{ ucfirst($demand->property_type) }}
                        </span>
                    </div>

                    <!-- Details Spec Grid -->
                    <div class="p-3 bg-slate-950/80 rounded-xl border border-slate-800/80 space-y-1.5 text-xs">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">مطلوبہ سائز:</span>
                            <span class="font-bold text-amber-400">{{ $demand->area_size }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">علاقہ / لوکیشن:</span>
                            <span class="font-semibold text-slate-200 truncate max-w-[150px]">{{ $demand->target_location }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">شہر:</span>
                            <span class="font-semibold text-slate-300">{{ $demand->city }}</span>
                        </div>
                        <div class="flex justify-between items-center pt-1 border-t border-slate-800">
                            <span class="text-slate-400 font-bold">بجٹ بریکٹ:</span>
                            <span class="font-black text-emerald-400 font-mono">
                                {{ $demand->budget_max ? 'تا PKR ' . number_format($demand->budget_max) : 'بجٹ اوپن' }}
                            </span>
                        </div>
                    </div>

                    @if($demand->details)
                        <p class="text-[11px] text-slate-400 leading-relaxed line-clamp-2">
                            {{ $demand->details }}
                        </p>
                    @endif
                </div>

                <!-- Footer: Dealer Identity & WhatsApp Connect -->
                <div class="pt-3 border-t border-slate-800 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center text-xs font-black text-amber-400 border border-slate-700">
                            {{ mb_substr($demand->agent->name ?? 'A', 0, 1) }}
                        </div>
                        <div class="text-[11px]">
                            <div class="font-bold text-white leading-tight">{{ $demand->agent->agency_name ?: ($demand->agent->name ?? 'Agent') }}</div>
                            <div class="text-[10px] text-slate-500">{{ $demand->created_at->diffForHumans() }}</div>
                        </div>
                    </div>

                    @if($demand->agent_id === Auth::id())
                        <button type="button" wire:click="markClosed({{ $demand->id }})" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold rounded-xl transition cursor-pointer">
                            مکمل / بند کریں
                        </button>
                    @else
                        <button type="button" 
                                wire:click="connectDealer({{ $demand->id }})"
                                class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl transition flex items-center gap-1.5 shadow-md shadow-emerald-600/20 cursor-pointer">
                            <i class="fa-brands fa-whatsapp text-sm"></i>
                            <span>ڈیل آفر کریں</span>
                        </button>
                    @endif
                </div>

            </div>
        @empty
            <div class="col-span-full p-12 text-center text-slate-500 bg-slate-900/60 rounded-3xl border border-slate-800">
                <i class="fa-solid fa-handshake-slash text-3xl mb-2 text-slate-600"></i>
                <p class="text-xs">فی الوقت اس کیٹیگری میں کوئی اوپن ڈیمانڈ دستیاب نہیں ہے۔</p>
            </div>
        @endforelse
    </div>

    <div class="pt-4">
        {{ $demands->links() }}
    </div>

    <!-- Demand Create Modal -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md">
        <div class="bg-slate-900 rounded-3xl shadow-2xl w-full max-w-lg border border-slate-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-800 flex justify-between items-center">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-bullhorn text-amber-400"></i>
                    <span>نئی کلائنٹ ڈیمانڈ شائع کریں</span>
                </h3>
                <button wire:click="$set('isModalOpen', false)" class="text-slate-400 hover:text-white text-lg font-bold">&times;</button>
            </div>

            <form wire:submit="saveDemand" class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-1">ڈیمانڈ کا عنوان *</label>
                    <input type="text" wire:model="title" placeholder="مثلاً: ارجنٹ 1 کنال کیش خریدار برائے ڈی ایچ اے" 
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                    @error('title') <span class="text-rose-400 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1">پراپرٹی کی نوعیت *</label>
                        <select wire:model="property_type" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                            <option value="plot">پلاٹ (Plot)</option>
                            <option value="house">مکان (House)</option>
                            <option value="commercial">کمرشل (Commercial)</option>
                            <option value="agricultural">زرعی زمین</option>
                            <option value="apartment">اپارٹمنٹ</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1">مطلوبہ سائز *</label>
                        <input type="text" wire:model="area_size" placeholder="مثلاً: 5 مرلہ، 1 کنال" 
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                        @error('area_size') <span class="text-rose-400 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1">شہر (City) *</label>
                        <input type="text" wire:model="city" placeholder="مثلاً: لاہور" 
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1">مخصوص علاقہ / لوکیشن *</label>
                        <input type="text" wire:model="target_location" placeholder="مثلاً: بحریہ ٹاؤن فیز 8" 
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1">زیادہ سے زیادہ بجٹ (PKR) *</label>
                        <input type="number" wire:model="budget_max" placeholder="مثلاً: 7500000" 
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                        @error('budget_max') <span class="text-rose-400 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1">ڈیل کی ارجنسی</label>
                        <select wire:model="urgency" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                            <option value="urgent">فوری کیش خریدار (Urgent)</option>
                            <option value="standard">معمول کی تلاش (Standard)</option>
                            <option value="flexible">ریٹ چیک کرنا (Flexible)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-1">اضافی تفصیل یا شرط</label>
                    <textarea wire:model="details" rows="2" placeholder="مثلاً: کارنر پلاٹ ہو، مین روڈ سے قریب ترجیح ہو گی..." 
                              class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" wire:click="$set('isModalOpen', false)" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-xl text-xs font-bold cursor-pointer">منسوخ</button>
                    <button type="submit" class="px-5 py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-black rounded-xl text-xs shadow-md cursor-pointer">پوسٹ کریں</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Universal Direct WhatsApp Trigger Script -->
    <script>
        document.addEventListener('livewire:navigated', initExchangeWhatsAppListener);
        document.addEventListener('DOMContentLoaded', initExchangeWhatsAppListener);

        function initExchangeWhatsAppListener() {
            if (window._exchangeWhatsAppListenerAttached) return;
            window._exchangeWhatsAppListenerAttached = true;

            window.addEventListener('trigger-whatsapp', (event) => {
                const data = event.detail;
                const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

                if (isMobile) {
                    // موبائل پر براہِ راست WhatsApp App کھل جائے گی
                    window.location.href = data.appUrl;
                } else {
                    // ڈیسک ٹاپ پر پہلے انسٹال شدہ WhatsApp App کھولنے کی کوشش کریں
                    const iframe = document.createElement('iframe');
                    iframe.style.display = 'none';
                    iframe.src = data.appUrl;
                    document.body.appendChild(iframe);

                    // اگر ایپ انسٹال نہیں ہے یا بیک گراؤنڈ میں نہیں کھلی، تو 700ms بعد سیدھا WhatsApp Web کھول دیں
                    setTimeout(() => {
                        document.body.removeChild(iframe);
                        if (!document.hidden) {
                            window.open(data.webUrl, '_blank');
                        }
                    }, 700);
                }
            });

            window.addEventListener('notify-error', (event) => {
                alert(event.detail.message || 'اس ڈیلر کا فون نمبر دستیاب نہیں ہے۔');
            });
        }
    </script>
</div>