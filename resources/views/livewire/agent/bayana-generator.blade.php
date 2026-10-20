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
                    <span>Bayana Agreement & Token Receipt Generator</span>
                </h2>
                <p class="text-xs text-slate-400 mt-1">Instant print and PDF-ready document with legal terms and conditions</p>
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
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Document Type</label>
                    <select wire:model="agreementType" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                        <option value="bayana">Formal Bayana Agreement</option>
                        <option value="token">Token Receipt / Advance Bayana</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Agreement Date</label>
                    <input type="date" wire:model="agreementDate" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Final Payment / Registry Due Date</label>
                    <input type="date" wire:model="finalPaymentDate" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <!-- Seller & Buyer Columns -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-b border-slate-700/80 pb-5">
                
                <!-- First Party: Seller -->
                <div class="space-y-3 bg-slate-900/60 p-4 rounded-xl border border-slate-700/60">
                    <h3 class="text-xs font-bold text-amber-400 uppercase flex items-center gap-1.5 border-b border-slate-800 pb-2">
                        <i class="fa-solid fa-user-tag"></i> First Party (Seller / Owner)
                    </h3>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Seller Full Name *</label>
                        <input type="text" wire:model="sellerName" placeholder="e.g. Muhammad Aslam S/O Abdul Rasheed" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                        @error('sellerName') <span class="text-rose-400 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">National Identity Card (CNIC)</label>
                        <input type="text" wire:model="sellerCnic" placeholder="33100-xxxxxxx-x" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Mobile Number</label>
                        <input type="text" wire:model="sellerPhone" placeholder="03001234567" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                    </div>
                </div>

                <!-- Second Party: Buyer -->
                <div class="space-y-3 bg-slate-900/60 p-4 rounded-xl border border-slate-700/60">
                    <h3 class="text-xs font-bold text-emerald-400 uppercase flex items-center gap-1.5 border-b border-slate-800 pb-2">
                        <i class="fa-solid fa-user-check"></i> Second Party (Buyer)
                    </h3>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Buyer Full Name *</label>
                        <input type="text" wire:model="buyerName" placeholder="e.g. Tariq Javed S/O Nazir Ahmed" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                        @error('buyerName') <span class="text-rose-400 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">National Identity Card (CNIC)</label>
                        <input type="text" wire:model="buyerCnic" placeholder="33100-xxxxxxx-x" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Mobile Number</label>
                        <input type="text" wire:model="buyerPhone" placeholder="03007654321" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                    </div>
                </div>

            </div>

            <!-- Property Description -->
            <div class="space-y-3 border-b border-slate-700/80 pb-5">
                <h3 class="text-xs font-bold text-indigo-400 uppercase flex items-center gap-1.5">
                    <i class="fa-solid fa-map-location-dot"></i> Property & Plot Details
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Property Title / Details *</label>
                        <input type="text" wire:model="propertyTitle" placeholder="e.g. House No. 12, Street No. 4" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                        @error('propertyTitle') <span class="text-rose-400 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Society / Mauza / Town</label>
                        <input type="text" wire:model="schemeName" placeholder="e.g. Canal City" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Area & Size *</label>
                        <input type="text" wire:model="areaSize" placeholder="e.g. 5 Marla (1125 Sq Ft)" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                        @error('areaSize') <span class="text-rose-400 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Financials -->
            <div class="space-y-3 border-b border-slate-700/80 pb-5">
                <h3 class="text-xs font-bold text-amber-400 uppercase flex items-center gap-1.5">
                    <i class="fa-solid fa-coins"></i> Financial Terms & Amounts (PKR)
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Total Agreed Price *</label>
                        <input type="number" wire:model.live.debounce.300ms="totalDealAmount" placeholder="5000000" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                        @error('totalDealAmount') <span class="text-rose-400 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Paid Bayana / Advance Amount *</label>
                        <input type="number" wire:model.live.debounce.300ms="bayanaAmount" placeholder="500000" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-emerald-400 font-bold focus:outline-none focus:border-indigo-500">
                        @error('bayanaAmount') <span class="text-rose-400 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Remaining Balance Due</label>
                        <input type="text" readonly value="PKR {{ number_format($remainingAmount) }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-400 font-bold font-mono">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Payment Method</label>
                        <select wire:model="paymentMethod" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                            <option value="cash">Cash in Hand</option>
                            <option value="cheque">Bank Cheque / Pay Order</option>
                            <option value="online">Online Transfer</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Cheque Number / Bank Details (If applicable)</label>
                        <input type="text" wire:model="chequeDetails" placeholder="e.g. HBL Cheque No. 98765432" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Witnesses -->
            <div class="space-y-3">
                <h3 class="text-xs font-bold text-slate-300 uppercase flex items-center gap-1.5">
                    <i class="fa-solid fa-users"></i> Witnesses Details (Optional)
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" wire:model="witness1Name" placeholder="Witness 1 Name" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                        <input type="text" wire:model="witness1Cnic" placeholder="Witness 1 CNIC" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" wire:model="witness2Name" placeholder="Witness 2 Name" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                        <input type="text" wire:model="witness2Cnic" placeholder="Witness 2 CNIC" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold flex items-center gap-2 shadow-lg shadow-emerald-600/20 cursor-pointer">
                    <i class="fa-solid fa-eye"></i>
                    <span>Generate Agreement & Preview Print</span>
                </button>
            </div>
        </form>

    </div>

    @else
    <!-- ========================================== -->
    <!-- PRINTABLE LEGAL DOCUMENT PREVIEW (URDU VENDOR STYLE) -->
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

        <!-- The Traditional Urdu Stamp Paper / Affidavit Body -->
        <div class="bg-amber-50/90 p-8 sm:p-14 rounded-2xl shadow-2xl border-2 border-amber-900/30 print:border-0 print:shadow-none print:p-4 text-right" dir="rtl" style="font-family: 'Jameel Noori Nastaleeq', 'Noto Nastaliq Urdu', serif; line-height: 2.3;">
            
            <!-- Document Top Header -->
            <div class="text-center border-b-2 border-black pb-4 mb-6">
                <h1 class="text-3xl font-extrabold tracking-wide mb-1 text-amber-950">
                    {{ $agreementType === 'bayana' ? 'اقرار نامہ بیعانہ (معاہدہ فروخت اراضی)' : 'رسید ٹوکن بیعانہ سر دستی' }}
                </h1>
                <p class="text-sm font-sans font-bold">دفتر ایجنسی / ثالث: {{ Auth::user()->name }} | مورخه: {{ \Carbon\Carbon::parse($agreementDate)->format('d-m-Y') }}</p>
            </div>

            <!-- Agreement Text Body in Traditional Urdu Legal Tone -->
            <div class="text-base text-white space-y-4 text-justify">
                <p>
                    <strong>منکہ فریقِ اول (فروخت کنندہ / مالکانہ):</strong> جناب <strong>{{ $sellerName }}</strong>، 
                    ولدیت / زوجیت: _____________________، 
                    شناختی کارڈ نمبر: <span class="font-sans font-bold">{{ $sellerCnic ?: '_____________________' }}</span>، 
                    ساکن: _____________________, فون نمبر: <span class="font-sans">{{ $sellerPhone }}</span>۔
                </p>

                <p>
                    <strong>بہ حق فریقِ دوئم (خریدار):</strong> جناب <strong>{{ $buyerName }}</strong>، 
                    ولدیت: _____________________، 
                    شناختی کارڈ نمبر: <span class="font-sans font-bold">{{ $buyerCnic ?: '_____________________' }}</span>، 
                    ساکن: _____________________, فون نمبر: <span class="font-sans">{{ $buyerPhone }}</span>۔
                </p>

                <p class="border-t border-amber-900/30 pt-3">
                    چونکہ فریقِ اول اپنی ذاتی ملکیتی، منقولہ و غیر منقولہ تصرفی پراپرٹی واقع <strong>{{ $propertyTitle }}</strong>، 
                    موضع / سوسائٹی / اسکیم <strong>{{ $schemeName ?: 'علاقہ ہذا' }}</strong>، 
                    بقدر رقبہ <strong>{{ $areaSize }}</strong>، جملہ حقوقِ ملکیت سمیت فریقِ دوئم کے ہاتھ بابت کل طے شدہ رقم 
                    <strong>{{ number_format($totalDealAmount) }} روپے</strong> (نصف رقم بنتی ہے: {{ number_format($totalDealAmount / 2) }} روپے) کے عوض قطعی طور پر فروخت کرنے کا اقرار و وعدہ کیا ہے۔
                </p>

                <p>
                    جس میں سے فریقِ دوئم نے بطور بیعانہ / ایڈوانس سر دستی مبلغ 
                    <strong>{{ number_format($bayanaAmount) }} روپے</strong> 
                    بحساب ادائیگی <strong>({{ $paymentMethod === 'cash' ? 'نقد کیش' : ($paymentMethod === 'cheque' ? 'بینک چیک نمبر: ' . $chequeDetails : 'آن لائن بینک ٹرانسفر') }})</strong> 
                    روبرو گواہانِ حاشیہ فریقِ اول کو بخوبی ادا کر دیے ہیں، اور فریقِ اول نے بحق خود وصول پا کر تسلیم کر لیا ہے۔ 
                    بقیہ کل واجب الادا رقم مبلغ <strong>{{ number_format($remainingAmount) }} روپے</strong> 
                    مورخہ <strong>{{ \Carbon\Carbon::parse($finalPaymentDate)->format('d-m-Y') }}</strong> تک باقاعدہ ادا کر کے 
                    محکمہ مال سے رجسٹری، انتقالِ ملکیت اور موقع پر پکی قبضہ تحویل حاصل کرنے کا فریقِ دوئم کو مجاز اور حقدار بنایا گیا ہے۔
                </p>

                <!-- Traditional Legal Clauses -->
                <div class="bg-amber-100/60 p-5 rounded-xl border border-amber-900/20 text-sm space-y-2 my-4">
                    <p class="font-bold text-amber-950">شرائط و ضوابط معاہدہ (تکمیل بایاں):</p>
                    <ol class="list-decimal list-inside space-y-1 text-white">
                        <li>فریقِ اول اس امر کا مکمل پابند و ضامن ہوگا کہ مذکورہ پراپرٹی ہر قسم کے عدل و عدالت کے تنازعات، رہن، بینک قرضہ، کلیم یا مالیاتی واجبات سے بالکل پاک و صاف ہے۔</li>
                        <li>اگر خریدار (فریقِ دوئم) مقررہ تاریخ تک بقیہ زرِ ثمن ادا کرنے میں ناکام رہا تو جمع شدہ بیعانہ کی رقم ضبط تصور ہوگی، اور اگر فروخت کنندہ (فریقِ اول) اپنے وعدے سے انحراف یا پس و پیش کرے گا تو وہ وصول شدہ بیعانہ کی دوگنی (Double) رقم واپس ادا کرنے کا پابند ہوگا۔</li>
                        <li>یہ اقرار نامہ تحریر کرنے کا مقصد یہ ہے کہ بوقتِ ضرورت سند رہے اور بوقتِ رجسٹری کام آئے۔ یہ تحریر دونوں فریقین نے بقائمی ہوش و حواسِ خمسہ، بغیر کسی جبر و اکراہ، خوشی و رغبت کے ساتھ روبرو گواہانِ ذیل عمل میں لائی گئی۔</li>
                    </ol>
                </div>
            </div>

            <!-- Signatures Section -->
            <div class="grid grid-cols-2 gap-12 pt-14 text-center" style="font-family: 'Jameel Noori Nastaleeq', 'Noto Nastaliq Urdu', serif; line-height: 2.3;">
                <div class="border-t-2 border-black pt-2">
                    <p class="font-bold text-sm">دستخط / نشان انگوٹھا فریقِ اول (فروخت کنندہ)</p>
                    <p class="text-xs text-gray-700 mt-1 font-serif text-base font-bold">{{ $sellerName }}</p>
                </div>
                <div class="border-t-2 border-black pt-2">
                    <p class="font-bold text-sm">دستخط / نشان انگوٹھا فریقِ دوئم (خریدار)</p>
                    <p class="text-xs text-gray-700 mt-1 font-serif text-base font-bold">{{ $buyerName }}</p>
                </div>
            </div>

            <!-- Witnesses Signatures -->
            <div class="grid grid-cols-3 gap-6 pt-12 text-center text-xs"  style="font-family: 'Jameel Noori Nastaleeq', 'Noto Nastaliq Urdu', serif; line-height: 2.3;">
                <div class="border-t border-gray-600 pt-2">
                    <p class="font-bold">گواہ شد (1)</p>
                    <p class="text-gray-800 font-bold">{{ $witness1Name ?: '________________' }}</p>
                    <p class="text-gray-600 font-mono text-[11px]">{{ $witness1Cnic ?: 'CNIC: _____________' }}</p>
                </div>
                <div class="border-t border-gray-600 pt-2">
                    <p class="font-bold text-emerald-800">تصدق و تصدیق (ثالث / ڈیلر)</p>
                    <p class="text-emerald-700 font-bold font-serif text-sm">{{ Auth::user()->name }}</p>
                    <p class="text-gray-600 font-mono text-[11px]">{{ Auth::user()->phone }}</p>
                </div>
                <div class="border-t border-gray-600 pt-2">
                    <p class="font-bold">گواہ شد (2)</p>
                    <p class="text-gray-800 font-bold">{{ $witness2Name ?: '________________' }}</p>
                    <p class="text-gray-600 font-mono text-[11px]">{{ $witness2Cnic ?: 'CNIC: _____________' }}</p>
                </div>
            </div>

            <!-- Portal Stamp Watermark at Footer -->
            <div class="mt-10 pt-3 border-t border-amber-900/30 text-center text-[11px] text-gray-600">
                دستاویز تیار کردہ بذریعہ AssanZameen.com پورٹل | قانونی ڈیل ریکارڈ برائے اسٹامپ اینڈ کچہری
            </div>

        </div>

    </div>
    @endif

</div>