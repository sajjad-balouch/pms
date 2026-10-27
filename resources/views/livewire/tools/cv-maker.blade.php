<div class="min-h-screen bg-[#070a13] text-slate-100 py-10 px-4 sm:px-6 lg:px-8 w-full overflow-x-hidden">

    <style>
        .cv-wrapper {
            display: flex;
            flex-direction: column;
            gap: 2rem;
            width: 100%;
        }
        @media (min-width: 1024px) {
            .cv-wrapper {
                flex-direction: row;
                align-items: flex-start;
            }
            .cv-form-col {
                width: 44% !important;
                flex-shrink: 0;
            }
            .cv-preview-col {
                width: 56% !important;
                flex-grow: 1;
            }
        }
    </style>

    <div class="max-w-7xl mx-auto space-y-8 w-full">

        <!-- Tool Header Banner -->
        <div class="text-center space-y-3 w-full">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-sky-500/10 border border-sky-500/30 text-sky-400 text-xs font-bold uppercase tracking-wider">
                <i class="fa-solid fa-file-user text-sm"></i>
                <span>ATS-Compliant English Resume & CV Studio</span>
            </div>
            
            <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight">
                Professional Resume & CV Builder
            </h1>
            
            <p class="text-sm text-slate-400 max-w-2xl mx-auto leading-relaxed">
                Design a recruiter-ready, ATS-friendly curriculum vitae in minutes. Fill in your career details and export an international standard A4 PDF instantly.
            </p>
        </div>

        <div class="cv-wrapper w-full">

            <!-- LEFT COLUMN: INPUT CONTROLS -->
            <div class="cv-form-col w-full bg-slate-900/90 rounded-3xl border border-slate-800 p-6 shadow-2xl space-y-5 text-left">
                
                <!-- Color Theme Selector -->
                <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                    <span class="text-xs font-bold text-slate-300 uppercase tracking-wider">Accent Color:</span>
                    <div class="flex items-center gap-2">
                        <button type="button" title="Corporate Blue" wire:click="$set('theme_color', '#0284c7')" class="w-6 h-6 rounded-full bg-sky-600 border-2 {{ $theme_color === '#0284c7' ? 'border-white scale-110 shadow-lg' : 'border-transparent' }}"></button>
                        <button type="button" title="Emerald Green" wire:click="$set('theme_color', '#059669')" class="w-6 h-6 rounded-full bg-emerald-600 border-2 {{ $theme_color === '#059669' ? 'border-white scale-110 shadow-lg' : 'border-transparent' }}"></button>
                        <button type="button" title="Warm Amber" wire:click="$set('theme_color', '#d97706')" class="w-6 h-6 rounded-full bg-amber-600 border-2 {{ $theme_color === '#d97706' ? 'border-white scale-110 shadow-lg' : 'border-transparent' }}"></button>
                        <button type="button" title="Executive Slate" wire:click="$set('theme_color', '#334155')" class="w-6 h-6 rounded-full bg-slate-700 border-2 {{ $theme_color === '#334155' ? 'border-white scale-110 shadow-lg' : 'border-transparent' }}"></button>
                    </div>
                </div>

                <!-- 1. Personal Information -->
                <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-3">
                    <span class="text-xs font-bold text-sky-400 uppercase tracking-wider block">1. Personal Information</span>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] text-slate-400 uppercase mb-1">Full Name</label>
                            <input type="text" wire:model.live="full_name" placeholder="John Doe" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white outline-none focus:border-sky-500">
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-400 uppercase mb-1">Professional Title</label>
                            <input type="text" wire:model.live="job_title" placeholder="Lead Software Architect" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white outline-none focus:border-sky-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] text-slate-400 uppercase mb-1">Email Address</label>
                            <input type="email" wire:model.live="email" placeholder="john@example.com" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono outline-none focus:border-sky-500">
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-400 uppercase mb-1">Phone Number</label>
                            <input type="text" wire:model.live="phone" placeholder="+1 (555) 000-0000" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono outline-none focus:border-sky-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] text-slate-400 uppercase mb-1">Location / City</label>
                            <input type="text" wire:model.live="city" placeholder="City, Country" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white outline-none focus:border-sky-500">
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-400 uppercase mb-1">LinkedIn / Portfolio URL</label>
                            <input type="text" wire:model.live="linkedin_url" placeholder="linkedin.com/in/username" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono outline-none focus:border-sky-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] text-slate-400 uppercase font-bold mb-1">Executive Summary</label>
                        <textarea rows="3" wire:model.live="summary" placeholder="Write a crisp, high-impact professional summary..." class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-xs text-white leading-relaxed outline-none focus:border-sky-500"></textarea>
                    </div>
                </div>

                <!-- 2. Work Experience -->
                <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider">2. Work Experience</span>
                        <button type="button" wire:click="addExperience" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-[11px] font-bold transition flex items-center gap-1 cursor-pointer">
                            <i class="fa-solid fa-plus text-[10px]"></i> Add Experience
                        </button>
                    </div>

                    @foreach($experiences as $index => $exp)
                        <div class="p-3 bg-slate-900 rounded-xl border border-slate-800 space-y-2 relative">
                            <button type="button" wire:click="removeExperience({{ $index }})" class="absolute top-2.5 right-2.5 text-rose-400 hover:text-rose-300 text-xs cursor-pointer" title="Delete Experience">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                            <div class="grid grid-cols-2 gap-2 pr-6">
                                <div>
                                    <label class="block text-[9px] text-slate-400 uppercase">Role / Job Title</label>
                                    <input type="text" wire:model.live="experiences.{{ $index }}.role" placeholder="e.g. Senior Software Architect" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-white outline-none">
                                </div>
                                <div>
                                    <label class="block text-[9px] text-slate-400 uppercase">Company / Organization</label>
                                    <input type="text" wire:model.live="experiences.{{ $index }}.company" placeholder="e.g. Acme Corporation" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-white outline-none">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[9px] text-slate-400 uppercase">Employment Dates</label>
                                <input type="text" wire:model.live="experiences.{{ $index }}.duration" placeholder="e.g. 2022 - Present" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-white font-mono outline-none">
                            </div>
                            <div>
                                <label class="block text-[9px] text-slate-400 uppercase">Key Responsibilities & Achievements</label>
                                <textarea rows="2" wire:model.live="experiences.{{ $index }}.details" placeholder="Describe measurable accomplishments and responsibilities..." class="w-full bg-slate-950 border border-slate-700 rounded-lg p-2 text-xs text-white outline-none"></textarea>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- 3. Education -->
                <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-amber-400 uppercase tracking-wider">3. Education</span>
                        <button type="button" wire:click="addEducation" class="px-2.5 py-1 bg-amber-600 hover:bg-amber-500 text-slate-950 rounded-lg text-[11px] font-bold transition flex items-center gap-1 cursor-pointer">
                            <i class="fa-solid fa-plus text-[10px]"></i> Add Education
                        </button>
                    </div>

                    @foreach($educations as $index => $edu)
                        <div class="p-3 bg-slate-900 rounded-xl border border-slate-800 space-y-2 relative">
                            <button type="button" wire:click="removeEducation({{ $index }})" class="absolute top-2.5 right-2.5 text-rose-400 hover:text-rose-300 text-xs cursor-pointer" title="Delete Degree">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                            <div class="grid grid-cols-2 gap-2 pr-6">
                                <div>
                                    <label class="block text-[9px] text-slate-400 uppercase">Degree / Qualification</label>
                                    <input type="text" wire:model.live="educations.{{ $index }}.degree" placeholder="e.g. BS Computer Science" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-white outline-none">
                                </div>
                                <div>
                                    <label class="block text-[9px] text-slate-400 uppercase">Institution / University</label>
                                    <input type="text" wire:model.live="educations.{{ $index }}.institute" placeholder="e.g. Oxford University" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-white outline-none">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[9px] text-slate-400 uppercase">Graduation Year</label>
                                <input type="text" wire:model.live="educations.{{ $index }}.year" placeholder="e.g. 2017 - 2021" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-white font-mono outline-none">
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- 4. Skills & Languages -->
                <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-3">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">4. Core Competencies & Languages</span>
                    <div>
                        <label class="block text-[10px] text-slate-400 uppercase mb-1">Key Skills (Comma separated):</label>
                        <input type="text" wire:model.live="skills" placeholder="Laravel, MySQL, REST APIs, Git" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white outline-none focus:border-sky-500">
                    </div>
                    <div>
                        <label class="block text-[10px] text-slate-400 uppercase mb-1">Languages Spoken:</label>
                        <input type="text" wire:model.live="languages" placeholder="English (Professional), Urdu (Native)" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white outline-none focus:border-sky-500">
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: LIVE RECRUITER PREVIEW & PRINT -->
            <div class="cv-preview-col w-full space-y-4">
                
                <div class="flex items-center justify-between gap-3 p-4 bg-slate-900 rounded-2xl border border-slate-800 w-full">
                    <span class="text-xs font-bold text-slate-400">Live Recruiter-Ready Preview:</span>
                    <button type="button" onclick="printPerfectCV()" class="px-5 py-2.5 bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-400 text-white font-black rounded-xl text-xs transition flex items-center gap-2 shadow-lg shadow-sky-500/20 cursor-pointer">
                        <i class="fa-solid fa-print"></i>
                        <span>Download Resume (A4 PDF)</span>
                    </button>
                </div>

                <!-- Printable Document Sheet -->
                <div id="printable-cv-sheet" class="bg-white rounded-3xl p-8 sm:p-12 shadow-2xl text-slate-900 space-y-6 w-full text-left" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
                    
                    <!-- Header Section -->
                    <div class="border-b-2 pb-5 space-y-1.5" style="border-color: {{ $theme_color }};">
                        <h1 class="text-2xl sm:text-3xl font-black tracking-tight" style="color: {{ $theme_color }};">
                            {{ $full_name ?: 'Your Full Name' }}
                        </h1>
                        <p class="text-xs sm:text-sm font-bold text-slate-700 uppercase tracking-widest">
                            {{ $job_title ?: 'Professional Title / Designation' }}
                        </p>

                        <!-- Contact Details Bar -->
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-600 pt-2 font-mono">
                            @if($phone) <span><i class="fa-solid fa-phone mr-1 text-slate-400"></i> {{ $phone }}</span> @endif
                            @if($email) <span><i class="fa-solid fa-envelope mr-1 text-slate-400"></i> {{ $email }}</span> @endif
                            @if($city) <span><i class="fa-solid fa-location-dot mr-1 text-slate-400"></i> {{ $city }}</span> @endif
                            @if($linkedin_url) <span><i class="fa-brands fa-linkedin mr-1 text-slate-400"></i> {{ $linkedin_url }}</span> @endif
                        </div>
                    </div>

                    <!-- Executive Summary -->
                    @if($summary)
                        <div class="space-y-1.5">
                            <h2 class="text-xs font-black uppercase tracking-wider" style="color: {{ $theme_color }};">
                                Professional Summary
                            </h2>
                            <p class="text-xs text-slate-700 leading-relaxed text-justify">
                                {{ $summary }}
                            </p>
                        </div>
                    @endif

                    <!-- Work Experience -->
                    @if(count($experiences) > 0)
                        <div class="space-y-3">
                            <h2 class="text-xs font-black uppercase tracking-wider border-b border-slate-200 pb-1" style="color: {{ $theme_color }};">
                                Work Experience
                            </h2>
                            <div class="space-y-3">
                                @foreach($experiences as $exp)
                                    <div class="space-y-1">
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="font-black text-slate-900 text-sm">{{ $exp['role'] }}</span>
                                            <span class="font-mono text-slate-500 font-bold text-[11px]">{{ $exp['duration'] }}</span>
                                        </div>
                                        <div class="text-xs font-bold text-slate-700">{{ $exp['company'] }}</div>
                                        @if(!empty($exp['details']))
                                            <p class="text-xs text-slate-600 leading-relaxed whitespace-pre-line text-justify">{{ $exp['details'] }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Education -->
                    @if(count($educations) > 0)
                        <div class="space-y-3">
                            <h2 class="text-xs font-black uppercase tracking-wider border-b border-slate-200 pb-1" style="color: {{ $theme_color }};">
                                Education
                            </h2>
                            <div class="space-y-2.5">
                                @foreach($educations as $edu)
                                    <div class="flex items-center justify-between text-xs">
                                        <div>
                                            <span class="font-black text-slate-900 text-xs block">{{ $edu['degree'] }}</span>
                                            <span class="text-slate-600 block text-[11px]">{{ $edu['institute'] }}</span>
                                        </div>
                                        <span class="font-mono text-slate-500 font-bold text-[11px]">{{ $edu['year'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Key Skills -->
                    @if($skills)
                        <div class="space-y-2">
                            <h2 class="text-xs font-black uppercase tracking-wider border-b border-slate-200 pb-1" style="color: {{ $theme_color }};">
                                Core Competencies & Skills
                            </h2>
                            <div class="flex flex-wrap gap-1.5 pt-0.5">
                                @foreach(explode(',', $skills) as $skill)
                                    @if(trim($skill))
                                        <span class="text-[11px] font-semibold bg-slate-100 text-slate-800 px-2.5 py-0.5 rounded border border-slate-200">
                                            {{ trim($skill) }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Languages -->
                    @if($languages)
                        <div class="space-y-1 pt-1">
                            <h2 class="text-xs font-black uppercase tracking-wider" style="color: {{ $theme_color }};">
                                Languages
                            </h2>
                            <p class="text-xs text-slate-700 font-medium">{{ $languages }}</p>
                        </div>
                    @endif

                </div>

            </div>

        </div>

    </div>

    <!-- BULLETPROOF PRINT JAVASCRIPT ENGINE -->
    <!-- 100% RELIABLE IFRAME PRINT ENGINE (GUARANTEED NO BLANK PAGE) -->
    <script>
        function printPerfectCV() {
            // 1. Get raw HTML of the CV sheet
            const cvElement = document.getElementById('printable-cv-sheet');
            if (!cvElement) {
                alert('CV content not found!');
                return;
            }
            const cvHtml = cvElement.innerHTML;

            // 2. Remove any previously created print iframe
            let oldIframe = document.getElementById('cv-print-frame');
            if (oldIframe) {
                oldIframe.remove();
            }

            // 3. Create a clean, hidden iframe
            const iframe = document.createElement('iframe');
            iframe.id = 'cv-print-frame';
            iframe.style.position = 'fixed';
            iframe.style.right = '0';
            iframe.style.bottom = '0';
            iframe.style.width = '0';
            iframe.style.height = '0';
            iframe.style.border = '0';
            document.body.appendChild(iframe);

            // 4. Inject isolated HTML + Tailwind CSS classes directly into the iframe
            const doc = iframe.contentWindow.document;
            doc.open();
            doc.write(`
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <title>Resume</title>
                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
                    <style>
                        /* Base Clean Resets */
                        * {
                            box-sizing: border-box;
                            margin: 0;
                            padding: 0;
                        }
                        html, body {
                            background-color: #ffffff !important;
                            color: #0f172a !important;
                            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif !important;
                            width: 100% !important;
                            margin: 0 !important;
                            padding: 0 !important;
                            -webkit-print-color-adjust: exact !important;
                            print-color-adjust: exact !important;
                        }

                        /* Typography & Layout Rules matching your design */
                        .cv-container {
                            width: 100%;
                            padding: 10mm 15mm;
                            background: #ffffff;
                            color: #0f172a;
                        }

                        h1 { font-size: 24pt; font-weight: 900; line-height: 1.1; margin-bottom: 4px; }
                        h2 { font-size: 10.5pt; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px; }
                        p { font-size: 9.5pt; line-height: 1.5; color: #334155; }

                        .border-b-2 { border-bottom: 2px solid; }
                        .border-b { border-bottom: 1px solid #e2e8f0; }

                        .flex { display: flex; }
                        .flex-wrap { flex-wrap: wrap; }
                        .items-center { align-items: center; }
                        .justify-between { justify-content: space-between; }
                        
                        .gap-x-4 { column-gap: 1rem; }
                        .gap-y-1 { row-gap: 0.25rem; }
                        .gap-1\.5 { gap: 0.375rem; }

                        .space-y-6 > * + * { margin-top: 1.25rem; }
                        .space-y-3 > * + * { margin-top: 0.75rem; }
                        .space-y-2\.5 > * + * { margin-top: 0.6rem; }
                        .space-y-2 > * + * { margin-top: 0.5rem; }
                        .space-y-1\.5 > * + * { margin-top: 0.35rem; }
                        .space-y-1 > * + * { margin-top: 0.25rem; }

                        .text-xs { font-size: 8.5pt; }
                        .text-sm { font-size: 9.5pt; }
                        .text-\[11px\] { font-size: 8pt; }
                        
                        .font-black { font-weight: 900; }
                        .font-bold { font-weight: 700; }
                        .font-semibold { font-weight: 600; }
                        .font-mono { font-family: monospace; }
                        .uppercase { text-transform: uppercase; }
                        .tracking-widest { letter-spacing: 0.1em; }
                        .tracking-wider { letter-spacing: 0.05em; }

                        .text-slate-900 { color: #0f172a; }
                        .text-slate-700 { color: #334155; }
                        .text-slate-600 { color: #475569; }
                        .text-slate-500 { color: #64748b; }
                        .text-slate-400 { color: #94a3b8; }

                        .bg-slate-100 { background-color: #f1f5f9; }
                        .border-slate-200 { border: 1px solid #e2e8f0; }
                        .rounded { border-radius: 4px; }
                        .px-2\.5 { padding-left: 0.6rem; padding-right: 0.6rem; }
                        .py-0\.5 { padding-top: 0.15rem; padding-bottom: 0.15rem; }
                        .pb-5 { padding-bottom: 1rem; }
                        .pb-1 { padding-bottom: 0.25rem; }
                        .pt-2 { padding-top: 0.5rem; }
                        .pt-1 { padding-top: 0.25rem; }
                        .mr-1 { margin-right: 0.25rem; }
                        .whitespace-pre-line { white-space: pre-line; }
                        .text-justify { text-align: justify; }

                        @page {
                            size: A4 portrait;
                            margin: 0; /* Handled by container padding */
                        }
                    </style>
                </head>
                <body>
                    <div class="cv-container">
                        ${cvHtml}
                    </div>
                </body>
                </html>
            `);
            doc.close();

            // 5. Trigger print once iframe resources finish loading
            setTimeout(() => {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            }, 300);
        }
    </script>
</div>