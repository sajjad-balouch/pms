<div class="min-h-screen bg-slate-950 text-slate-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-10">

        <!-- Header -->
        <div class="text-center max-w-3xl mx-auto space-y-3">
            <span class="text-xs font-bold uppercase tracking-widest text-amber-400 bg-amber-400/10 px-3 py-1 rounded-full border border-amber-400/20">
                AssanZameen Real Estate Knowledge Hub
            </span>
            <h1 class="text-4xl sm:text-5xl font-black text-white">
                Pakistan Property Insights, Taxes & Guides
            </h1>
            <p class="text-sm text-slate-400">
                Sarkari registry schedule, FBR tax updates, patwari measurements aur investment trends par authentic maloomat.
            </p>
        </div>

        <!-- Category Filter Tabs -->
        <div class="flex items-center justify-center flex-wrap gap-2 pt-2">
            @php $cats = ['', 'Property Guides', 'Taxes & Laws', 'Market News', 'Investment Tips']; @endphp
            @foreach($cats as $c)
                <button wire:click="filterCategory('{{ $c }}')" 
                        class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $category === $c ? 'bg-amber-400 text-slate-950 font-black shadow-lg shadow-amber-400/20' : 'bg-slate-900 text-slate-300 hover:bg-slate-800 border border-slate-800' }}">
                    {{ $c ?: 'All Articles' }}
                </button>
            @endforeach
        </div>

        <!-- Articles Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" style="font-family: Jameel Noori Nastaleeq">
            @forelse($posts as $p)
                <div class="bg-slate-900/80 border border-slate-800 rounded-3xl overflow-hidden hover:border-slate-700 transition flex flex-col justify-between group shadow-xl">
                    <div>
                        <div class="h-48 bg-slate-950 overflow-hidden relative">
                            @if($p->featured_image)
                                <img src="{{ asset('public/' . $p->featured_image) }}" alt="{{ $p->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-700 bg-slate-900"><i class="fa-solid fa-newspaper text-4xl"></i></div>
                            @endif
                            <span class="absolute top-3 right-3 px-3 py-1 bg-slate-950/80 backdrop-blur-md border border-slate-800 text-[10px] font-bold text-amber-400 rounded-lg">
                                {{ $p->category }}
                            </span>
                        </div>

                        <div class="p-6 space-y-3">
                            <div class="flex items-center gap-2 text-[11px] text-slate-400 font-mono">
                                <span>{{ $p->created_at->format('M d, Y') }}</span>
                                <span>•</span>
                                <span>{{ $p->read_time }} min read</span>
                            </div>

                            <a href="{{ route('blog.show', $p->slug) }}" class="block">
                                <h3 class="text-base font-bold text-white group-hover:text-amber-400 transition line-clamp-2">
                                    {{ $p->title }}
                                </h3>
                            </a>

                            <p class="text-xs text-slate-400 line-clamp-3 leading-relaxed">
                                {{ $p->summary }}
                            </p>
                        </div>
                    </div>

                    <div class="p-6 pt-0 border-t border-slate-800/80 mt-4 flex items-center justify-between text-xs">
                        <span class="text-slate-500 font-medium">By {{ $p->author->name ?? 'Editorial Team' }}</span>
                        <a href="{{ route('blog.show', $p->slug) }}" class="font-bold text-amber-400 group-hover:translate-x-1 transition flex items-center gap-1">
                            <span>Read More</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full p-12 text-center text-slate-500 bg-slate-900/60 rounded-3xl border border-slate-800">
                    No articles found matching criteria.
                </div>
            @endforelse
        </div>

        <div class="pt-4">
            {{ $posts->links() }}
        </div>

        <!-- Ad Placement Card at Bottom -->
        

    </div>
</div>