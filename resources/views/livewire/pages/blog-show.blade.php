@php
    use Illuminate\Support\Str;

    $isUr = app()->getLocale() === 'ur';
    $t    = fn (string $en, string $ur) => $isUr ? $ur : $en;

    // ---- Content ----
    $contentHtml  = html_entity_decode((string) $post->content);
    $plainContent = trim(preg_replace('/\s+/u', ' ', strip_tags($contentHtml)));

    // Article ki zubaan: Arabic/Urdu script ho to RTL, warna LTR (pehle hamesha RTL tha)
    $contentIsRtl = (bool) preg_match('/\p{Arabic}/u', $plainContent);
    $contentDir   = $contentIsRtl ? 'rtl' : 'ltr';
    $contentLang  = $contentIsRtl ? 'ur' : 'en';

    $authorName  = $post->author->name ?? 'AssanZameen Expert';
    $postUrl     = route('blog.show', $post->slug);
    $reviewCount = count($reviews);
    $views       = (int) ($post->views_count ?? 0);
    $likes       = (int) ($post->likes_count ?? 0);
    $ratingNow   = (int) ($rating ?? 0);

    $imageUrl = $post->featured_image ? asset('public/' . $post->featured_image) : null;

    // ---- SEO ----
    $seoTitle = Str::limit($post->title, 62, '…') . ' | Assan Zameen';
    $seoDescription = Str::limit(trim(strip_tags((string) ($post->summary ?: $plainContent))), 158);

    view()->share([
        'title'       => $seoTitle,
        'description' => $seoDescription,
        'canonical'   => $postUrl,
        'image'       => $imageUrl ?? asset('public/hero-image.jpg'),
        'ogType'      => 'article',
    ]);

    $jsonLd = [
        '@context' => 'https://schema.org',
        '@graph'   => [
            [
                '@type'           => 'BreadcrumbList',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home',            'item' => url('/')],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog',            'item' => route('blog.index')],
                    ['@type' => 'ListItem', 'position' => 3, 'name' => $post->title,      'item' => $postUrl],
                ],
            ],
            array_filter([
                '@type'            => 'BlogPosting',
                'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $postUrl],
                'headline'         => Str::limit($post->title, 110, ''),
                'description'      => $seoDescription,
                'image'            => $imageUrl ? [$imageUrl] : null,
                'datePublished'    => optional($post->created_at)->toAtomString(),
                'dateModified'     => optional($post->updated_at ?? $post->created_at)->toAtomString(),
                'articleSection'   => $post->category,
                'inLanguage'       => $contentLang,
                'author'           => ['@type' => 'Person', 'name' => $authorName],
                'publisher'        => [
                    '@type' => 'Organization',
                    'name'  => 'Assan Zameen',
                    'logo'  => ['@type' => 'ImageObject', 'url' => asset('public/favicon.png')],
                ],
            ]),
        ],
    ];

    $whatsappShare = 'https://wa.me/?text=' . rawurlencode($post->title . ' - ' . $postUrl);
    $facebookShare = 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode($postUrl);

    $fieldClass = 'w-full bg-slate-800/60 border border-slate-700/60 rounded-xl px-3.5 py-2.5 text-sm text-slate-100 placeholder:text-slate-500 focus:outline-none focus:border-amber-400/70 focus:ring-2 focus:ring-amber-400/15 transition';
@endphp

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-24">

    @push('head')
        <meta property="article:published_time" content="{{ optional($post->created_at)->toAtomString() }}">
        <meta property="article:modified_time" content="{{ optional($post->updated_at ?? $post->created_at)->toAtomString() }}">
        <meta property="article:section" content="{{ $post->category }}">
        <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
        <style>
            /* Article reading typography (Tailwind typography plugin ki zaroorat nahi) */
            .az-article { color: #cbd5e1; font-size: 1.0625rem; overflow-wrap: anywhere; }
            .az-article--rtl { line-height: 2.2; }
            .az-article--ltr { line-height: 1.85; }
            .az-article > * + * { margin-top: 1.1em; }
            .az-article h2 { font-size: 1.55em; font-weight: 700; color: #f8fafc; margin-top: 2em; line-height: 1.5; }
            .az-article h3 { font-size: 1.3em; font-weight: 700; color: #f1f5f9; margin-top: 1.7em; line-height: 1.5; }
            .az-article h4 { font-size: 1.1em; font-weight: 600; color: #f1f5f9; margin-top: 1.5em; }
            .az-article strong, .az-article b { color: #f1f5f9; font-weight: 700; }
            .az-article a { color: #fcd34d; text-decoration: underline; text-underline-offset: 4px; text-decoration-color: rgba(252, 211, 77, .4); }
            .az-article a:hover { color: #fde68a; text-decoration-color: currentColor; }
            .az-article ul { list-style: disc; padding-inline-start: 1.5em; }
            .az-article ol { list-style: decimal; padding-inline-start: 1.5em; }
            .az-article li + li { margin-top: .4em; }
            .az-article li::marker { color: #f59e0b; }
            .az-article img, .az-article video { max-width: 100%; height: auto; border-radius: .75rem; margin-inline: auto; display: block; }
            .az-article iframe { max-width: 100%; border-radius: .75rem; }
            .az-article blockquote { background: rgba(30, 41, 59, .45); border-inline-start: 3px solid #f59e0b; border-radius: .5rem; padding: .8em 1.1em; color: #94a3b8; }
            .az-article table { display: block; width: 100%; overflow-x: auto; border-collapse: collapse; font-size: .95em; }
            .az-article th, .az-article td { border: 1px solid #334155; padding: .55em .8em; text-align: start; }
            .az-article th { background: #1e293b; color: #f8fafc; font-weight: 600; }
            .az-article hr { border: 0; border-top: 1px solid #334155; margin-block: 2em; }
            .az-article code { background: #1e293b; color: #fde68a; padding: .1em .4em; border-radius: .3rem; font-size: .9em; }
            .az-article pre { background: #0b1220; border: 1px solid #1e293b; border-radius: .75rem; padding: 1em; overflow-x: auto; }
            .az-article pre code { background: none; padding: 0; }
        </style>
    @endpush

    {{-- Breadcrumb --}}
    <nav aria-label="Breadcrumb" class="mb-8">
        <ol class="flex flex-wrap items-center gap-2 text-xs text-slate-500">
            <li><a href="{{ url('/') }}" class="hover:text-amber-300 transition-colors">{{ __('Home') }}</a></li>
            <li aria-hidden="true"><i class="fa-solid fa-chevron-right text-[9px] rtl:rotate-180"></i></li>
            <li><a href="{{ route('blog.index') }}" class="hover:text-amber-300 transition-colors">{{ __('Our Blog') }}</a></li>
            <li aria-hidden="true"><i class="fa-solid fa-chevron-right text-[9px] rtl:rotate-180"></i></li>
            <li class="text-slate-300 truncate max-w-[14rem]" aria-current="page">{{ $post->category }}</li>
        </ol>
    </nav>

    <article>
        {{-- Article header --}}
        <header class="space-y-5">
            <p class="inline-flex items-center gap-1.5 bg-amber-400/10 border border-amber-300/20 text-amber-300 text-xs font-semibold px-3 py-1 rounded-full">
                <i class="fa-solid fa-tag text-[10px]" aria-hidden="true"></i>{{ $post->category }}
            </p>

            <h1 class="text-3xl sm:text-4xl font-bold text-slate-50 leading-tight tracking-tight">{{ $post->title }}</h1>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-slate-400 border-b border-slate-800 pb-5">
                <div class="flex flex-wrap items-center gap-x-2.5 gap-y-1">
                    <span class="w-8 h-8 rounded-full bg-amber-500 text-slate-950 flex items-center justify-center font-bold text-xs">
                        {{ mb_strtoupper(mb_substr($authorName, 0, 1)) }}
                    </span>
                    <span class="font-medium text-slate-200">{{ $authorName }}</span>
                    <span aria-hidden="true">•</span>
                    <time datetime="{{ optional($post->created_at)->toDateString() }}">{{ optional($post->created_at)->format('F d, Y') }}</time>
                </div>

                <div class="flex flex-wrap items-center gap-x-4 gap-y-1">
                    <span><i class="fa-regular fa-clock me-1" aria-hidden="true"></i>{{ $post->read_time }} {{ $t('mins read', 'منٹ کا مطالعہ') }}</span>
                    <span><i class="fa-solid fa-eye me-1" aria-hidden="true"></i>{{ number_format($views) }} {{ $t('views', 'ملاحظات') }}</span>
                    @if($reviewCount > 0)
                        <a href="#reviews" class="text-amber-300 font-semibold hover:text-amber-200 transition-colors">
                            <i class="fa-solid fa-star me-1" aria-hidden="true"></i>{{ $avgRating }} ({{ $reviewCount }})
                        </a>
                    @endif
                </div>
            </div>
        </header>

        {{-- Featured image --}}
        @if($imageUrl)
            <figure class="mt-8 rounded-2xl overflow-hidden border border-slate-700/50 bg-slate-800">
                <img src="{{ $imageUrl }}" alt="{{ $post->title }}" width="1200" height="630"
                     loading="eager" fetchpriority="high" decoding="async"
                     class="w-full h-auto max-h-[460px] object-cover">
            </figure>
        @endif

        {{-- Article body --}}
        <div class="az-article az-article--{{ $contentDir }} mt-10 max-w-3xl mx-auto" dir="{{ $contentDir }}" lang="{{ $contentLang }}">
            {!! $contentHtml !!}
        </div>
    </article>

    {{-- Engagement: like + share --}}
    <div x-data="{ copied: false, copy() {
            const url = window.location.href;
            (navigator.clipboard ? navigator.clipboard.writeText(url) : Promise.reject()).catch(() => {
                const el = document.createElement('input'); el.value = url; document.body.appendChild(el); el.select(); document.execCommand('copy'); el.remove();
            }).finally(() => { this.copied = true; setTimeout(() => this.copied = false, 2000) });
         } }"
         class="mt-12 max-w-3xl mx-auto p-5 rounded-2xl bg-slate-800/40 border border-slate-700/50 flex flex-col sm:flex-row items-center justify-between gap-4">

        <button type="button" wire:click="toggleLike" aria-pressed="{{ $hasLiked ? 'true' : 'false' }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-semibold cursor-pointer transition-colors
                       {{ $hasLiked ? 'bg-rose-500 text-white' : 'bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700' }}">
            <i class="fa-solid fa-heart text-sm {{ $hasLiked ? 'text-white' : 'text-rose-400' }}" aria-hidden="true"></i>
            <span>{{ $hasLiked ? $t('Liked!', 'پسند کیا!') : $t('Helpful Article', 'مفید مضمون') }}</span>
            <span class="px-2 py-0.5 rounded-md bg-slate-950/50 font-mono text-[11px]">{{ $likes }}</span>
        </button>

        <div class="flex flex-wrap items-center justify-center gap-2">
            <span class="text-xs text-slate-500 hidden sm:inline">{{ $t('Share this guide:', 'یہ گائیڈ شیئر کریں:') }}</span>
            <a href="{{ $whatsappShare }}" target="_blank" rel="noopener noreferrer"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-500 hover:bg-emerald-400 text-slate-950 rounded-xl text-xs font-semibold transition-colors">
                <i class="fa-brands fa-whatsapp text-sm" aria-hidden="true"></i>WhatsApp
            </a>
            <a href="{{ $facebookShare }}" target="_blank" rel="noopener noreferrer"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 rounded-xl text-xs font-semibold transition-colors">
                <i class="fa-brands fa-facebook-f text-xs" aria-hidden="true"></i>Facebook
            </a>
            <button type="button" @click="copy()"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 rounded-xl text-xs font-semibold cursor-pointer transition-colors">
                <i class="fa-solid text-xs" :class="copied ? 'fa-check text-emerald-300' : 'fa-link'" aria-hidden="true"></i>
                <span x-text="copied ? '{{ $t('Link copied!', 'لنک کاپی ہو گیا!') }}' : '{{ $t('Copy Link', 'لنک کاپی کریں') }}'"></span>
            </button>
        </div>
    </div>

    {{-- Related tools --}}
    <aside class="mt-10 max-w-3xl mx-auto" aria-label="{{ $t('Related tools', 'متعلقہ ٹولز') }}">
        <p class="text-xs font-semibold text-slate-500 mb-3">{{ $t('Free tools you may need', 'آپ کے کام کے مفت ٹولز') }}</p>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            @foreach([
                ['tools.tax-calculator', 'fa-calculator',     $t('Tax & Registry Calculator', 'ٹیکس و رجسٹری کیلکولیٹر')],
                ['tools.land-converter', 'fa-ruler-combined', $t('Patwari Land Converter', 'پٹواری اراضی کنورٹر')],
                ['tools.legal-scanner',  'fa-shield-halved',  $t('AI Property Wakeel', 'AI پراپرٹی وکیل')],
            ] as [$routeName, $icon, $label])
                <a href="{{ route($routeName) }}" wire:navigate class="az-card flex items-center gap-3 p-4 rounded-xl bg-slate-800/40 border border-slate-700/50 text-sm text-slate-200">
                    <span class="w-9 h-9 rounded-lg bg-amber-400/10 text-amber-300 flex items-center justify-center shrink-0"><i class="fa-solid {{ $icon }}" aria-hidden="true"></i></span>
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </aside>

    {{-- Reviews --}}
    <section id="reviews" class="mt-16 max-w-3xl mx-auto pt-8 border-t border-slate-800" aria-labelledby="reviews-heading">

        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 id="reviews-heading" class="text-xl font-bold text-slate-50 flex items-center gap-2.5">
                    <i class="fa-solid fa-comments text-amber-300" aria-hidden="true"></i>{{ $t('Reader Reviews & Discussion', 'قارئین کی رائے اور گفتگو') }}
                </h2>
                <p class="text-xs text-slate-400 mt-1.5">{{ $t('Apni raye ya sawal bina kisi login ke darj karein', 'اپنی رائے یا سوال بغیر لاگ اِن کے درج کریں') }}</p>
            </div>

            @if($reviewCount > 0)
                <div class="flex items-center gap-1.5 bg-slate-800/50 px-3.5 py-2 rounded-xl border border-slate-700/50 shrink-0" dir="ltr">
                    <i class="fa-solid fa-star text-amber-300 text-sm" aria-hidden="true"></i>
                    <span class="text-sm font-bold text-slate-50">{{ $avgRating }}</span>
                    <span class="text-[11px] text-slate-500">/ 5.0</span>
                </div>
            @endif
        </div>

        @if (session()->has('review_success'))
            <div role="status" class="mt-5 p-4 bg-emerald-400/10 border border-emerald-300/20 text-emerald-300 rounded-xl text-xs font-semibold flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-base" aria-hidden="true"></i>
                <span>{{ session('review_success') }}</span>
            </div>
        @endif

        {{-- Review form --}}
        <div class="mt-6 p-6 rounded-2xl bg-slate-800/40 border border-slate-700/50">
            <h3 class="text-sm font-semibold text-slate-100 mb-5">{{ $t('Write Your Review / Question', 'اپنا تبصرہ / سوال لکھیں') }}</h3>

            <form wire:submit="submitReview" class="space-y-5">
                <div>
                    <p id="rating-label" class="text-xs font-medium text-slate-400 mb-2">{{ $t('Your Rating', 'آپ کی ریٹنگ') }} *</p>
                    <div role="radiogroup" aria-labelledby="rating-label" class="flex items-center gap-1.5">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" role="radio" aria-checked="{{ $ratingNow === $i ? 'true' : 'false' }}"
                                    aria-label="{{ $i }} {{ $t($i > 1 ? 'stars' : 'star', 'ستارے') }}"
                                    wire:click="$set('rating', {{ $i }})"
                                    class="text-2xl leading-none transition-transform hover:scale-110 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400/60 rounded cursor-pointer {{ $ratingNow >= $i ? 'text-amber-400' : 'text-slate-700' }}">★</button>
                        @endfor
                        <span class="text-xs font-semibold text-amber-300 ms-2" dir="ltr">({{ $ratingNow }})</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="rev-name" class="block text-xs font-medium text-slate-400 mb-1.5">{{ $t('Your Name', 'آپ کا نام') }} *</label>
                        <input id="rev-name" type="text" wire:model="user_name" autocomplete="name" placeholder="{{ $t('Maslan: Tariq Mahmood', 'مثلاً: طارق محمود') }}" class="{{ $fieldClass }}">
                        @error('user_name') <span class="text-rose-300 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="rev-email" class="block text-xs font-medium text-slate-400 mb-1.5">{{ $t('Email Address (Optional / Private)', 'ای میل (اختیاری / نجی)') }}</label>
                        <input id="rev-email" type="email" wire:model="user_email" autocomplete="email" placeholder="name@example.com" class="{{ $fieldClass }}" dir="ltr">
                        @error('user_email') <span class="text-rose-300 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label for="rev-comment" class="block text-xs font-medium text-slate-400 mb-1.5">{{ $t('Comment / Feedback', 'تبصرہ / رائے') }} *</label>
                    <textarea id="rev-comment" wire:model="comment" rows="4"
                              placeholder="{{ $t('Is article ke baare mein apni raye ya property se mutalliq sawal likhein...', 'اس مضمون کے بارے میں اپنی رائے یا پراپرٹی سے متعلق سوال لکھیں...') }}"
                              class="{{ $fieldClass }} leading-relaxed"></textarea>
                    @error('comment') <span class="text-rose-300 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" wire:loading.attr="disabled" wire:target="submitReview"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-amber-500 hover:bg-amber-400 disabled:opacity-60 text-slate-950 font-semibold text-sm rounded-xl cursor-pointer transition-colors">
                        <i class="fa-solid fa-circle-notch fa-spin hidden" wire:loading.class.remove="hidden" wire:target="submitReview" aria-hidden="true"></i>
                        <span wire:loading.remove wire:target="submitReview">{{ $t('Post Review', 'تبصرہ شائع کریں') }}</span>
                        <span wire:loading wire:target="submitReview">{{ $t('Publishing...', 'شائع ہو رہا ہے...') }}</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Published reviews --}}
        <div class="mt-6 space-y-3">
            @forelse($reviews as $rev)
                <article class="p-4 rounded-xl bg-slate-800/30 border border-slate-700/40">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="w-8 h-8 rounded-full bg-slate-700/60 text-amber-300 flex items-center justify-center font-bold text-xs shrink-0">
                                {{ mb_strtoupper(mb_substr($rev->user_name, 0, 1)) }}
                            </span>
                            <div class="min-w-0">
                                <p class="font-semibold text-slate-100 text-xs truncate">{{ $rev->user_name }}</p>
                                <p class="text-[11px] text-slate-500">{{ $rev->created_at->diffForHumans() }}</p>
                            </div>
                        </div>

                        <div class="text-amber-400 text-sm tracking-wide shrink-0" dir="ltr" role="img" aria-label="{{ $rev->rating }} / 5">
                            @for($s = 1; $s <= 5; $s++)<span aria-hidden="true">{{ $rev->rating >= $s ? '★' : '☆' }}</span>@endfor
                        </div>
                    </div>

                    <p class="text-sm text-slate-300 leading-relaxed mt-3 ps-10">{{ $rev->comment }}</p>
                </article>
            @empty
                <div class="p-8 text-center text-slate-500 rounded-xl bg-slate-800/20 border border-slate-700/40 text-sm">
                    {{ $t('No reviews yet. Be the first to share your thoughts.', 'ابھی کوئی تبصرہ نہیں۔ سب سے پہلے اپنی رائے دیں۔') }}
                </div>
            @endforelse
        </div>
    </section>

    {{-- Back to blog --}}
    <div class="mt-12 max-w-3xl mx-auto">
        <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-amber-300 hover:text-amber-200 transition-colors">
            <i class="fa-solid fa-arrow-left text-xs rtl:rotate-180" aria-hidden="true"></i>{{ $t('Back to all articles', 'تمام مضامین پر واپس') }}
        </a>
    </div>
</div>