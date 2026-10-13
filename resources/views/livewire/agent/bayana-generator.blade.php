<div class="py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

    @if(!$isPreview)
    <!-- ========================================== -->
    <!-- INPUT FORM VIEW -->
    <!-- ========================================== -->
    <div class="space-y-6">
        
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-slate-800/90 p-6 rounded-2xl shadow-md border border-slate-700/80 gap-4">
            <div>
                <h2 class="text-2xl font-black text-white flex items-center gap-2">
                    <i class="fa-solid fa-file-contract text-emerald-400"></i>
                    <span>اقرار نامہ بیعانہ و ٹوکن رسید جنریٹر</span>
                </h2>
                <p class="text-xs text-slate-400 mt-1">قانونی شرائط و ضوابط کے ساتھ پرنٹ اور پی ڈی ایف کے قابل فوری دستاویز</p>
            </div>
            <div>
                <span class="px-3 py-1 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-bold rounded-full">
                    <i class="fa-solid fa-stamp mr-1"></i> Legal Draft Ready
                </span>
            </div>
        </div>

        <form wire:submit="generateReceipt" class="bg-slate-800/90 rounded-2xl p-6 border border-slate-700/80 shadow-md space-y-6">
            
            <!-- Type and Dates -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 border-b border-slate-700/80 pb-5">
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">دستاویز کی قسم (Document Type)</label>
                    <select wire:model="agreementType" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                        <option value="bayana">باقاعدہ اقرار نامہ بیعانہ (Bayana Agreement)</option>
                        <option value="token">ٹوکن رسید / سر دستی بیعانہ (Token Receipt)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">تاریخ اقرار نامہ (Date)</label>
                    <input type="date" wire:model="agreementDate" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">آخری تاریخ رجسٹری / بقیہ رقم</label>
                    <input type="date" wire:model="finalPaymentDate" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <!-- Seller & Buyer Columns -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-b border-slate-700/80 pb-5">
                
                <!-- First Party: Seller -->
                <div class="space-y-3 bg-slate-900/60 p-4 rounded-xl border border-slate-700/60">
                    <h3 class="text-xs font-bold text-amber-400 uppercase flex items-center gap-1.5 border-b border-slate-800 pb-2">
                        <i class="fa-solid fa-user-tag"></i> فریقِ اول (فروخت کنندہ / مالک)
                    </h3>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">مالک کا پورا نام *</label>
                        <input type="text" wire:model="sellerName" placeholder="مثلاً: محمد اسلم ولد عبدالرشید" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                        @error('sellerName') <span class="text-rose-400 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">شناختی کارڈ نمبر (CNIC)</label>
                        <input type="text" wire:model="sellerCnic" placeholder="33100-xxxxxxx-x" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">موبائل نمبر</label>
                        <input type="text" wire:model="sellerPhone" placeholder="03001234567" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                    </div>
                </div>

                <!-- Second Party: Buyer -->
                <div class="space-y-3 bg-slate-900/60 p-4 rounded-xl border border-slate-700/60">
                    <h3 class="text-xs font-bold text-emerald-400 uppercase flex items-center gap-1.5 border-b border-slate-800 pb-2">
                        <i class="fa-solid fa-user-check"></i> فریقِ دوئم (خریدار)
                    </h3>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">خریدار کا پورا نام *</label>
                        <input type="text" wire:model="buyerName" placeholder="مثلاً: طارق جاوید ولد نذیر احمد" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                        @error('buyerName') <span class="text-rose-400 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">شناختی کارڈ نمبر (CNIC)</label>
                        <input type="text" wire:model="buyerCnic" placeholder="33100-xxxxxxx-x" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">موبائل نمبر</label>
                        <input type="text" wire:model="buyerPhone" placeholder="03007654321" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                    </div>
                </div>

            </div>

            <!-- Property Description -->
            <div class="space-y-3 border-b border-slate-700/80 pb-5">
                <h3 class="text-xs font-bold text-indigo-400 uppercase flex items-center gap-1.5">
                    <i class="fa-solid fa-map-location-dot"></i> پراپرٹی و پلاٹ کی تفصیلات
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">پراپرٹی کا عنوان / تفصیل *</label>
                        <input type="text" wire:model="propertyTitle" placeholder="مثلاً: مکان نمبر 12، گلی نمبر 4" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                        @error('propertyTitle') <span class="text-rose-400 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">سوسائٹی / موضع / ٹاؤن</label>
                        <input type="text" wire:model="schemeName" placeholder="مثلاً: کینال سٹی" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">رقبہ و سائز *</label>
                        <input type="text" wire:model="areaSize" placeholder="مثلاً: 5 مرلہ (1125 مربع فٹ)" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                        @error('areaSize') <span class="text-rose-400 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Financials -->
            <div class="space-y-3 border-b border-slate-700/80 pb-5">
                <h3 class="text-xs font-bold text-amber-400 uppercase flex items-center gap-1.5">
                    <i class="fa-solid fa-coins"></i> مالی شرائط و رقوم کا اندراج (PKR)
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">کل طے شدہ رقم (Total Price) *</label>
                        <input type="number" wire:model.live.debounce.300ms="totalDealAmount" placeholder="5000000" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                        @error('totalDealAmount') <span class="text-rose-400 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">ادا شدہ بیعانہ رقم (Bayana Amount) *</label>
                        <input type="number" wire:model.live.debounce.300ms="bayanaAmount" placeholder="500000" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-emerald-400 font-bold focus:outline-none focus:border-indigo-500">
                        @error('bayanaAmount') <span class="text-rose-400 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">بقیہ واجب الادا رقم</label>
                        <input type="text" readonly value="PKR {{ number_format($remainingAmount) }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-400 font-bold font-mono">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">طریقہ ادائیگی</label>
                        <select wire:model="paymentMethod" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                            <option value="cash">کیش رقم (Cash in Hand)</option>
                            <option value="cheque">بینک چیک / پے آرڈر (Cheque / Pay Order)</option>
                            <option value="online">آن لائن ٹرانسفر (Online Transfer)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">چیک نمبر یا بینک تفصیلات (اگر کوئی ہو)</label>
                        <input type="text" wire:model="chequeDetails" placeholder="مثلاً: HBL چیک نمبر 98765432" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Witnesses -->
            <div class="space-y-3">
                <h3 class="text-xs font-bold text-slate-300 uppercase flex items-center gap-1.5">
                    <i class="fa-solid fa-users"></i> گواہان کی تفصیلات (اختیاری)
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" wire:model="witness1Name" placeholder="گواہ 1 کا نام" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                        <input type="text" wire:model="witness1Cnic" placeholder="گواہ 1 کا CNIC" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" wire:model="witness2Name" placeholder="گواہ 2 کا نام" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                        <input type="text" wire:model="witness2Cnic" placeholder="گواہ 2 کا CNIC" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold flex items-center gap-2 shadow-lg shadow-emerald-600/20 cursor-pointer">
                    <i class="fa-solid fa-eye"></i>
                    <span>اقرار نامہ تیار کریں اور پرنٹ دیکھیں</span>
                </button>
            </div>
        </form>

    </div>

    @else
    <!-- ========================================== -->
    <!-- PRINTABLE LEGAL DOCUMENT PREVIEW -->
    <!-- ========================================== -->
    <div class="space-y-4">
        
        <!-- Action Control Bar (Hidden on Print) -->
        <div class="print:hidden flex items-center justify-between bg-slate-800 p-4 rounded-2xl border border-slate-700">
            <button type="button" wire:click="editAgreement" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white text-xs font-bold rounded-xl flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i>
                <span>واپس ایڈیٹنگ پر جائیں</span>
            </button>

            <button type="button" onclick="window.print()" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl flex items-center gap-2 shadow-md">
                <i class="fa-solid fa-print"></i>
                <span>پرنٹ کریں یا PDF محفوظ کریں</span>
            </button>
        </div>

        <!-- The Stamp Paper / Document Body -->
        <div class="bg-white text-black p-8 sm:p-12 rounded-2xl shadow-2xl border border-slate-200 print:border-0 print:shadow-none print:p-4 text-right" dir="rtl" style="font-family: 'Jameel Noori Nastaleeq', 'Noto Nastaliq Urdu', serif; line-height: 2.2;">
            
            <!-- Document Top Header -->
            <div class="text-center border-b-2 border-black pb-4 mb-6">
                <h1 class="text-3xl font-extrabold tracking-wide mb-1">
                    {{ $agreementType === 'bayana' ? 'اقرار نامہ بیعانہ (باوجود معاہدہ بیع)' : 'رسید ٹوکن بیعانہ سر دستی' }}
                </h1>
                <p class="text-sm font-sans text-gray-700 font-bold">بمقام ڈیلر آفس: {{ Auth::user()->name }} | تاریخ: {{ \Carbon\Carbon::parse($agreementDate)->format('d-m-Y') }}</p>
            </div>

            <!-- Agreement Text Body -->
            <div class="text-base text-gray-900 space-y-4 text-justify">
                <p>
                    <strong>منکہ فریقِ اول (فروخت کنندہ):</strong> جناب <strong>{{ $sellerName }}</strong>، 
                    شناختی کارڈ نمبر: <span class="font-sans font-bold">{{ $sellerCnic ?: '_____________________' }}</span>، 
                    فون نمبر: <span class="font-sans">{{ $sellerPhone }}</span>۔
                </p>

                <p>
                    <strong>بہ حق فریقِ دوئم (خریدار):</strong> جناب <strong>{{ $buyerName }}</strong>، 
                    شناختی کارڈ نمبر: <span class="font-sans font-bold">{{ $buyerCnic ?: '_____________________' }}</span>، 
                    فون نمبر: <span class="font-sans">{{ $buyerPhone }}</span>۔
                </p>

                <p class="border-t border-gray-300 pt-3">
                    جو کہ فریقِ اول اپنی ملکیتی و تصرفی پراپرٹی واقع <strong>{{ $propertyTitle }}</strong>، 
                    موضع / سکیم <strong>{{ $schemeName ?: 'علاقہ ہذا' }}</strong>، 
                    بقدر رقبہ <strong>{{ $areaSize }}</strong>، فریقِ دوئم کے ہاتھ بابت مبلغ 
                    <strong>{{ number_format($totalDealAmount) }} روپے</strong> (نصف جن کے {{ number_format($totalDealAmount / 2) }} روپے بنتے ہیں) 
                    قطعی فروخت کرنے پر رضامند پایا ہے۔
                </p>

                <p>
                    جس میں سے فریقِ دوئم نے بطور بیعانہ سر دستی مبلغ 
                    <strong>{{ number_format($bayanaAmount) }} روپے</strong> 
                    بحساب ادائیگی <strong>({{ $paymentMethod === 'cash' ? 'نقد کیش' : ($paymentMethod === 'cheque' ? 'بینک چیک: ' . $chequeDetails : 'آن لائن ٹرانسفر') }})</strong> 
                    روبرو گواہان حاشیہ فریقِ اول کو ادا کر دیے ہیں، اور فریقِ اول نے وصول پا لیے ہیں۔ 
                    بقیہ واجب الادا رقم مبلغ <strong>{{ number_format($remainingAmount) }} روپے</strong> 
                    مورخہ <strong>{{ \Carbon\Carbon::parse($finalPaymentDate)->format('d-m-Y') }}</strong> تک ادا کر کے 
                    رجسٹری، انتقال یا قبضہ حاصل کرنے کا فریقِ دوئم مجاز ہو گا۔
                </p>

                <!-- Legal Clauses -->
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 text-sm space-y-2 font-sans my-4">
                    <p class="font-bold">شرائط و ضوابط معاہدہ:</p>
                    <ol class="list-decimal list-inside space-y-1 text-gray-800">
                        <li>فریقِ اول پابند ہے کہ مقررہ تاریخ تک پراپرٹی تمام قسم کے قانونی، مالیاتی تنازعات اور بینک بار سے پاک کر کے رجسٹری یا ٹرانسفر کروائے گا۔</li>
                        <li>اگر فریقِ دوئم مقررہ تاریخ تک بقیہ رقم ادا کرنے میں ناکام رہا تو بیعانہ رقم ضبط تصور ہو گی، اور اگر فریقِ اول مکر گیا تو وہ بیعانہ کی دوگنی رقم ادا کرنے کا پابند ہو گا۔</li>
                        <li>یہ اقرار نامہ دونوں فریقین نے بقائمی ہوش و حواس بغیر کسی جبر و اکراہ روبرو گواہان تحریر کر دیا ہے تاکہ سند رہے اور بوقت ضرورت کام آئے۔</li>
                    </ol>
                </div>
            </div>

            <!-- Signatures Section -->
            <div class="grid grid-cols-2 gap-8 pt-12 text-center font-sans">
                <div class="border-t-2 border-black pt-2">
                    <p class="font-bold text-base">دستخط / نشان انگوٹھا فریقِ اول (مالک)</p>
                    <p class="text-xs text-gray-600 mt-1">{{ $sellerName }}</p>
                </div>
                <div class="border-t-2 border-black pt-2">
                    <p class="font-bold text-base">دستخط / نشان انگوٹھا فریقِ دوئم (خریدار)</p>
                    <p class="text-xs text-gray-600 mt-1">{{ $buyerName }}</p>
                </div>
            </div>

            <!-- Witnesses Signatures -->
            <div class="grid grid-cols-3 gap-6 pt-10 text-center font-sans text-xs">
                <div class="border-t border-gray-400 pt-2">
                    <p class="font-bold">گواہ شد 1</p>
                    <p class="text-gray-700">{{ $witness1Name ?: '________________' }}</p>
                    <p class="text-gray-500">{{ $witness1Cnic }}</p>
                </div>
                <div class="border-t border-gray-400 pt-2">
                    <p class="font-bold">ڈیلر / ثالثی دستخط و مہر</p>
                    <p class="text-emerald-700 font-bold">{{ Auth::user()->name }}</p>
                    <p class="text-gray-500">{{ Auth::user()->phone }}</p>
                </div>
                <div class="border-t border-gray-400 pt-2">
                    <p class="font-bold">گواہ شد 2</p>
                    <p class="text-gray-700">{{ $witness2Name ?: '________________' }}</p>
                    <p class="text-gray-500">{{ $witness2Cnic }}</p>
                </div>
            </div>

            <!-- Portal Stamp Watermark at Footer -->
            <div class="mt-8 pt-3 border-t border-gray-200 text-center text-[10px] text-gray-400 font-sans">
                تیار کردہ بذریعہ AssanZameen.com پورٹل | قانونی دستاویز برائے ڈیل ریکارڈ
            </div>

        </div>

    </div>
    @endif

</div>