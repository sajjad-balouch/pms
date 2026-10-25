<div class="min-h-screen bg-[#070a13] text-slate-100 py-10 px-4 sm:px-6 lg:px-8 w-full overflow-x-hidden">

    <!-- Print & e-Stamp Paper Clearance Styling -->
    <style>
        .deed-wrapper {
            display: flex;
            flex-direction: column;
            gap: 2rem;
            width: 100%;
        }
        @media (min-width: 1024px) {
            .deed-wrapper {
                flex-direction: row;
                align-items: flex-start;
            }
            .deed-form-col {
                width: 42% !important;
                flex-shrink: 0;
            }
            .deed-preview-col {
                width: 58% !important;
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
            }

            #printable-deed {
                display: block !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 15mm 15mm 15mm !important;
                background: #ffffff !important;
                color: #000000 !important;
                border: none !important;
                box-shadow: none !important;
                font-family: 'Jameel Noori Nastaleeq', 'Noto Nastaliq Urdu', serif !important;
                line-height: 2.4 !important;
            }

            .e-stamp-blank-header {
                display: block !important;
                height: 4.8in !important; /* Standard e-Stamp 4.5 inch barcode clearance */
            }

            #printable-deed p, #printable-deed span, #printable-deed div, #printable-deed h1, #printable-deed h2 {
                color: #000000 !important;
            }

            @page {
                size: legal portrait; /* e-Stamp Paper Legal Size hota hai */
                margin: 10mm;
            }
        }
    </style>

    <div class="max-w-7xl mx-auto space-y-8 w-full">

        <!-- Tool Header Banner (No-Print) -->
        <div class="text-center space-y-3 no-print w-full">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-indigo-500/10 border border-indigo-500/30 text-indigo-400 text-xs font-bold uppercase tracking-wider">
                <i class="fa-solid fa-file-signature text-sm"></i>
                <span>Automated Legal Stamp Paper Deed Builder</span>
            </div>
            
            <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight" style="font-family: 'Noto Nastaliq Urdu', 'Noto Sans Arabic', serif; line-height: 1.8;">
                خودکار اقرار نامہ بیعانہ میکر
            </h1>
            
            <p class="text-sm text-slate-400 max-w-2xl mx-auto leading-relaxed">
                پلاٹ، مکان یا زرعی رقبے کی خرید و فروخت پر کچہری وثیقہ نویس کے چکروں سے نجات۔ تمام قانونی شقوں کے ساتھ ای-اسٹامپ پیپر پر پرنٹ ہونے والا باضابطہ معاہدہ بیعانہ۔
            </p>
        </div>

        <div class="deed-wrapper w-full">

            <!-- LEFT COLUMN: INPUT WIZARD (No-Print) -->
            <div class="deed-form-col w-full bg-slate-900/90 rounded-3xl border border-slate-800 p-6 shadow-2xl space-y-5 no-print">
                <h3 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2 border-b border-slate-800 pb-3">
                    <i class="fa-solid fa-pen-nib text-amber-400"></i>
                    <span>اقرار نامہ کے کوائف درج کریں</span>
                </h3>

                <!-- Seller (بائع) -->
                <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-3">
                    <span class="text-xs font-bold text-amber-400 uppercase tracking-wider block">فریقِ اول (فروخت کنندہ / مالک)</span>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" wire:model.live="seller_name" placeholder="مالک کا نام" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white">
                        <input type="text" wire:model.live="seller_father" placeholder="ولدیت" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" wire:model.live="seller_cnic" placeholder="شناختی کارڈ نمبر" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono">
                        <input type="text" wire:model.live="seller_phone" placeholder="موبائل نمبر" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono">
                    </div>
                    <input type="text" wire:model.live="seller_address" placeholder="مکمل رہائشی پتہ" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white">
                </div>

                <!-- Buyer (مشتری) -->
                <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-3">
                    <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider block">فریقِ دوم (خریدار / مشتری)</span>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" wire:model.live="buyer_name" placeholder="خریدار کا نام" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white">
                        <input type="text" wire:model.live="buyer_father" placeholder="ولدیت" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" wire:model.live="buyer_cnic" placeholder="شناختی کارڈ نمبر" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono">
                        <input type="text" wire:model.live="buyer_phone" placeholder="موبائل نمبر" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono">
                    </div>
                    <input type="text" wire:model.live="buyer_address" placeholder="مکمل رہائشی پتہ" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white">
                </div>

                <!-- Property Particulars -->
                <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-3">
                    <span class="text-xs font-bold text-sky-400 uppercase tracking-wider block">جائیداد کی تفصیل</span>
                    <input type="text" wire:model.live="property_title" placeholder="پلاٹ / مکان / دکان کی تفصیل" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white">
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" wire:model.live="area_size" placeholder="رقبہ (مثلاً 5 مرلہ)" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white">
                        <input type="text" wire:model.live="city" placeholder="شہر / ضلع" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white">
                    </div>
                    <input type="text" wire:model.live="location_mouza" placeholder="موضع / ہاؤسنگ سکیم کا پتہ" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white">
                    <input type="text" wire:model.live="khasra_khewat" placeholder="کھیوٹ / خسرہ نمبر (اختیاری)" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white">
                </div>

                <!-- Financials & Timeline -->
                <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-3">
                    <span class="text-xs font-bold text-amber-400 uppercase tracking-wider block">مالی لین دین و ٹائم لائن</span>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[10px] text-slate-400 mb-0.5">کل طے شدہ رقم (PKR)</label>
                            <input type="number" wire:model.live="total_amount" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold">
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-400 mb-0.5">ادا شدہ بیعانہ (PKR)</label>
                            <input type="number" wire:model.live="bayana_advance_amount" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-emerald-400 font-mono font-bold">
                        </div>
                    </div>

                    <div class="p-2.5 bg-slate-900 rounded-xl border border-slate-800 flex justify-between text-xs font-mono">
                        <span class="text-slate-400">بقایا واجب الادا رقم:</span>
                        <span class="text-amber-400 font-black">PKR {{ number_format($remaining_amount) }}</span>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[10px] text-slate-400 mb-0.5">طریقہ ادائیگی</label>
                            <select wire:model.live="payment_method" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white">
                                <option value="cash">نقد (Cash in Hand)</option>
                                <option value="pay_order">پے آرڈر (Pay Order)</option>
                                <option value="cheque">بینک چیک (Bank Cheque)</option>
                                <option value="online">آن لائن ٹرانسفر (Online Transfer)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-400 mb-0.5">حوالہ نمبر / تفصیل</label>
                            <input type="text" wire:model.live="payment_reference" placeholder="چیک یا پے آرڈر نمبر" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-1">
                        <div>
                            <label class="block text-[10px] text-slate-400 mb-0.5">معاہدہ کی تاریخ</label>
                            <input type="date" wire:model.live="deed_date" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-white font-mono">
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-400 mb-0.5">رجسٹری کی آخری تاریخ</label>
                            <input type="date" wire:model.live="target_registry_date" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-amber-400 font-mono font-bold">
                        </div>
                    </div>
                </div>

                <!-- Witnesses (گواہان) -->
                <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-3">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">گواہان کے کوائف</span>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" wire:model.live="witness1_name" placeholder="گواہ 1 کا نام معہ ولدیت" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-white">
                        <input type="text" wire:model.live="witness1_cnic" placeholder="گواہ 1 شناختی کارڈ" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-white font-mono">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" wire:model.live="witness2_name" placeholder="گواہ 2 کا نام معہ ولدیت" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-white">
                        <input type="text" wire:model.live="witness2_cnic" placeholder="گواہ 2 شناختی کارڈ" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-white font-mono">
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: CERTIFIED LEGAL DEED PREVIEW -->
            <div class="deed-preview-col w-full space-y-5">
                
                <!-- Action Controls (No-Print) -->
                <div id="action-bar" class="no-print flex items-center justify-between gap-3 p-4 bg-slate-900 rounded-2xl border border-slate-800 w-full">
                    
                    <!-- Toggle e-Stamp Paper Header Space -->
                    <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-300">
                        <input type="checkbox" wire:model.live="leave_stamp_space" class="w-4 h-4 rounded text-amber-500 bg-slate-950 border-slate-700">
                        <span class="font-bold">ای-اسٹامپ پیپر پرنٹ موڈ (اوپر 4.5 انچ جگہ خالی چھوڑیں)</span>
                    </label>

                    <div class="flex items-center gap-2">
                        <button onclick="window.print()" class="px-5 py-2.5 bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 text-slate-950 font-black rounded-xl text-xs transition flex items-center gap-2 shadow-lg shadow-amber-500/20 cursor-pointer">
                            <i class="fa-solid fa-print"></i>
                            <span>اسٹامپ پرنٹ / PDF ڈاؤن لوڈ</span>
                        </button>
                    </div>
                </div>

                <!-- Main Printable Legal Stamp Document -->
                <div id="printable-deed" class="bg-slate-950/80 rounded-3xl border border-slate-800 p-8 sm:p-12 shadow-2xl text-slate-200 text-right leading-loose relative" dir="rtl" style="font-family: 'Jameel Noori Nastaleeq', 'Noto Nastaliq Urdu', serif; line-height: 2.3; font-size: 1.05rem;">
                    
                    <!-- e-Stamp Paper Clearance Header (Only prints if enabled) -->
                    @if($leave_stamp_space)
                        <div class="e-stamp-blank-header hidden print:block text-center text-xs text-slate-400 font-sans border-b border-dashed border-slate-300 mb-6 pb-2" style="height: 4.5in;">
                            <span class="text-slate-400 text-[10px]">[پنجاب ای-اسٹامپ پیپر بارکوڈ اور چالان کی جگہ برائے پرنٹنگ]</span>
                        </div>
                    @endif

                    <!-- Document Title -->
                    <div class="text-center space-y-1 pb-6 border-b border-slate-800 print:border-slate-400">
                        <h2 class="text-2xl sm:text-3xl font-black text-amber-400 print:text-black">
                            اقرار نامہ بیعانہ جائیداد غیر منقولہ
                        </h2>
                        <span class="text-xs text-slate-400 font-sans print:text-black">بمطابق مروجہ کنٹریکٹ ایکٹ 1872 و شیڈول پنجاب لینڈ ریونیو</span>
                    </div>

                    <!-- Fariqain Declaration -->
                    <div class="space-y-4 pt-6">
                        <p>
                            <strong>فریقِ اول (بائع / فروخت کنندہ):</strong> مسمی <strong>{{ $seller_name }}</strong> ولد <strong>{{ $seller_father }}</strong>، حامل قومی شناختی کارڈ نمبر <span class="font-mono font-bold">{{ $seller_cnic }}</span>، ساکن {{ $seller_address }}۔
                        </p>

                        <p class="text-center font-bold text-amber-400 print:text-black">۔۔۔ بمقابلہ ۔۔۔</p>

                        <p>
                            <strong>فریقِ دوم (مشتری / خریدار):</strong> مسمی <strong>{{ $buyer_name }}</strong> ولد <strong>{{ $buyer_father }}</strong>، حامل قومی شناختی کارڈ نمبر <span class="font-mono font-bold">{{ $buyer_cnic }}</span>، ساکن {{ $buyer_address }}۔
                        </p>

                        <p class="pt-2">
                            جو کہ فریقِ اول جائیداد مندرجہ ذیل کا بلا شرکتِ غیرے مالک و قابض قانونی ہے اور ہر قسم کے تنازعات، رہن، بار، عدالتی اسٹے آرڈر اور جعلی دعویٰ جات سے پاک و صاف ہونے کا حلفاً اقرار کرتا ہے۔
                        </p>

                        <!-- Property Details Box -->
                        <div class="p-4 bg-slate-900 print:bg-slate-100 rounded-2xl border border-slate-800 print:border-slate-400 space-y-1.5 my-3">
                            <p><strong>تفصیلِ جائیداد:</strong> {{ $property_title }}، واقع {{ $location_mouza }}، {{ $city }}۔</p>
                            <p><strong>رقبہ:</strong> {{ $area_size }}۔ {{ $khasra_khewat ? "({$khasra_khewat})" : "" }}</p>
                        </div>

                        <!-- Financials -->
                        <p>
                            اب فریقِ اول نے مذکورہ جائیداد روبرو گواہان فریقِ دوم کے ہاتھ بمبلغ <strong>PKR {{ number_format($total_amount) }}</strong> روپے کے عوض قطعی فروخت کرنے کا سودا طے پایا ہے۔ جس میں سے فریقِ اول نے مبلغ <strong>PKR {{ number_format($bayana_advance_amount) }}</strong> روپے بطورِ پیشگی بیعانہ (Token / Advance Money) ذریعہ <em>{{ $payment_method === 'cash' ? 'نقد' : ($payment_method === 'pay_order' ? 'پے آرڈر' : 'بینک چیک') }} {{ $payment_reference ? "({$payment_reference})" : "" }}</em> فریقِ دوم سے وصول پا کر مکمل رسید حاصل کر لی ہے۔
                        </p>

                        <p>
                            جبکہ بقیہ واجب الادا رقم بمبلغ <strong>PKR {{ number_format($remaining_amount) }}</strong> روپے بوقتِ رجسٹری و بائیو میٹرک انتقال مورخہ <strong class="font-mono">{{ \Carbon\Carbon::parse($target_registry_date)->format('d-m-Y') }}</strong> تک فریقِ دوم فریقِ اول کو ادا کرنے کا پابند ہوگا۔
                        </p>

                        <!-- Legal Strict Clauses -->
                        <h4 class="font-bold pt-2 text-white print:text-black">شرائط و قانونی نظائر:</h4>
                        <ol class="list-decimal list-inside space-y-2 text-sm leading-relaxed pr-2">
                            <li>
                                <strong>ہرجانہ و ضبطی بیعانہ:</strong> اگر فریقِ دوم (خریدار) مقررہ تاریخ یعنی <span class="font-mono">{{ $target_registry_date }}</span> تک بقیہ رقم کی ادائیگی میں ناکام رہا یا بلا جواز سودے سے مکر گیا تو اس کا دیا گیا پیشگی بیعانہ بحق فریقِ اول ضبط متصور ہوگا اور معاہدہ منسوخ سمجھا جائے گا۔
                            </li>
                            <li>
                                <strong>بیچنے والے کا دوگنا تاوان (2X Penalty):</strong> اگر فریقِ اول مقررہ تاریخ تک رجسٹری و بائیو میٹرک انتقال کروانے سے انکاری ہوا یا ٹائٹل میں کوئی قانونی سقم نکلا، تو فریقِ اول اصل بیعانہ واپس کرنے کے علاوہ اتنی ہی رقم بطور ہرجانہ (یعنی دوگنا بیعانہ) فریقِ دوم کو ادا کرنے کا پابند ہوگا۔ بصورتِ دیگر فریقِ دوم کو عدالت کے ذریعے تعمیلِ مختص (Specific Performance) کا مکمل حق حاصل ہوگا۔
                            </li>
                            <li>
                                <strong>اخراجاتِ رجسٹری:</strong> رجسٹری، کمپیوٹرائزڈ فرد، انتقال، اسٹامپ ڈیوٹی، ٹی ایم اے اور خریدار کا ودہولڈنگ ٹیکس (236-K) فریقِ دوم کے ذمہ ہوگا، جبکہ فروخت کنندہ کا انکم ٹیکس (236-C) فریقِ اول خود ادا کرے گا۔
                            </li>
                            <li>
                                <strong>حوالگی قبضہ:</strong> جائیداد کا موقع پر فزیکل قبضہ بقیہ رقم کی مکمل وصولی اور رجسٹری کے ساتھ ہی فریقِ دوم کے سپرد کیا جائے گا۔
                            </li>
                        </ol>

                        <p class="pt-4">
                            لہٰذا یہ اقرار نامہ بیعانہ دونوں فریقین نے بہ ہوش و حواسِ خمسہ، بلا جبر و اکراہ، روبرو گواہانِ حاشیہ تحریر و ثبت کر دیا ہے تاکہ سند رہے اور بوقتِ ضرورت کام آوے۔
                        </p>

                        <p class="text-left font-mono text-xs pt-2">
                            تحریر بتاریخ: {{ \Carbon\Carbon::parse($deed_date)->format('d F, Y') }}
                        </p>

                        <!-- Signatures & Thumb Impressions Table -->
                        <div class="pt-10 grid grid-cols-2 gap-8 text-center text-xs">
                            <div class="border-t border-slate-700 print:border-black pt-3">
                                <span class="font-bold block text-sm">{{ $seller_name }}</span>
                                <span class="text-slate-400 print:text-black">دستخط و نشان انگوٹھا (فریقِ اول / بائع)</span>
                            </div>

                            <div class="border-t border-slate-700 print:border-black pt-3">
                                <span class="font-bold block text-sm">{{ $buyer_name }}</span>
                                <span class="text-slate-400 print:text-black">دستخط و نشان انگوٹھا (فریقِ دوم / مشتری)</span>
                            </div>
                        </div>

                        <!-- Witnesses Signatures -->
                        <div class="pt-8 grid grid-cols-2 gap-8 text-center text-xs">
                            <div class="border-t border-slate-700 print:border-black pt-3">
                                <span class="font-bold block">{{ $witness1_name ?: 'گواہ شد نمبر 1' }}</span>
                                <span class="text-[10px] font-mono text-slate-400 print:text-black">{{ $witness1_cnic }}</span>
                            </div>

                            <div class="border-t border-slate-700 print:border-black pt-3">
                                <span class="font-bold block">{{ $witness2_name ?: 'گواہ شد نمبر 2' }}</span>
                                <span class="text-[10px] font-mono text-slate-400 print:text-black">{{ $witness2_cnic }}</span>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>
</div>