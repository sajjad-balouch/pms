<div class="py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

    @if (session()->has('message'))
        <div class="flex items-center gap-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-3 rounded-xl shadow-sm">
            <i class="fa-solid fa-circle-check text-lg"></i>
            <span class="text-xs font-bold">{{ session('message') }}</span>
        </div>
    @endif

    <!-- Top Bar with Live Link Copy -->
    <div class="bg-slate-800/90 p-6 rounded-2xl shadow-md border border-slate-700/80 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-white flex items-center gap-2">
                <i class="fa-solid fa-id-card-clip text-indigo-400"></i>
                <span>ڈیجیٹل وزیٹنگ کارڈ و پورٹ فولیو سیٹنگز</span>
            </h2>
            <p class="text-xs text-slate-400 mt-1">آپ کا پرسنل پبلک پورٹ فولیو پیج اور شیئر ایبل ڈیجیٹل وزیٹنگ کارڈ</p>
        </div>

        <div class="flex items-center gap-2" x-data>
            @php $publicUrl = url('/agent/' . ($username ?: 'my-agency')); @endphp
            <a href="{{ $publicUrl }}" target="_blank" class="px-3.5 py-2 bg-slate-900 hover:bg-slate-750 text-indigo-400 border border-indigo-500/30 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                <span>پبلک کارڈ دیکھیں</span>
            </a>

            <button type="button" @click="navigator.clipboard.writeText('{{ $publicUrl }}'); alert('آپ کا پبلک پروفائل لنک کاپی ہو گیا! گاہکوں کو واٹس ایپ بھیجیں۔');"
                    class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-md shadow-emerald-600/20 cursor-pointer">
                <i class="fa-solid fa-copy"></i>
                <span>لنک کاپی کریں</span>
            </button>
        </div>
    </div>

    <!-- Edit Form -->
    <form wire:submit="saveProfile" class="bg-slate-800/90 rounded-2xl p-6 border border-slate-700/80 shadow-md space-y-5">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">ایجنسی کا نام (Agency Title) *</label>
                <input type="text" wire:model="agency_name" placeholder="مثلاً: المکہ ریئل اسٹیٹ ایڈوائزرز" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                @error('agency_name') <span class="text-rose-400 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">پرسنل پورٹل یوزرنیم / لنک (Slug) *</label>
                <div class="flex items-center bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-400">
                    <span class="text-slate-500 font-mono">assanzameen.com/agent/</span>
                    <input type="text" wire:model="username" class="border-0 text-white font-bold text-xs p-0 focus:ring-0 focus:outline-none flex-1">
                </div>
                @error('username') <span class="text-rose-400 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">ٹیگ لائن یا نعرہ (Tagline)</label>
                <input type="text" wire:model="agency_tagline" placeholder="مثلاً: دیانت داری ہماری پہچان" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">واٹس ایپ / فون نمبر *</label>
                <input type="text" wire:model="phone" placeholder="03001234567" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">شہر (City)</label>
                <input type="text" wire:model="city_name" placeholder="مثلاً: لاہور / فیصل آباد" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">دفتر کا پتہ (Office Address)</label>
                <input type="text" wire:model="office_address" placeholder="مثلاً: آفس نمبر 4، مین بلیوارڈ، فیز 1" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">گوگل میپ لوکیشن لنک (Google Maps)</label>
                <input type="url" wire:model="google_map_link" placeholder="https://maps.app.goo.gl/..." class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-300 uppercase mb-1">ایجنسی کا لوگو یا اپنی تصویر</label>
            <div class="flex items-center gap-4">
                @if($existing_logo)
                    <img src="{{ asset('public/' . $existing_logo) }}" class="w-12 h-12 rounded-xl object-cover border border-slate-700 bg-slate-950">
                @endif
                <input type="file" wire:model="new_logo" class="text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-500/10 file:text-indigo-400 hover:file:bg-indigo-500/20">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-300 uppercase mb-1">ایجنسی کے بارے میں مختصر تعارف (Bio / About)</label>
            <textarea wire:model="about_agency" rows="3" placeholder="ہم 10 سال سے اس علاقے میں تصدیق شدہ پلاٹس اور مکانات کی خرید و فروخت کا کام کر رہے ہیں..." class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-indigo-500"></textarea>
        </div>

        <div class="flex justify-end pt-3 border-t border-slate-700/80">
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold flex items-center gap-2 shadow-lg shadow-indigo-600/20 cursor-pointer">
                <i class="fa-solid fa-floppy-disk"></i>
                <span>پروفائل کارڈ محفوظ کریں</span>
            </button>
        </div>

    </form>

</div>