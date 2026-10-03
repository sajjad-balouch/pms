<div class="bg-slate-950 text-slate-300 py-16 px-4 sm:px-6 lg:px-8" x-data="{ openFaq: null }">
    <div class="max-w-4xl mx-auto space-y-10">
        
        <!-- Header -->
        <div class="border-b border-slate-800 pb-8 text-center sm:text-left">
            <span class="text-xs font-semibold text-amber-400 uppercase tracking-widest">Help Center</span>
            <h1 class="text-3xl font-extrabold text-white mt-1">Land Ownership & Transfer FAQ</h1>
            <p class="text-slate-400 text-xs mt-2">Common questions regarding plot verification, units, and legal ownership.</p>
        </div>

        <div class="space-y-4">
            <!-- FAQ Item 1 -->
            <div class="border border-slate-800 rounded-xl bg-slate-900/50 overflow-hidden">
                <button @click="openFaq === 1 ? openFaq = null : openFaq = 1" class="w-full text-left px-5 py-4 font-semibold text-white flex items-center justify-between text-sm hover:text-amber-400 transition-colors">
                    <span>زمین کی پیمائش (Units of Land Measurement) کس طرح شمار کی جاتی ہے؟</span>
                    <i class="fa-solid" :class="openFaq === 1 ? 'fa-chevron-up text-amber-400' : 'fa-chevron-down text-slate-500'"></i>
                </button>
                <div x-show="openFaq === 1" x-collapse class="px-5 pb-5 text-xs text-slate-400 leading-relaxed border-t border-slate-800/50 pt-3">
                    ہمارے پلیٹ فارم پر زمین کی پیمائش کو کنال، مرلہ اور مربع فٹ (Square Feet) میں ظاہر کیا جاتا ہے۔
                    <ul class="list-disc list-inside mt-2 space-y-1 text-slate-300">
                        <li>1 کنال = 20 مرلے</li>
                        <li>1 مرلہ = 225 مربع فٹ (معیاری ہاؤسنگ سوسائٹیز کے مطابق) / 272.25 مربع فٹ (ریونیو ریکارڈز کے مطابق)</li>
                    </ul>
                </div>
            </div>

            <!-- FAQ Item 2 -->
            <div class="border border-slate-800 rounded-xl bg-slate-900/50 overflow-hidden">
                <button @click="openFaq === 2 ? openFaq = null : openFaq = 2" class="w-full text-left px-5 py-4 font-semibold text-white flex items-center justify-between text-sm hover:text-amber-400 transition-colors">
                    <span>مالکِ اصلی اور مالکِ قبضہ میں کیا فرق ہے؟</span>
                    <i class="fa-solid" :class="openFaq === 2 ? 'fa-chevron-up text-amber-400' : 'fa-chevron-down text-slate-500'"></i>
                </button>
                <div x-show="openFaq === 2" x-collapse class="px-5 pb-5 text-xs text-slate-400 leading-relaxed border-t border-slate-800/50 pt-3">
                    <strong>مالکِ اصلی (Title Owner):</strong> وہ فرد جس کے نام پر سرکاری انتقال یا سوسائٹی رجسٹر میں پراپرٹی کی قانونی ملکیت ہو۔<br>
                    <strong>مالکِ قبضہ (Possessory Owner):</strong> وہ فرد جس کے پاس پراپرٹی کا موقع پر فزیکل قبضہ ہو لیکن انتقال کا حتمی عمل باقی ہو۔ ہم ہر پلاٹ کا اسٹیٹس واضحت کے ساتھ درج کرتے ہیں۔
                </div>
            </div>

            <!-- FAQ Item 3 -->
            <div class="border border-slate-800 rounded-xl bg-slate-900/50 overflow-hidden">
                <button @click="openFaq === 3 ? openFaq = null : openFaq = 3" class="w-full text-left px-5 py-4 font-semibold text-white flex items-center justify-between text-sm hover:text-amber-400 transition-colors">
                    <span>پلاٹ کی بائنگ اور ٹرانسفر کا قانونی طریقہ کار کیا ہے؟</span>
                    <i class="fa-solid" :class="openFaq === 3 ? 'fa-chevron-up text-amber-400' : 'fa-chevron-down text-slate-500'"></i>
                </button>
                <div x-show="openFaq === 3" x-collapse class="px-5 pb-5 text-xs text-slate-400 leading-relaxed border-t border-slate-800/50 pt-3">
                    1. ٹوکن ڈپازٹ اور بیعانہ سائن کرنا۔<br>
                    2. سوسائٹی یا ریونیو ڈیپارٹمنٹ سے NOC اور کلیئرنس سرٹیفکیٹ حاصل کرنا۔<br>
                    3. ٹرانسفر فیس کی ادائیگی اور بائیر/سیلر کی بالمشافہ تصدیق کے بعد الائٹمنٹ لیٹر کی منتقلی۔
                </div>
            </div>

            <!-- FAQ Item 4 -->
            <div class="border border-slate-800 rounded-xl bg-slate-900/50 overflow-hidden">
                <button @click="openFaq === 4 ? openFaq = null : openFaq = 4" class="w-full text-left px-5 py-4 font-semibold text-white flex items-center justify-between text-sm hover:text-amber-400 transition-colors">
                    <span>How are mortgaged (رہن / مرتہن) properties handled?</span>
                    <i class="fa-solid" :class="openFaq === 4 ? 'fa-chevron-up text-amber-400' : 'fa-chevron-down text-slate-500'"></i>
                </button>
                <div x-show="openFaq === 4" x-collapse class="px-5 pb-5 text-xs text-slate-400 leading-relaxed border-t border-slate-800/50 pt-3">
                    Plots listed under mortgaged status clearly display the obligation holder details (مرتہن). The transfer cannot proceed until a official Redemption Deed (فکِ رہن) or Bank NOC is verified and uploaded to the platform system.
                </div>
            </div>
        </div>
    </div>
</div>