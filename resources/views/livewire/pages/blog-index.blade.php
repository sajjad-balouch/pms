@php
    $isUr = app()->getLocale() === 'ur';
    $t    = fn (string $en, string $ur) => $isUr ? $ur : $en;

    $cats = [
        ''                => ['All Articles',     'تمام مضامین',        'fa-table-cells-large'],
        'Property Guides' => ['Property Guides',  'پراپرٹی گائیڈز',     'fa-book-open'],
        'Taxes & Laws'    => ['Taxes & Laws',     'ٹیکس اور قوانین',    'fa-scale-balanced'],
        'Market News'     => ['Market News',      'مارکیٹ کی خبریں',    'fa-chart-line'],
        'Investment Tips' => ['Investment Tips',  'سرمایہ کاری کے مشورے', 'fa-lightbulb'],
    ];
    $catLabel = fn ($c) => isset($cats[$c]) ? ($isUr ? $cats[$c][1] : $cats[$c][0]) : $c;
    $catIcon  = fn ($c) => $cats[$c][2] ?? 'fa-newspaper';

    $activeCat   = (string) ($category ?? '');
    $isPaginated = method_exists($posts, 'total');
    $total       = $isPaginated ? $posts->total() : $posts->count();
    $currentPage = $isPaginated ? $posts->currentPage() : 1;

    // Page 1 par, bina category ke, pehli post "featured" dikhayen
    $featured  = ($currentPage === 1 && $activeCat === '' && $posts->count() >= 3) ? $posts->first() : null;
    $gridPosts = $featured ? $posts->slice(1) : $posts;

    // ---- SEO ----
    $seoTitle = $activeCat !== ''
        ? $t("{$activeCat} – Property Blog | Assan Zameen", "{$catLabel($activeCat)} – پراپرٹی بلاگ | آسان زمین")
        : $t('Property Guides, Taxes & Laws in Pakistan – Blog | Assan Zameen', 'پاکستان میں پراپرٹی گائیڈز، ٹیکس اور قوانین – بلاگ | آسان زمین')
          . ($currentPage > 1 ? ' – ' . $t("Page {$currentPage}", "صفحہ {$currentPage}") : '');

    $seoDescription = $t(
        'Practical guides on property registry, FBR taxes, patwari land measurement and investment trends in Pakistan, from the Assan Zameen editorial team.',
        'پاکستان میں پراپرٹی رجسٹری، FBR ٹیکس، پٹواری پیمائش اور سرمایہ کاری کے رجحانات پر آسان زمین کی ایڈیٹوریل ٹیم کی عملی گائیڈز۔'
    );

    $canonical = $currentPage > 1 && $activeCat === '' ? route('blog.index', ['page' => $currentPage]) : route('blog.index');
    $firstImage = optional($posts->first())->featured_image;

    view()->share([
        'title'       => $seoTitle,
        'description' => $seoDescription,
        'canonical'   => $canonical,
        'robots'      => $activeCat !== '' ? 'noindex, follow' : 'index, follow, max-image-preview:large',
        'image'       => $firstImage ? asset('public/' . $firstImage) : asset('public/hero-image.jpg'),
    ]);

    $blogPosts = [];
    foreach ($posts as $p) {
        $blogPosts[] = array_filter([
            '@type'         => 'BlogPosting',
            'headline'      => $p->title,
            'url'           => route('blog.show', $p->slug),
            'datePublished' => optional($p->created_at)->toAtomString(),
            'image'         => $p->featured_image ? asset('public/' . $p->featured_image) : null,
            'author'        => ['@type' => 'Person', 'name' => $p->author->name ?? 'Editorial Team'],
        ]);
    }

    $jsonLd = [
        '@context' => 'https://schema.org',
        '@graph'   => [
            [
                '@type'           => 'BreadcrumbList',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => route('blog.index')],
                ],
            ],
            [
                '@type'       => 'Blog',
                'name'        => 'Assan Zameen Property Blog',
                'description' => $seoDescription,
                'url'         => route('blog.index'),
                'publisher'   => ['@type' => 'Organization', 'name' => 'Assan Zameen', 'url' => url('/')],
                'blogPost'    => $blogPosts,
            ],
        ],
    ];
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-24">

    @push('head')
        <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
    @endpush

    {{-- Breadcrumb --}}
    <nav aria-label="Breadcrumb" class="mb-6">
        <ol class="flex items-center gap-2 text-xs text-slate-500">
            <li><a href="{{ url('/') }}" class="hover:text-amber-300 transition-colors">{{ __('Home') }}</a></li>
            <li aria-hidden="true"><i class="fa-solid fa-chevron-right text-[9px] rtl:rotate-180"></i></li>
            <li class="text-slate-300" aria-current="page">{{ __('Our Blog') }}</li>
        </ol>
    </nav>

    {{-- Header --}}
    <header class="max-w-3xl">
        <p class="text-amber-300/90 text-xs font-semibold uppercase tracking-[0.18em] mb-3">
            <i class="fa-solid fa-book-open me-1.5" aria-hidden="true"></i>{{ $t('AssanZameen Real Estate Knowledge Hub', 'آسان زمین ریئل اسٹیٹ نالج ہب') }}
        </p>
        <h1 class="text-3xl sm:text-4xl font-bold text-slate-50 tracking-tight leading-tight">
            {{ $t('Pakistan Property Insights, Taxes & Guides', 'پاکستان پراپرٹی انسائٹس، ٹیکس اور گائیڈز') }}
        </h1>
        <p class="text-slate-400 text-sm sm:text-base leading-relaxed mt-3">
            {{ $t('Sarkari registry schedule, FBR tax updates, patwari measurements aur investment trends par authentic maloomat.', 'سرکاری رجسٹری شیڈول، FBR ٹیکس اپ ڈیٹس، پٹواری پیمائش اور سرمایہ کاری کے رجحانات پر مستند معلومات۔') }}
        </p>
    </header>

    {{-- Category filter --}}
    <div role="group" aria-label="{{ $t('Filter by category', 'کیٹیگری کے مطابق فلٹر') }}" class="flex flex-wrap gap-2 mt-8 pb-8 border-b border-slate-800">
        @foreach($cats as $c => $meta)
            @php $isActive = $activeCat === (string) $c; @endphp
            <button type="button" wire:click="filterCategory(@js($c))" aria-pressed="{{ $isActive ? 'true' : 'false' }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold cursor-pointer transition-colors
                           {{ $isActive ? 'bg-amber-500 text-slate-950' : 'bg-slate-800/50 text-slate-300 border border-slate-700/60 hover:border-slate-500 hover:text-white' }}">
                <i class="fa-solid {{ $meta[2] }} text-[11px] {{ $isActive ? '' : 'text-amber-300/80' }}" aria-hidden="true"></i>
                {{ $isUr ? $meta[1] : $meta[0] }}
            </button>
        @endforeach
    </div>

    <div wire:loading.class="opacity-60" wire:target="filterCategory" class="transition-opacity">

        {{-- Featured post --}}
        @if($featured)
            <article class="az-card group relative mt-8 grid grid-cols-1 lg:grid-cols-2 rounded-2xl bg-slate-800/40 border border-slate-700/50 overflow-hidden">
                <div class="relative aspect-[16/10] lg:aspect-auto lg:min-h-[18rem] bg-slate-800 overflow-hidden">
                    @if($featured->featured_image)
                        <img src="{{ asset('public/' . $featured->featured_image) }}" alt="{{ $featured->title }}"
                             width="800" height="500" loading="eager" fetchpriority="high" decoding="async"
                             class="absolute inset-0 w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-500">
                    @else
                        <div class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-slate-800 to-slate-900" aria-hidden="true">
                            <i class="fa-solid fa-newspaper text-5xl text-slate-600"></i>
                        </div>
                    @endif
                </div>

                <div class="p-6 lg:p-8 flex flex-col justify-center gap-4">
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1.5 text-xs text-slate-400">
                        <span class="inline-flex items-center gap-1.5 bg-amber-400/10 text-amber-300 border border-amber-300/20 px-2.5 py-1 rounded-full font-semibold">
                            <i class="fa-solid {{ $catIcon($featured->category) }} text-[10px]" aria-hidden="true"></i>{{ $catLabel($featured->category) }}
                        </span>
                        <time datetime="{{ optional($featured->created_at)->toDateString() }}">{{ optional($featured->created_at)->format('M d, Y') }}</time>
                        @if($featured->read_time)
                            <span aria-hidden="true">•</span>
                            <span>{{ $featured->read_time }} {{ $t('min read', 'منٹ کا مطالعہ') }}</span>
                        @endif
                    </div>

                    <h2 class="text-2xl sm:text-3xl font-bold text-slate-50 leading-snug group-hover:text-amber-300 transition-colors">
                        <a href="{{ route('blog.show', $featured->slug) }}" class="after:absolute after:inset-0">{{ $featured->title }}</a>
                    </h2>
                    <p class="text-sm sm:text-base text-slate-400 leading-relaxed line-clamp-3">{{ $featured->summary }}</p>

                    <div class="flex items-center justify-between text-xs pt-2">
                        <span class="text-slate-500">{{ $t('By', 'از') }} {{ $featured->author->name ?? $t('Editorial Team', 'ایڈیٹوریل ٹیم') }}</span>
                        <span class="inline-flex items-center gap-1.5 font-semibold text-amber-300">
                            {{ $t('Read More', 'مزید پڑھیں') }}
                            <i class="fa-solid fa-arrow-right text-[10px] rtl:rotate-180 group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform" aria-hidden="true"></i>
                        </span>
                    </div>
                </div>
            </article>
        @endif

        {{-- Articles grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mt-8">
            @forelse($gridPosts as $p)
                <article wire:key="post-{{ $p->id ?? $p->slug }}" class="az-card group relative flex flex-col rounded-2xl bg-slate-800/40 border border-slate-700/50 overflow-hidden">

                    <div class="relative aspect-[16/10] bg-slate-800 overflow-hidden">
                        @if($p->featured_image)
                            <img src="{{ asset('public/' . $p->featured_image) }}" alt="{{ $p->title }}"
                                 width="640" height="400" loading="lazy" decoding="async"
                                 class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-slate-800 to-slate-900" aria-hidden="true">
                                <i class="fa-solid fa-newspaper text-4xl text-slate-600"></i>
                            </div>
                        @endif
                        <span class="absolute top-3 ltr:left-3 rtl:right-3 inline-flex items-center gap-1.5 bg-slate-950/75 backdrop-blur text-amber-300 text-[11px] font-semibold px-2.5 py-1 rounded-full border border-amber-300/20">
                            <i class="fa-solid {{ $catIcon($p->category) }} text-[10px]" aria-hidden="true"></i>{{ $catLabel($p->category) }}
                        </span>
                    </div>

                    <div class="flex flex-col flex-1 gap-3 p-5">
                        <div class="flex items-center gap-2 text-[11px] text-slate-500">
                            <time datetime="{{ optional($p->created_at)->toDateString() }}">{{ optional($p->created_at)->format('M d, Y') }}</time>
                            @if($p->read_time)
                                <span aria-hidden="true">•</span>
                                <span>{{ $p->read_time }} {{ $t('min read', 'منٹ کا مطالعہ') }}</span>
                            @endif
                        </div>

                        <h2 class="text-base font-semibold text-slate-50 leading-snug line-clamp-2 group-hover:text-amber-300 transition-colors">
                            <a href="{{ route('blog.show', $p->slug) }}" class="after:absolute after:inset-0">{{ $p->title }}</a>
                        </h2>

                        <p class="text-sm text-slate-400 leading-relaxed line-clamp-3">{{ $p->summary }}</p>

                        <div class="mt-auto pt-4 border-t border-slate-700/50 flex items-center justify-between text-xs">
                            <span class="text-slate-500 truncate">{{ $t('By', 'از') }} {{ $p->author->name ?? $t('Editorial Team', 'ایڈیٹوریل ٹیم') }}</span>
                            <span class="inline-flex items-center gap-1.5 font-semibold text-amber-300 shrink-0">
                                {{ $t('Read More', 'مزید پڑھیں') }}
                                <i class="fa-solid fa-arrow-right text-[10px] rtl:rotate-180 group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform" aria-hidden="true"></i>
                            </span>
                        </div>
                    </div>
                </article>
            @empty
                @unless($featured)
                    <div class="col-span-full rounded-2xl bg-slate-800/30 border border-slate-700/40 px-6 py-16 text-center">
                        <i class="fa-solid fa-newspaper text-4xl mb-4 text-slate-600" aria-hidden="true"></i>
                        <p class="text-slate-200 font-medium">{{ $t('No articles found matching criteria.', 'اس کیٹیگری میں کوئی مضمون نہیں ملا۔') }}</p>
                        @if($activeCat !== '')
                            <button type="button" wire:click="filterCategory('')"
                                    class="mt-5 inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-semibold text-sm px-5 py-2.5 rounded-xl cursor-pointer transition-colors">
                                <i class="fa-solid fa-rotate-left text-xs" aria-hidden="true"></i>{{ $t('Show all articles', 'تمام مضامین دکھائیں') }}
                            </button>
                        @endif
                    </div>
                @endunless
            @endforelse
        </div>
    </div>

    @if($isPaginated && $posts->hasPages())
        <nav class="mt-10" aria-label="Pagination">
            {{ $posts->links() }}
        </nav>
    @endif

    {{-- Related tools: blog ke topics (tax, registry, patwari) se seedha jura hua --}}
    <section class="mt-20" aria-labelledby="blog-tools-heading">
        <div class="max-w-2xl mb-6">
            <h2 id="blog-tools-heading" class="text-xl font-bold text-slate-50">{{ $t('Put the guides to use', 'گائیڈز کو استعمال میں لائیں') }}</h2>
            <p class="text-sm text-slate-400 mt-2">{{ $t('Free tools for the taxes, registry and land measurements covered in our articles.', 'ہمارے مضامین میں بیان کردہ ٹیکس، رجسٹری اور زمین کی پیمائش کے مفت ٹولز۔') }}</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach([
                ['tools.tax-calculator',  'fa-calculator',      $t('Tax & Registry Calculator', 'ٹیکس و رجسٹری کیلکولیٹر'), 'Stamp duty, TMA, FBR 236-C/K'],
                ['tools.land-converter',  'fa-ruler-combined',  $t('Patwari Land Converter', 'پٹواری اراضی کنورٹر'),         $t('Karam, sarsahi & marla', 'کرم، سرساہی و مرلہ')],
                ['tools.legal-scanner',   'fa-shield-halved',   $t('AI Property Wakeel', 'AI پراپرٹی وکیل'),                 $t('Fard, registry & title risk', 'فرد، رجسٹری و ٹائٹل رسک')],
            ] as [$routeName, $icon, $label, $desc])
                <a href="{{ route($routeName) }}" wire:navigate class="az-card flex items-start gap-4 p-5 rounded-2xl bg-slate-800/40 border border-slate-700/50">
                    <span class="w-11 h-11 rounded-xl bg-amber-400/10 text-amber-300 flex items-center justify-center shrink-0"><i class="fa-solid {{ $icon }}" aria-hidden="true"></i></span>
                    <span class="min-w-0">
                        <span class="block text-sm font-semibold text-slate-50">{{ $label }}</span>
                        <span class="block text-xs text-slate-400 mt-1">{{ $desc }}</span>
                    </span>
                </a>
            @endforeach
        </div>
    </section>
</div>