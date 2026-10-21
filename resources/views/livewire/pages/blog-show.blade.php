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
            <div class="flex flex-col sm:flex-row sm:items-center justify-between text-xs text-slate-400 border-b border-slate-800 pb-4 gap-2">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-amber-400 text-slate-950 flex items-center justify-center font-black text-xs">
                        {{ strtoupper(substr($post->author->name ?? 'A', 0, 1)) }}
                    </div>
                    <span>{{ $post->author->name ?? 'AssanZameen Expert' }}</span>
                    <span>•</span>
                    <span>{{ $post->created_at->format('F d, Y') }}</span>
                </div>
                <div class="flex items-center gap-4 font-mono">
                    <span><i class="fa-regular fa-clock mr-1"></i> {{ $post->read_time }} mins read</span>
                    <span><i class="fa-solid fa-eye mr-1 text-slate-400"></i> {{ number_format($post->views_count) }} views</span>
                    <span class="text-amber-400 font-bold"><i class="fa-solid fa-star text-amber-400 mr-1"></i> {{ $avgRating }} ({{ count($reviews) }})</span>
                </div>
            </div>
        </div>

        <!-- Featured Image -->
        @if($post->featured_image)
            <div class="w-full max-h-[460px] rounded-3xl overflow-hidden border border-slate-800 shadow-2xl">
                <img src="{{ asset('public/' . $post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
            </div>
        @endif

        <!-- Ad Placement 1 (Top) -->
        

        <!-- Article Body Container -->
        <div class="urdu-article-body text-slate-200" dir="rtl" style="font-size: 18px; line-height: 1.4; text-align: right;">
            {!! html_entity_decode($post->content) !!}
        </div>

        <!-- Ad Placement 2 (Bottom) -->
        

        <!-- Engagement Bar: Like Button & Social Share -->
        <div class="p-5 bg-slate-900/90 rounded-2xl border border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
            <!-- Like Button -->
            <button type="button" wire:click="toggleLike"
                    class="px-5 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 cursor-pointer shadow-sm {{ $hasLiked ? 'bg-rose-600 text-white shadow-rose-600/30' : 'bg-slate-800 hover:bg-slate-750 text-slate-300 hover:text-white border border-slate-700' }}">
                <i class="fa-solid fa-heart text-sm {{ $hasLiked ? 'text-white scale-110' : 'text-rose-400' }}"></i>
                <span>{{ $hasLiked ? 'Liked!' : 'Helpful Article' }}</span>
                <span class="px-2 py-0.5 rounded-md bg-slate-950/60 font-mono text-[11px]">{{ $post->likes_count }}</span>
            </button>

            <!-- Share Buttons -->
            <div class="flex items-center gap-2" x-data>
                <span class="text-xs text-slate-400 hidden sm:inline">Share this guide:</span>
                <a href="https://wa.me/?text={{ urlencode($post->title . ' - ' . url()->current()) }}" target="_blank"
                   class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 transition shadow-sm">
                    <i class="fa-brands fa-whatsapp text-sm"></i> WhatsApp
                </a>
                <button type="button" @click="navigator.clipboard.writeText(window.location.href); alert('Article link copied!');"
                        class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-bold flex items-center gap-1.5 transition border border-slate-700">
                    <i class="fa-solid fa-link text-xs"></i> Copy Link
                </button>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- USER REVIEWS & RATING SECTION (NO LOGIN) -->
        <!-- ========================================== -->
        <div class="space-y-6 pt-6 border-t border-slate-800">
            
            <!-- Section Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-black text-white flex items-center gap-2">
                        <i class="fa-solid fa-comments text-amber-400"></i>
                        <span>Reader Reviews & Discussion</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Apni raye ya sawal bina kisi login ke darj karein</p>
                </div>
                
                <div class="flex items-center gap-1 bg-slate-900 px-3.5 py-1.5 rounded-xl border border-slate-800">
                    <i class="fa-solid fa-star text-amber-400 text-sm"></i>
                    <span class="text-sm font-bold text-white font-mono">{{ $avgRating }}</span>
                    <span class="text-[11px] text-slate-500">/ 5.0</span>
                </div>
            </div>

            <!-- Flash Alert for Review -->
            @if (session()->has('review_success'))
                <div class="p-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 rounded-2xl text-xs font-bold flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-base"></i>
                    <span>{{ session('review_success') }}</span>
                </div>
            @endif

            <!-- Review Input Form (Without Login) -->
            <div class="bg-slate-900/90 p-6 rounded-3xl border border-slate-800 shadow-xl space-y-4">
                <span class="text-xs font-bold text-slate-300 uppercase block tracking-wider">Write Your Review / Question</span>

                <form wire:submit="submitReview" class="space-y-4">
                    <!-- Rating Stars Selector -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">Your Rating *</label>
                        <div class="flex items-center gap-2">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" wire:click="$set('rating', {{ $i }})"
                                        class="text-xl transition-transform hover:scale-125 focus:outline-none cursor-pointer {{ $rating >= $i ? 'text-amber-400' : 'text-slate-700' }}">
                                    ★
                                </button>
                            @endfor
                            <span class="text-xs font-bold text-amber-400 font-mono ml-2">({{ $rating }} Star{{ $rating > 1 ? 's' : '' }})</span>
                        </div>
                    </div>

                    <!-- Name and Optional Email -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">Your Name *</label>
                            <input type="text" wire:model="user_name" placeholder="Maslan: Tariq Mahmood"
                                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-amber-500 font-medium">
                            @error('user_name') <span class="text-rose-400 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">Email Address (Optional / Private)</label>
                            <input type="email" wire:model="user_email" placeholder="name@example.com"
                                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-amber-500 font-medium">
                            @error('user_email') <span class="text-rose-400 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Comment / Review Text -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">Comment / Feedback *</label>
                        <textarea wire:model="comment" rows="3" placeholder="Is article ke baare mein apni raye ya property se mutalliq sawal likhein..."
                                  class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-xs text-white focus:outline-none focus:border-amber-500 leading-relaxed"></textarea>
                        @error('comment') <span class="text-rose-400 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" wire:loading.attr="disabled"
                                class="px-6 py-2.5 bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 text-slate-950 font-black text-xs rounded-xl transition shadow-lg shadow-amber-500/20 cursor-pointer disabled:opacity-50">
                            <span wire:loading.remove wire:target="submitReview">Post Review</span>
                            <span wire:loading wire:target="submitReview">Publishing...</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Published Reviews List -->
            <div class="space-y-3 pt-2">
                @forelse($reviews as $rev)
                    <div class="p-4 bg-slate-900/60 rounded-2xl border border-slate-800/80 space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-amber-400 text-xs">
                                    {{ strtoupper(substr($rev->user_name, 0, 1)) }}
                                </div>
                                <span class="font-bold text-white text-xs">{{ $rev->user_name }}</span>
                                <span class="text-[10px] text-slate-500 font-mono">• {{ $rev->created_at->diffForHumans() }}</span>
                            </div>

                            <!-- Rating Stars -->
                            <div class="flex items-center text-amber-400 text-xs">
                                @for($s = 1; $s <= 5; $s++)
                                    <span>{{ $rev->rating >= $s ? '★' : '☆' }}</span>
                                @endfor
                            </div>
                        </div>

                        <p class="text-xs text-slate-300 leading-relaxed pl-9">
                            {{ $rev->comment }}
                        </p>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-500 bg-slate-900/30 rounded-2xl border border-slate-800 text-xs">
                        No reviews yet...
                    </div>
                @endforelse
            </div>

        </div>

    </div>
</div>