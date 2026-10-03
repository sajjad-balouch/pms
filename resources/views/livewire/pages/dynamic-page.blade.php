<div class="bg-slate-950 min-h-screen text-slate-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto space-y-8">
        
        <!-- Header Banner -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-8 sm:p-10 relative overflow-hidden shadow-2xl">
            <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10 space-y-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-500/10 text-amber-400 border border-amber-500/20 text-[10px] font-black uppercase tracking-widest rounded-full">
                    <i class="fa-solid fa-file-contract"></i> Portal Document
                </span>
                
                <h1 class="text-3xl sm:text-5xl font-extrabold text-white tracking-tight">
                    {{ $page->title }}
                </h1>

                <div class="flex items-center gap-4 text-xs text-slate-400 pt-2 border-t border-slate-800/80">
                    <span class="flex items-center gap-1.5">
                        <i class="fa-regular fa-clock text-amber-400"></i>
                        Last Updated: {{ $page->updated_at ? $page->updated_at->format('M d, Y') : 'Recently' }}
                    </span>
                    <span>•</span>
                    <span class="flex items-center gap-1.5 text-emerald-400">
                        <i class="fa-solid fa-shield-halved"></i> Verified Official Page
                    </span>
                </div>
            </div>
        </div>

        <!-- Content Container -->
        <div class="bg-slate-900/60 border border-slate-800/80 rounded-3xl p-6 sm:p-10 shadow-xl backdrop-blur-sm">
            <div class="dynamic-page-content prose prose-invert prose-amber max-w-none">
                
                {{-- HTML-entities decode + Raw output --}}
                {!! htmlspecialchars_decode($page->content) !!}

            </div>
        </div>

        <!-- Footer Back Link -->
        <div class="text-center pt-4">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-400 hover:text-amber-400 transition-colors">
                <i class="fa-solid fa-arrow-left"></i> Back to Home
            </a>
        </div>

    </div>

    <!-- Custom Styling for Decoded HTML Elements -->
    <style>
        .dynamic-page-content h2 {
            color: #ffffff;
            font-weight: 800;
            font-size: 1.35rem;
            margin-top: 2rem;
            margin-bottom: 0.85rem;
            border-bottom: 1px solid #1e293b;
            padding-bottom: 0.5rem;
            letter-spacing: -0.025em;
        }
        .dynamic-page-content p {
            color: #cbd5e1;
            font-size: 0.95rem;
            line-height: 1.75;
            margin-bottom: 1.25rem;
        }
        .dynamic-page-content ul {
            list-style-type: disc;
            padding-left: 1.25rem;
            margin-bottom: 1.5rem;
            color: #cbd5e1;
        }
        .dynamic-page-content li {
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }
        .dynamic-page-content strong {
            color: #fbbf24;
            font-weight: 700;
        }
    </style>
</div>