<div class="min-h-screen bg-[#070a13] text-slate-100 py-10 px-4 sm:px-6 lg:px-8" style="font-family: 'Noto Nastaliq Urdu', 'Noto Sans Arabic', serif; line-height: 1.8;">

    <!-- Print Media Query Styling -->
    <style>
        @media print {
            /* 1. Website ke tamaam navigation, header, footer aur buttons chhupayein */
            header, footer, nav, .glass-nav, button, 
            .no-print, [wire\:loading], #action-bar {
                display: none !important;
            }

            /* 2. Pure white background aur crisp black text */
            body, html {
                background-color: #ffffff !important;
                color: #000000 !important;
                padding: 0 !important;
                margin: 0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* 3. Sirf report container print ho */
            #printable-report {
                display: block !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 15px !important;
                background: #ffffff !important;
                color: #000000 !important;
                border: 2px solid #0f172a !important;
                border-radius: 16px !important;
                box-shadow: none !important;
            }

            /* 4. Dark containers ko crisp clean boxes banayein */
            #printable-report .bg-slate-900,
            #printable-report .bg-slate-950,
            #printable-report .bg-emerald-500\/10,
            #printable-report .bg-amber-500\/10,
            #printable-report .bg-rose-500\/10 {
                background-color: #f8fafc !important;
                border: 1px solid #cbd5e1 !important;
                color: #000000 !important;
            }

            /* 5. Typography readability */
            #printable-report h2, 
            #printable-report h3, 
            #printable-report span, 
            #printable-report p,
            #printable-report div {
                color: #000000 !important;
            }

            #printable-report .font-mono {
                color: #000000 !important;
                font-weight: 800 !important;
            }

            @page {
                size: A4 portrait;
                margin: 12mm;
            }
        }
    </style>

    <div class="max-w-5xl mx-auto space-y-8">

        <!-- Tool Header Banner (No-Print) -->
        <div class="text-center space-y-3 no-print">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs font-bold uppercase tracking-wider">
                <i class="fa-solid fa-scale-balanced text-sm animate-pulse"></i>
                <span>Smart Title Risk Inspection Engine</span>
            </div>
            
            <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight" style="font-family: 'Noto Nastaliq Urdu', 'Noto Sans Arabic', serif; line-height: 1.8;">
                AI پراپرٹی وکیل: قانونی دستاویزات کی فوری اسکیننگ
            </h1>
            
            <p class="text-sm text-slate-400 max-w-2xl mx-auto leading-relaxed">
                پلاٹ، مکان یا زرعی رقبہ خریدنے سے پہلے فرد، رجسٹری یا الاٹمنٹ لیٹر اپلوڈ کریں۔ ہمارا اسمارٹ AI جعلی کاغذات، دہرے بیعانے اور مشترکہ کھاتہ کے خطرات کا خودکار آڈٹ کرے گا۔
            </p>
        </div>

        <!-- ============================================== -->
        <!-- STEP 1: UPLOAD & FORM WIZARD (No-Print) -->
        <!-- ============================================== -->
        @if ($currentStep === 'upload')
            <div class="bg-slate-900/80 rounded-3xl border border-slate-800/80 p-6 sm:p-10 shadow-2xl backdrop-blur-xl relative overflow-hidden no-print">
                <div class="absolute -top-24 -right-24 w-80 h-80 rounded-full bg-amber-500/5 blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-24 -left-24 w-80 h-80 rounded-full bg-indigo-500/5 blur-3xl pointer-events-none"></div>

                <form wire:submit="startAnalysis" class="space-y-6 relative z-10">
                    
                    <!-- Document Type Selector Cards -->
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2.5">
                            دستاویز کی قسم منتخب کریں (Select Document Type) *
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <label class="cursor-pointer p-4 rounded-2xl border transition text-center flex flex-col items-center gap-2 {{ $document_type === 'fard' ? 'bg-amber-500/15 border-amber-500 text-amber-300 font-bold shadow-lg shadow-amber-500/10' : 'bg-slate-950/60 border-slate-800 text-slate-400 hover:border-slate-700' }}">
                                <input type="radio" wire:model.live="document_type" value="fard" class="sr-only">
                                <i class="fa-solid fa-file-contract text-2xl"></i>
                                <span class="text-xs">فردِ ملکیت (PLRA Fard)</span>
                            </label>

                            <label class="cursor-pointer p-4 rounded-2xl border transition text-center flex flex-col items-center gap-2 {{ $document_type === 'registry' ? 'bg-amber-500/15 border-amber-500 text-amber-300 font-bold shadow-lg shadow-amber-500/10' : 'bg-slate-950/60 border-slate-800 text-slate-400 hover:border-slate-700' }}">
                                <input type="radio" wire:model.live="document_type" value="registry" class="sr-only">
                                <i class="fa-solid fa-stamp text-2xl"></i>
                                <span class="text-xs">رجسٹری / بیع نامہ</span>
                            </label>

                            <label class="cursor-pointer p-4 rounded-2xl border transition text-center flex flex-col items-center gap-2 {{ $document_type === 'allotment_letter' ? 'bg-amber-500/15 border-amber-500 text-amber-300 font-bold shadow-lg shadow-amber-500/10' : 'bg-slate-950/60 border-slate-800 text-slate-400 hover:border-slate-700' }}">
                                <input type="radio" wire:model.live="document_type" value="allotment_letter" class="sr-only">
                                <i class="fa-solid fa-building-circle-check text-2xl"></i>
                                <span class="text-xs">سوسائٹی الاٹمنٹ لیٹر</span>
                            </label>

                            <label class="cursor-pointer p-4 rounded-2xl border transition text-center flex flex-col items-center gap-2 {{ $document_type === 'intiqal' ? 'bg-amber-500/15 border-amber-500 text-amber-300 font-bold shadow-lg shadow-amber-500/10' : 'bg-slate-950/60 border-slate-800 text-slate-400 hover:border-slate-700' }}">
                                <input type="radio" wire:model.live="document_type" value="intiqal" class="sr-only">
                                <i class="fa-solid fa-diagram-project text-2xl"></i>
                                <span class="text-xs">انتقال / وراثت ریکارڈ</span>
                            </label>
                        </div>
                    </div>

                    <!-- Dropzone / File Upload -->
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2.5">
                            دستاویز کی تصویر یا پی ڈی ایف اپلوڈ کریں (Upload Clean Photo / PDF) *
                        </label>
                        <div class="border-2 border-dashed border-slate-700 hover:border-amber-500 rounded-3xl p-8 text-center bg-slate-950/40 transition group cursor-pointer relative">
                            <input type="file" wire:model="document" accept="image/*,.pdf" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            
                            <div class="space-y-3">
                                <div class="w-16 h-16 rounded-2xl bg-amber-500/10 text-amber-400 flex items-center justify-center mx-auto text-2xl group-hover:scale-110 transition border border-amber-500/20">
                                    <i class="fa-solid fa-cloud-arrow-up"></i>
                                </div>
                                <div>
                                    <span class="text-sm font-bold text-white block">
                                        {{ $document ? $document->getClientOriginalName() : 'فائل یہاں ڈریگ کریں یا کلک کر کے منتخب کریں' }}
                                    </span>
                                    <span class="text-xs text-slate-500 mt-1 block">Supports JPG, PNG, WebP, PDF (Max 10MB)</span>
                                </div>
                            </div>
                        </div>
                        <div wire:loading wire:target="document" class="text-xs text-amber-400 font-bold mt-2 flex items-center gap-2">
                            <i class="fa-solid fa-spinner fa-spin"></i> فائل سسٹم میں لوڈ ہو رہی ہے، براہِ کرم انتظار کریں...
                        </div>
                        @error('document') <span class="text-rose-400 text-xs font-bold mt-1.5 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- District & CNIC Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">
                                ضلع / ریونیو کورٹ (District) *
                            </label>
                            <select wire:model="district" class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 text-xs text-white focus:outline-none focus:border-amber-500">
                                <option value="Rahim Yar Khan">Rahim Yar Khan (رحیم یار خان)</option>
                                <option value="Lahore">Lahore (لاہور)</option>
                                <option value="Rawalpindi">Rawalpindi (راولپنڈی / اسلام آباد)</option>
                                <option value="Faisalabad">Faisalabad (فیصل آباد)</option>
                                <option value="Multan">Multan (ملتان)</option>
                                <option value="Gujranwala">Gujranwala (گوجرانوالہ)</option>
                                <option value="Bahawalpur">Bahawalpur (بہاولپور)</option>
                                <option value="Sialkot">Sialkot (سیالکوٹ)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">
                                فروخت کنندہ شناختی کارڈ (Seller CNIC - اختیاری)
                            </label>
                            <input type="text" wire:model="seller_cnic" placeholder="31301-xxxxxxx-x" 
                                   class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 text-xs text-white font-mono focus:outline-none focus:border-amber-500">
                            @error('seller_cnic') <span class="text-rose-400 text-xs block mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="pt-2">
                        <button type="submit" wire:loading.attr="disabled"
                                class="w-full py-4 bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 hover:to-amber-300 text-slate-950 font-black text-sm rounded-2xl shadow-xl shadow-amber-500/20 transition-all transform active:scale-95 flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50">
                            <i class="fa-solid fa-shield-halved text-base"></i>
                            <span>فوری قانونی آڈٹ شروع کریں (Run Legal AI Scan)</span>
                        </button>
                    </div>

                </form>
            </div>
        @endif

        <!-- ============================================== -->
        <!-- STEP 2: ANIMATED REAL-TIME SCANNING (No-Print) -->
        <!-- ============================================== -->
        @if ($currentStep === 'analyzing')
            <div class="bg-slate-900 rounded-3xl border border-slate-800 p-12 text-center shadow-2xl relative overflow-hidden no-print" 
                 x-data="{ stage: 1 }" 
                 x-init="setInterval(() => { if(stage < 4) stage++; }, 800);">
                
                <!-- Animated Laser Bar -->
                <div class="w-full h-1 bg-gradient-to-r from-transparent via-amber-400 to-transparent absolute top-0 left-0 animate-bounce"></div>

                <div class="max-w-md mx-auto space-y-6">
                    <div class="relative w-28 h-28 mx-auto">
                        <div class="w-28 h-28 rounded-full border-4 border-amber-500/20 border-t-amber-400 animate-spin flex items-center justify-center"></div>
                        <i class="fa-solid fa-file-shield text-3xl text-amber-400 absolute inset-0 m-auto flex items-center justify-center"></i>
                    </div>

                    <div class="space-y-2">
                        <h3 class="text-lg font-black text-white" x-text="
                            stage === 1 ? 'دستاویز کے ہائی ریزولیوشن ٹیکسٹ کو اسکین کیا جا رہا ہے...' :
                            stage === 2 ? 'خسرہ اور موضع کا لینڈ ریکارڈ اتھارٹی (PLRA) سے موازنہ جاری ہے...' :
                            stage === 3 ? 'مشترکہ کھاتہ، حصہ بمطابق رقبہ اور کورٹ اسٹے کی جانچ ہو رہی ہے...' :
                            'حتمی لیگل ٹائٹل رسک اسکور تیار ہو رہا ہے...'
                        "></h3>
                        <p class="text-xs text-slate-400 font-mono">AssanZameen AI Legal Core v3.4 • Verified Protocol</p>
                    </div>

                    <!-- Progress Pills -->
                    <div class="flex items-center justify-center gap-2">
                        <span class="w-3 h-3 rounded-full" :class="stage >= 1 ? 'bg-amber-400 shadow-md shadow-amber-400/50' : 'bg-slate-800'"></span>
                        <span class="w-3 h-3 rounded-full" :class="stage >= 2 ? 'bg-amber-400 shadow-md shadow-amber-400/50' : 'bg-slate-800'"></span>
                        <span class="w-3 h-3 rounded-full" :class="stage >= 3 ? 'bg-amber-400 shadow-md shadow-amber-400/50' : 'bg-slate-800'"></span>
                        <span class="w-3 h-3 rounded-full" :class="stage >= 4 ? 'bg-amber-400 shadow-md shadow-amber-400/50' : 'bg-slate-800'"></span>
                    </div>
                </div>
            </div>
        @endif

        <!-- ============================================== -->
        <!-- STEP 3: HIGH-TECH AUDIT REPORT RESULT -->
        <!-- ============================================== -->
        @if ($currentStep === 'result' && $scanResult)
            <div class="space-y-6">
                
                <!-- Action Controls Bar (Print mein chupa rahega) -->
                <div id="action-bar" class="no-print flex items-center justify-between gap-4 p-4 bg-slate-900 rounded-2xl border border-slate-800 shadow-lg">
                    <button wire:click="resetScanner" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>نیا دستاویز اسکین کریں</span>
                    </button>

                    <button onclick="window.print()" class="px-5 py-2.5 bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 text-slate-950 font-black rounded-xl text-xs transition flex items-center gap-2 shadow-lg shadow-amber-500/20 cursor-pointer">
                        <i class="fa-solid fa-print"></i>
                        <span>سرٹیفکیٹ پرنٹ / PDF ڈاؤن لوڈ کریں</span>
                    </button>
                </div>

                <!-- Main Printable Container -->
                <div id="printable-report" class="space-y-6">

                    <!-- Certificate Header for Print Watermark -->
                    <div class="border-b border-slate-800 pb-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-500 text-slate-950 flex items-center justify-center font-black text-lg">
                                AZ
                            </div>
                            <div>
                                <h2 class="text-lg font-black tracking-tight text-white uppercase">AssanZameen</h2>
                                <span class="text-[10px] text-slate-400 block tracking-widest uppercase">Smart Title Legal Inspection Certificate</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-[11px] font-mono font-bold text-amber-400 block">AZ-{{ strtoupper(substr($scanResult->scan_uuid, 0, 8)) }}</span>
                            <span class="text-[10px] text-slate-400">{{ $scanResult->created_at->format('d M, Y h:i A') }}</span>
                        </div>
                    </div>

                    <!-- Risk Verdict Box -->
                    <div class="bg-slate-900 rounded-3xl border border-slate-800 p-6 sm:p-8 shadow-2xl relative overflow-hidden">
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
                            
                            <div class="space-y-2 text-center sm:text-right flex-grow" dir="rtl">
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">لیگل رسک تجزیہ کا حتمی نتیجہ</span>
                                <h2 class="text-2xl sm:text-3xl font-black text-white flex items-center justify-center sm:justify-start gap-3">
                                    <span>{{ $scanResult->risk_verdict }}</span>
                                    <i class="fa-solid fa-circle-check text-emerald-400"></i>
                                </h2>
                                <p class="text-xs text-slate-400">
                                    دستاویز کی قسم: <strong class="text-white uppercase">{{ $scanResult->document_type }}</strong> &bull; موضع / ضلع: <strong class="text-white">{{ $scanResult->extracted_data['district'] ?? 'N/A' }}</strong>
                                </p>
                            </div>

                            <!-- Speedometer Gauge Metric -->
                            <div class="flex flex-col items-center justify-center p-4 bg-slate-950 rounded-2xl border border-slate-800 min-w-[160px] shrink-0">
                                <span class="text-[11px] font-bold text-slate-400 uppercase">حفاظتی اسکور</span>
                                <div class="text-5xl font-black font-mono mt-1 {{ $scanResult->risk_score >= 80 ? 'text-emerald-400' : ($scanResult->risk_score >= 60 ? 'text-amber-400' : 'text-rose-400') }}">
                                    {{ $scanResult->risk_score }}<span class="text-lg text-slate-500">/100</span>
                                </div>
                                <span class="text-[10px] font-bold text-emerald-400 mt-1 uppercase tracking-wider">
                                    {{ $scanResult->risk_score >= 80 ? 'Certified Legally Clean' : 'Needs Verification' }}
                                </span>
                            </div>

                        </div>
                    </div>

                    <!-- Extracted Key Parameters Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div class="bg-slate-900 p-4 rounded-2xl border border-slate-800">
                            <span class="text-[10px] text-slate-400 uppercase font-bold block">مخصوص خسرہ / کھاتہ</span>
                            <span class="text-sm font-black text-white font-mono mt-1 block">{{ $scanResult->extracted_data['khasra'] ?? 'مشترکہ کھاتہ' }}</span>
                        </div>

                        <div class="bg-slate-900 p-4 rounded-2xl border border-slate-800">
                            <span class="text-[10px] text-slate-400 uppercase font-bold block">کھیوٹ / کھتونی نمبر</span>
                            <span class="text-sm font-black text-amber-400 font-mono mt-1 block">#{{ $scanResult->extracted_data['khewat'] ?? 'N/A' }}</span>
                        </div>

                        <div class="bg-slate-900 p-4 rounded-2xl border border-slate-800">
                            <span class="text-[10px] text-slate-400 uppercase font-bold block">دستاویز میں درج رقبہ</span>
                            <span class="text-sm font-black text-emerald-400 mt-1 block">{{ $scanResult->extracted_data['area'] ?? 'N/A' }}</span>
                        </div>

                        <div class="bg-slate-900 p-4 rounded-2xl border border-slate-800">
                            <span class="text-[10px] text-slate-400 uppercase font-bold block">مالک / فروخت کنندہ</span>
                            <span class="text-sm font-black text-sky-400 mt-1 block truncate">{{ $scanResult->extracted_data['owner_name'] ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <!-- Detailed Audit Checkpoints -->
                    <div class="bg-slate-900 rounded-3xl border border-slate-800 p-6 sm:p-8 space-y-6">
                        <h3 class="text-base font-black text-white flex items-center gap-2">
                            <i class="fa-solid fa-list-check text-amber-400"></i>
                            <span>تفصیلی قانونی آڈٹ چیک پوائنٹس (Inspection Breakdown)</span>
                        </h3>

                        <!-- Green Flags -->
                        <div class="space-y-2">
                            <span class="text-xs font-bold text-emerald-400 uppercase block">مثبت نکات (Verified Clearances)</span>
                            <div class="space-y-2">
                                @foreach($scanResult->audit_flags['green'] ?? [] as $flag)
                                    <div class="flex items-start gap-3 p-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-xs text-emerald-300">
                                        <i class="fa-solid fa-check text-emerald-400 mt-0.5 shrink-0"></i>
                                        <span>{{ $flag }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Warnings -->
                        @if(!empty($scanResult->audit_flags['warnings']))
                            <div class="space-y-2">
                                <span class="text-xs font-bold text-amber-400 uppercase block">احتیاطی انتباہ (Legal Advisories)</span>
                                <div class="space-y-2">
                                    @foreach($scanResult->audit_flags['warnings'] as $warn)
                                        <div class="flex items-start gap-3 p-3 bg-amber-500/10 border border-amber-500/20 rounded-xl text-xs text-amber-300">
                                            <i class="fa-solid fa-triangle-exclamation text-amber-400 mt-0.5 shrink-0"></i>
                                            <span>{{ $warn }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Critical Red Flags -->
                        @if(!empty($scanResult->audit_flags['critical']))
                            <div class="space-y-2">
                                <span class="text-xs font-bold text-rose-400 uppercase block">خطرناک نقائص (Critical Risk Alerts)</span>
                                <div class="space-y-2">
                                    @foreach($scanResult->audit_flags['critical'] as $crit)
                                        <div class="flex items-start gap-3 p-3 bg-rose-500/10 border border-rose-500/20 rounded-xl text-xs text-rose-300 font-bold">
                                            <i class="fa-solid fa-ban text-rose-400 mt-0.5 shrink-0"></i>
                                            <span>{{ $crit }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Legal Disclaimer & Stamp Footer -->
                        <div class="pt-4 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between text-[11px] text-slate-500 gap-3">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-stamp text-amber-400 text-lg"></i>
                                <span>AssanZameen Smart OCR & Real Estate Title Validation Certificate</span>
                            </div>
                            <span class="font-mono">Computer Generated Audit &bull; Valid for Legal Due Diligence</span>
                        </div>

                    </div>

                </div>

            </div>
        @endif

    </div>
</div>