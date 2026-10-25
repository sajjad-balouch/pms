<div class="min-h-screen bg-[#070a13] text-slate-100 py-10 px-4 sm:px-6 lg:px-8 w-full overflow-x-hidden">

    <style>
        .rent-wrapper {
            display: flex;
            flex-direction: column;
            gap: 2rem;
            width: 100%;
        }
        @media (min-width: 1024px) {
            .rent-wrapper {
                flex-direction: row;
                align-items: flex-start;
            }
            .rent-form-col {
                width: 42% !important;
                flex-shrink: 0;
            }
            .rent-preview-col {
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

            #printable-rent-deed {
                display: block !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 15mm 15mm 15mm !important;
                background: #ffffff !important;
                color: #000000 !important;
                border: none !important;
                box-shadow: none !important;
                font-family: 'Jameel Noori Nastaleeq', 'Noto Nastaliq Urdu', serif !important;
                line-height: 2.3 !important;
            }

            .e-stamp-blank-header {
                display: block !important;
                height: 4.5in !important;
            }

            #printable-rent-deed p, #printable-rent-deed span, #printable-rent-deed div, #printable-rent-deed h1, #printable-rent-deed h2 {
                color: #000000 !important;
            }

            @page {
                size: legal portrait;
                margin: 10mm;
            }
        }
    </style>

    <div class="max-w-7xl mx-auto space-y-8 w-full">

        <!-- Tool Header Banner (No-Print) -->
        <div class="text-center space-y-3 no-print w-full">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 text-xs font-bold uppercase tracking-wider">
                <i class="fa-solid fa-file-contract text-sm"></i>
                <span>Punjab Tenancy Act 2009 Compliant Deed Studio</span>
            </div>
            
            <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight" style="font-family: 'Noto Nastaliq Urdu', 'Noto Sans Arabic', serif; line-height: 1.8;">
                خودکار کرایہ نامہ و قانونی ٹیننسی معاہدہ میکر
            </h1>
            
            <p class="text-sm text-slate-400 max-w-2xl mx-auto leading-relaxed">
                مکان، دکان یا کمرشل پورشن کرایہ پر دینے یا لینے کے لیے عدالت و تھانہ تصدیق شدہ معاہدہ۔ ای-اسٹامپ پیپر اور سیکیورٹی ڈپازٹ شرائط کے ساتھ فوری پرنٹ۔
            </p>
        </div>

        <div class="rent-wrapper w-full">

            <!-- LEFT COLUMN: INPUT WIZARD (No-Print) -->
            <div class="rent-form-col w-full bg-slate-900/90 rounded-3xl border border-slate-800 p-6 shadow-2xl space-y-5 no-print">
                <h3 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2 border-b border-slate-800 pb-3">
                    <i class="fa-solid fa-pen-fancy text-amber-400"></i>
                    <span>معاہدہ کے کوائف درج کریں</span>
                </h3>

                <!-- Landlord (مالک مکان) -->
                <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-3">
                    <span class="text-xs font-bold text-amber-400 uppercase tracking-wider block">فریقِ اول (مالکِ جائیداد / Landlord)</span>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" wire:model.live="landlord_name" placeholder="مالک کا نام" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white">
                        <input type="text" wire:model.live="landlord_father" placeholder="ولدیت" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" wire:model.live="landlord_cnic" placeholder="شناختی کارڈ نمبر" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono">
                        <input type="text" wire:model.live="landlord_phone" placeholder="موبائل نمبر" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono">
                    </div>
                    <input type="text" wire:model.live="landlord_address" placeholder="مالک کا مستقل پتہ" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white">
                </div>

                <!-- Tenant (کرایہ دار) -->
                <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-3">
                    <span class="text-xs font-bold text-cyan-400 uppercase tracking-wider block">فریقِ دوم (کرایہ دار / Tenant)</span>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" wire:model.live="tenant_name" placeholder="کرایہ دار کا نام" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white">
                        <input type="text" wire:model.live="tenant_father" placeholder="ولدیت" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" wire:model.live="tenant_cnic" placeholder="شناختی کارڈ نمبر" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono">
                        <input type="text" wire:model.live="tenant_phone" placeholder="موبائل نمبر" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono">
                    </div>
                    <input type="text" wire:model.live="tenant_permanent_address" placeholder="کرایہ دار کا آبائی / مستقل پتہ" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white">
                </div>

                <!-- Property Details -->
                <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-3">
                    <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider block">کرایہ پر دی جانے والی جگہ</span>
                    <div class="grid grid-cols-2 gap-2">
                        <select wire:model.live="property_type" class="bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white">
                            <option value="residential">رہائشی مکان (House)</option>
                            <option value="commercial">کمرشل دکان (Shop)</option>
                            <option value="portion">بالائی / نچلا پورشن</option>
                            <option value="warehouse">گودام / فیکٹری ہال</option>
                        </select>
                        <input type="text" wire:model.live="city" placeholder="شہر" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white">
                    </div>
                    <input type="text" wire:model.live="property_title" placeholder="مختصر تفصیل (مثلاً 5 مرلہ سنگل سٹوری مکان)" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white">
                    <input type="text" wire:model.live="property_address" placeholder="جائیداد کا مکمل موقع پتہ معہ گلی و پلاٹ نمبر" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white">
                </div>

                <!-- Rent, Security & Terms -->
                <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-3">
                    <span class="text-xs font-bold text-amber-400 uppercase tracking-wider block">مالی شرائط و مدتِ معاہدہ</span>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[10px] text-slate-400 mb-0.5">ماہوار کرایہ (PKR)</label>
                            <input type="number" wire:model.live="monthly_rent" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-emerald-400 font-mono font-bold">
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-400 mb-0.5">سیکیورٹی ڈپازٹ (PKR)</label>
                            <input type="number" wire:model.live="security_deposit" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-amber-400 font-mono font-bold">
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <label class="block text-[10px] text-slate-400 mb-0.5">مدت (ماہ)</label>
                            <input type="number" wire:model.live="tenancy_tenure_months" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono text-center">
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-400 mb-0.5">سالانہ اضافہ %</label>
                            <input type="number" wire:model.live="annual_increment_percentage" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono text-center">
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-400 mb-0.5">کرایہ کی تاریخ</label>
                            <input type="number" min="1" max="28" wire:model.live="rent_due_day" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono text-center">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-1">
                        <div>
                            <label class="block text-[10px] text-slate-400 mb-0.5">شروع معاہدہ</label>
                            <input type="date" wire:model.live="start_date" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-white font-mono">
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-400 mb-0.5">اختتام معاہدہ</label>
                            <input type="date" wire:model.live="end_date" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-amber-400 font-mono font-bold" readonly>
                        </div>
                    </div>
                </div>

                <!-- Utilities Reading -->
                <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-3">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">یوٹیلیٹی میٹرز ریفرنس</span>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" wire:model.live="electricity_meter_no" placeholder="بجلی کا میٹر نمبر" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-white font-mono">
                        <input type="text" wire:model.live="current_electricity_reading" placeholder="موجودہ ریڈنگ یونٹس" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-white font-mono">
                    </div>
                </div>

                <!-- Witnesses -->
                <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-3">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">گواہان</span>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" wire:model.live="witness1_name" placeholder="گواہ 1 معہ ولدیت" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-white">
                        <input type="text" wire:model.live="witness1_cnic" placeholder="شناختی کارڈ" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-white font-mono">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" wire:model.live="witness2_name" placeholder="گواہ 2 معہ ولدیت" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-white">
                        <input type="text" wire:model.live="witness2_cnic" placeholder="شناختی کارڈ" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-white font-mono">
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: OFFICIAL PREVIEW & PRINT -->
            <div class="rent-preview-col w-full space-y-5">
                
                <!-- Action Controls (No-Print) -->
                <div id="action-bar" class="no-print flex items-center justify-between gap-3 p-4 bg-slate-900 rounded-2xl border border-slate-800 w-full">
                    <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-300">
                        <input type="checkbox" wire:model.live="leave_stamp_space" class="w-4 h-4 rounded text-cyan-500 bg-slate-950 border-slate-700">
                        <span class="font-bold">ای-اسٹامپ پیپر پرنٹ موڈ (اوپر 4.5 انچ جگہ خالی چھوڑیں)</span>
                    </label>

                    <button onclick="window.print()" class="px-5 py-2.5 bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-400 text-slate-950 font-black rounded-xl text-xs transition flex items-center gap-2 shadow-lg shadow-cyan-500/20 cursor-pointer">
                        <i class="fa-solid fa-print"></i>
                        <span>کرایہ نامہ پرنٹ / PDF</span>
                    </button>
                </div>

                <!-- Printable Document -->
                <div id="printable-rent-deed" class="bg-slate-950/80 rounded-3xl border border-slate-800 p-8 sm:p-12 shadow-2xl text-slate-200 text-right leading-loose relative" dir="rtl" style="font-family: 'Jameel Noori Nastaleeq', 'Noto Nastaliq Urdu', serif; line-height: 2.3; font-size: 1.05rem;">
                    
                    @if($leave_stamp_space)
                        <div class="e-stamp-blank-header hidden print:block text-center text-xs text-slate-400 font-sans border-b border-dashed border-slate-300 mb-6 pb-2" style="height: 4.5in;">
                            <span class="text-slate-400 text-[10px]">[پنجاب ای-اسٹامپ پیپر بارکوڈ و چالان پرنٹنگ سپیس]</span>
                        </div>
                    @endif

                    <div class="text-center space-y-1 pb-6 border-b border-slate-800 print:border-slate-400">
                        <h2 class="text-2xl sm:text-3xl font-black text-cyan-400 print:text-black">
                            معاہدہ کرایہ داری (Rent Agreement)
                        </h2>
                        <span class="text-xs text-slate-400 font-sans print:text-black">بمطابق پنجاب رینٹڈ پریمائزز ایکٹ 2009 (Punjab Rented Premises Act)</span>
                    </div>

                    <div class="space-y-4 pt-6">
                        <p>
                            <strong>فریقِ اول (مالکِ مکان / دکان):</strong> مسمی <strong>{{ $landlord_name }}</strong> ولد <strong>{{ $landlord_father }}</strong>، شناختی کارڈ نمبر <span class="font-mono font-bold">{{ $landlord_cnic }}</span>، ساکن {{ $landlord_address }}، رابطہ نمبر {{ $landlord_phone }}۔
                        </p>

                        <p class="text-center font-bold text-cyan-400 print:text-black">۔۔۔ بمقابلہ ۔۔۔</p>

                        <p>
                            <strong>فریقِ دوم (کرایہ دار / مستاجر):</strong> مسمی <strong>{{ $tenant_name }}</strong> ولد <strong>{{ $tenant_father }}</strong>، شناختی کارڈ نمبر <span class="font-mono font-bold">{{ $tenant_cnic }}</span>، مستقل ساکن {{ $tenant_permanent_address }}، رابطہ نمبر {{ $tenant_phone }}۔
                        </p>

                        <p class="pt-2">
                            جو کہ فریقِ اول جائیداد واقع <strong>{{ $property_address }}</strong> ({{ $property_title }}) کا مکمل مالک و قابض قانونی ہے۔ اور فریقِ اول نے مذکورہ جائیداد فریقِ دوم کو مندرجہ ذیل شرائط و طے شدہ معاہدہ کے تحت کرایہ پر دینا قبول کیا ہے۔
                        </p>

                        <!-- Rent & Tenure Snapshot -->
                        <div class="p-4 bg-slate-900 print:bg-slate-100 rounded-2xl border border-slate-800 print:border-slate-400 space-y-1.5 my-3">
                            <p><strong>ماہوار کرایہ:</strong> مبلغ <strong>PKR {{ number_format($monthly_rent) }}</strong> روپے (ماہانہ ادا ہوگا۔)</p>
                            <p><strong>سیکیورٹی ڈپازٹ (ضمانت):</strong> مبلغ <strong>PKR {{ number_format($security_deposit) }}</strong> روپے نقد بحق فریقِ اول بطور امانت جمع کرا دیا گیا ہے۔</p>
                            <p><strong>مدتِ معاہدہ:</strong> کل <strong>{{ $tenancy_tenure_months }} ماہ</strong>، از تاریخ <span class="font-mono font-bold">{{ \Carbon\Carbon::parse($start_date)->format('d-m-Y') }}</span> تا تاریخ <span class="font-mono font-bold">{{ \Carbon\Carbon::parse($end_date)->format('d-m-Y') }}</span>۔</p>
                        </div>

                        <!-- Legal Strict Clauses -->
                        <h4 class="font-bold pt-2 text-white print:text-black">باہمی طے شدہ شرائط و قانونی شقیں:</h4>
                        <ol class="list-decimal list-inside space-y-2 text-sm leading-relaxed pr-2">
                            <li>
                                <strong>ادائیگی کرایہ:</strong> فریقِ دوم ہر ماہ کا کرایہ انگریزی تاریخ کی <strong>{{ $rent_due_day }} تاریخ</strong> تک فریقِ اول کو پیشگی ادا کرنے کا پابند ہوگا۔ بصورتِ تاخیر فریقِ اول کو قانونی کارروائی کا حق حاصل ہوگا۔
                            </li>
                            <li>
                                <strong>واپسی سیکیورٹی:</strong> سیکیورٹی کی رقم غیر سودی امانت ہے جو مدتِ معاہدہ ختم ہونے اور مکان خالی کرنے پر تمام بلز و بقایا جات منہا کرنے کے بعد فریقِ دوم کو واپس کر دی جائے گی۔
                            </li>
                            <li>
                                <strong>یوٹیلیٹی بلز کی ادائیگی:</strong> بجلی (میٹر نمبر: <span class="font-mono">{{ $electricity_meter_no ?: 'حسبِ موقع' }}</span>)، سوئی گیس، پانی و صفائی کا کرایہ بل بلاتعطل فریقِ دوم ادا کرے گا اور بل کی ادا شدہ اصل رسید فریقِ اول کو دکھائے گا۔
                            </li>
                            <li>
                                <strong>سالانہ اضافہ:</strong> اگر فریقین کی باہمی رضامندی سے معاہدہ کی تجدید کی گئی تو ماہانہ کرایہ میں <strong>{{ $annual_increment_percentage }} فیصد</strong> سالانہ کی شرح سے اضافہ لاگو ہوگا۔
                            </li>
                            <li>
                                <strong>ممنوعہ استعمال و سب لیٹنگ:</strong> فریقِ دوم کرایہ پر لی گئی جائیداد کو کسی دوسرے شخص کو آگے کرایہ (Sub-let) پر دینے کا ہرگز مجاز نہ ہوگا، نہ ہی کسی غیر قانونی، غیر اخلاقی یا شرپسندانہ مقصد کے لیے استعمال کرے گا۔
                            </li>
                            <li>
                                <strong>تھانہ اندراج (Police Verification):</strong> فریقِ دوم پابند ہوگا کہ وہ نیشنل ایکشن پلان کے تحت متعلقہ تھانہ میں اپنا کرایہ داری اندراج اور اندراج فارم 7 روز کے اندر جمع کروائے گا۔
                            </li>
                            <li>
                                <strong>خالی کروانے کا نوٹس:</strong> اگر کوئی فریق مدت ختم ہونے سے قبل معاہدہ منسوخ کرنا چاہے تو وہ دوسرے فریق کو <strong>ایک ماہ کا پیشگی تحریری نوٹس</strong> دینے کا پابند ہوگا۔
                            </li>
                        </ol>

                        <p class="pt-4">
                            لہٰذا یہ اقرار نامہ کرایہ داری دونوں فریقین نے بہ ہوش و حواسِ خمسہ، روبرو گواہانِ حاشیہ تحریر و ثبت کر دیا ہے تاکہ سند رہے۔
                        </p>

                        <!-- Signatures Grid -->
                        <div class="pt-10 grid grid-cols-2 gap-8 text-center text-xs">
                            <div class="border-t border-slate-700 print:border-black pt-3">
                                <span class="font-bold block text-sm">{{ $landlord_name }}</span>
                                <span class="text-slate-400 print:text-black">دستخط و نشان انگوٹھا (فریقِ اول / مالک)</span>
                            </div>

                            <div class="border-t border-slate-700 print:border-black pt-3">
                                <span class="font-bold block text-sm">{{ $tenant_name }}</span>
                                <span class="text-slate-400 print:text-black">دستخط و نشان انگوٹھا (فریقِ دوم / کرایہ دار)</span>
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