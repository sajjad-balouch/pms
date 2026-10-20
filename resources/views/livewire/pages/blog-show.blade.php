<div class="min-h-screen bg-slate-950 text-slate-100 py-12 px-4 sm:px-6 lg:px-8" style="font-family: Jameel Noori Nastaleeq; direction: rtl;">
    <div class="max-w-4xl mx-auto space-y-8">

        <!-- Top Breadcrumbs -->
        <div class="flex items-center gap-2 text-xs text-slate-400">
            <a href="{{ route('home') }}" class="hover:text-white">Home</a>
            <span>/</span>
            <a href="{{ route('blog.index') }}" class="hover:text-white">Blog</a>
            <span>/</span>
            <span class="text-amber-400 truncate">{{ $post->category }}</span>
        </div>

        <!-- Article Header -->
        <div class="space-y-4">
            <span class="px-3 py-1 bg-amber-400/10 border border-amber-400/20 text-amber-400 text-xs font-bold rounded-lg uppercase">
                {{ $post->category }}
            </span>
            <h1 class="text-3xl sm:text-5xl font-black text-white leading-tight">
                {{ $post->title }}
            </h1>
            <div class="flex items-center justify-between text-xs text-slate-400 border-b border-slate-800 pb-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-amber-400 text-slate-950 flex items-center justify-center font-black text-xs">
                        {{ strtoupper(substr($post->author->name ?? 'A', 0, 1)) }}
                    </div>
                    <span>{{ $post->author->name ?? 'AssanZameen Expert' }}</span>
                    <span>•</span>
                    <span>{{ $post->created_at->format('F d, Y') }}</span>
                </div>
                <div class="flex items-center gap-3 font-mono">
                    <span><i class="fa-regular fa-clock mr-1"></i> {{ $post->read_time }} mins read</span>
                    <span><i class="fa-solid fa-eye mr-1"></i> {{ number_format($post->views_count) }} views</span>
                </div>
            </div>
        </div>

        <!-- Featured Image -->
        @if($post->featured_image)
            <div class="w-full max-h-[460px] rounded-3xl overflow-hidden border border-slate-800 shadow-2xl">
                <img src="{{ asset('public/' . $post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
            </div>
        @endif

        <!-- Ad Placement 1 (Top of Article) -->
        

        <!-- Article Body -->
        <article class="prose prose-invert max-w-none text-slate-300 text-base leading-loose space-y-6">
            <div class="p-4 bg-slate-900 border-l-4 border-amber-400 rounded-r-2xl text-slate-300 italic text-sm">
                {{ $post->summary }}
            </div>

            <div class="space-y-4">
               {!! html_entity_decode($post->content) !!}
            </div>
        </article>

        <!-- Ad Placement 2 (End of Article) -->
        

        <!-- Social Share Bar -->
        <div class="p-4 bg-slate-900/80 rounded-2xl border border-slate-800 flex items-center justify-between text-xs" x-data>
            <span class="font-bold text-slate-300">Share this helpful article:</span>
            <div class="flex items-center gap-2">
                <a href="https://wa.me/?text={{ urlencode($post->title . ' - ' . url()->current()) }}" target="_blank"
                   class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold flex items-center gap-1.5 transition">
                    <i class="fa-brands fa-whatsapp"></i> WhatsApp
                </a>
                <button type="button" @click="navigator.clipboard.writeText(window.location.href); alert('Article link copied!');"
                        class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl font-bold flex items-center gap-1 transition">
                    <i class="fa-solid fa-link"></i> Copy Link
                </button>
            </div>
        </div>

    </div>
</div>