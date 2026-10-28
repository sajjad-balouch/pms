@php
    $isUr = app()->getLocale() === 'ur';
    $t    = fn (string $en, string $ur) => $isUr ? $ur : $en;

    $region = 'Rahim Yar Khan'; // SEO title/description mein use hota hai

    $q            = trim((string) ($search ?? ''));
    $hasSearch    = $q !== '';
    $isPaginated  = method_exists($schemes, 'total');
    $total        = $isPaginated ? $schemes->total() : $schemes->count();
    $currentPage  = $isPaginated ? $schemes->currentPage() : 1;
    $baseUrl      = route('housing-schemes');

    // ---- SEO ----
    // Search results aur sirf-filter wale URLs index nahi hone chahiyen (duplicate content)
    $seoTitle = $hasSearch
        ? $t("Search results for “{$q}” – Housing Schemes | Assan Zameen", "“{$q}” کے نتائج – ہاؤسنگ سکیمیں | آسان زمین")
        : "Housing Schemes in {$region} – Plots, Maps & NOC | Assan Zameen" . ($currentPage > 1 ? " – Page {$currentPage}" : '');

    $seoDescription = "Browse housing schemes and societies in {$region} with location maps, NOC details and available plots. Compare projects and check before you buy on Assan Zameen.";

    $seoCanonical = $currentPage > 1 && !$hasSearch ? $baseUrl . '?page=' . $currentPage : $baseUrl;
    $firstCover   = optional($schemes->first())->cover_image;

    view()->share([
        'title'       => $seoTitle,
        'description' => $seoDescription,
        'canonical'   => $seoCanonical,
        'robots'      => $hasSearch ? 'noindex, follow' : 'index, follow, max-image-preview:large',
        'image'       => $firstCover ? asset('storage/' . $firstCover) : asset('public/hero-image.jpg'),
    ]);

    // ---- Structured data ----
    $itemList = [];
    foreach ($schemes as $i => $s) {
        $itemList[] = [
            '@type'    => 'ListItem',
            'position' => ($isPaginated ? ($schemes->firstItem() ?? 1) : 1) + $i,
            'url'      => route('housing-schemes.show', $s->id),
            'name'     => $s->name,
        ];
    }

    $jsonLd = [
        '@context' => 'https://schema.org',
        '@graph'   => [
            [
                '@type'           => 'BreadcrumbList',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home',            'item' => url('/')],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => 'Housing Schemes', 'item' => $baseUrl],
                ],
            ],
            [
                '@type'           => 'CollectionPage',
                'name'            => "Housing Schemes in {$region}",
                'description'     => $seoDescription,
                'url'             => $seoCanonical,
                'mainEntity'      => ['@type' => 'ItemList', 'numberOfItems' => $total, 'itemListElement' => $itemList],
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
            <li class="text-slate-300" aria-current="page">{{ __('Housing Schemes') }}</li>
        </ol>
    </nav>

    {{-- Page header --}}
    <header class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 pb-8 border-b border-slate-800">
        <div class="max-w-2xl">
            <p class="text-amber-300/90 text-xs font-semibold uppercase tracking-[0.18em] mb-3">
                <i class="fa-solid fa-city me-1.5" aria-hidden="true"></i>{{ __('Approved Projects') }}
            </p>
            <h1 class="text-3xl sm:text-4xl font-bold text-slate-50 tracking-tight">{{ __('Housing Schemes') }}</h1>
            <p class="text-slate-400 text-sm sm:text-base leading-relaxed mt-3">
                {{ __('Explore government-verified LDA, CDA, and RDA approved housing societies.') }}
            </p>
        </div>

        {{-- Search --}}
        <form role="search" wire:submit.prevent class="w-full lg:w-80">
            <label for="scheme-search" class="sr-only">{{ $t('Search society or location', 'سوسائٹی یا مقام تلاش کریں') }}</label>
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute top-1/2 -translate-y-1/2 ltr:left-4 rtl:right-4 text-slate-500 text-sm pointer-events-none" aria-hidden="true"></i>
                <input id="scheme-search" type="search" wire:model.live.debounce.300ms="search"
                       placeholder="{{ $t('Search society or location...', 'سوسائٹی یا مقام تلاش کریں...') }}"
                       autocomplete="off"
                       class="w-full bg-slate-800/60 border border-slate-700/60 rounded-xl ltr:pl-11 rtl:pr-11 ltr:pr-10 rtl:pl-10 py-3 text-sm text-slate-100 placeholder:text-slate-500 focus:outline-none focus:border-amber-400/70 focus:ring-2 focus:ring-amber-400/15 transition">

                <span wire:loading wire:target="search" class="absolute top-1/2 -translate-y-1/2 ltr:right-4 rtl:left-4 text-amber-300 text-xs">
                    <i class="fa-solid fa-circle-notch fa-spin" aria-hidden="true"></i>
                </span>
                @if($hasSearch)
                    <button type="button" wire:click="$set('search', '')" wire:loading.remove wire:target="search"
                            aria-label="{{ $t('Clear search', 'تلاش صاف کریں') }}"
                            class="absolute top-1/2 -translate-y-1/2 ltr:right-3 rtl:left-3 w-6 h-6 rounded-full text-slate-400 hover:text-white hover:bg-slate-700 flex items-center justify-center cursor-pointer transition-colors">
                        <i class="fa-solid fa-xmark text-xs" aria-hidden="true"></i>
                    </button>
                @endif
            </div>
        </form>
    </header>

    {{-- Result summary --}}
    <p class="text-xs text-slate-500 mt-5 mb-6" aria-live="polite">
        @if($total > 0)
            @if($hasSearch)
                {{ $t("{$total} result(s) for “{$q}”", "“{$q}” کے {$total} نتائج") }}
            @else
                {{ $t("{$total} housing scheme(s)", "کل {$total} ہاؤسنگ سکیمیں") }}
            @endif
        @endif
    </p>

    {{-- Schemes grid --}}
    <div wire:loading.class="opacity-60" wire:target="search"
         class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 transition-opacity">

        @forelse($schemes as $scheme)
            @php
                $rawMap = trim($scheme->google_map_url ?? '');
                $mapUrl = preg_match('#^https?://#i', $rawMap)
                    ? $rawMap
                    : 'https://www.google.com/maps/search/?api=1&query=' . urlencode(trim(($scheme->location ?? '') . ', ' . ($scheme->city ?? ''), ' ,'));

                $plots   = (int) ($scheme->available_plots_count ?? 0);
                $altText = trim($scheme->name . ($scheme->location ? ' – ' . $scheme->location : '') . ($scheme->city ? ', ' . $scheme->city : ''));
            @endphp

            <article wire:key="scheme-{{ $scheme->id }}"
                     class="az-card group relative flex flex-col rounded-2xl bg-slate-800/40 border border-slate-700/50 overflow-hidden">

                {{-- Cover --}}
                <div class="relative aspect-[16/10] bg-slate-800 overflow-hidden">
                    @if($scheme->cover_image)
                        <img src="{{ asset('storage/' . $scheme->cover_image) }}"
                             alt="{{ $altText }}"
                             width="640" height="400"
                             loading="{{ $loop->first ? 'eager' : 'lazy' }}" decoding="async"
                             class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-500">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-slate-800 to-slate-900" aria-hidden="true">
                            <i class="fa-solid fa-city text-4xl text-slate-600"></i>
                        </div>
                    @endif
                    <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-slate-950/60 to-transparent pointer-events-none"></div>

                    <span class="absolute top-3 ltr:left-3 rtl:right-3 inline-flex items-center gap-1.5 bg-slate-950/75 backdrop-blur text-amber-300 text-[11px] font-semibold px-2.5 py-1 rounded-full border border-amber-300/20">
                        <i class="fa-solid fa-circle-check text-[10px]" aria-hidden="true"></i>
                        {{ $scheme->noc_number ?: __('Approved') }}
                    </span>

                    <a href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer"
                       aria-label="{{ $t('View map for', 'نقشہ دیکھیں:') }} {{ $scheme->name }}"
                       class="absolute top-3 ltr:right-3 rtl:left-3 z-10 inline-flex items-center gap-1.5 bg-slate-950/75 hover:bg-emerald-500 hover:text-white backdrop-blur text-emerald-300 text-[11px] font-semibold px-2.5 py-1 rounded-full border border-emerald-300/20 transition-colors">
                        <i class="fa-solid fa-location-dot text-[10px]" aria-hidden="true"></i>{{ __('Map') }}
                    </a>
                </div>

                {{-- Body --}}
                <div class="flex flex-col flex-1 gap-4 p-5">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-50 group-hover:text-amber-300 transition-colors">
                            <a href="{{ route('housing-schemes.show', $scheme->id) }}"
                               class="after:absolute after:inset-0 focus:outline-none focus-visible:after:ring-2 focus-visible:after:ring-amber-400/60 after:rounded-2xl">
                                {{ $scheme->name }}
                            </a>
                        </h2>
                        @if($scheme->location)
                            <p class="text-xs text-slate-400 flex items-start gap-1.5 mt-1.5">
                                <i class="fa-solid fa-location-dot text-amber-300/80 mt-0.5" aria-hidden="true"></i>
                                <span>{{ $scheme->location }}</span>
                            </p>
                        @endif
                    </div>

                    <dl class="grid grid-cols-2 gap-3 text-xs rounded-xl bg-slate-900/50 border border-slate-700/40 p-3">
                        <div>
                            <dt class="text-slate-500">{{ __('Available Plots:') }}</dt>
                            <dd class="mt-0.5 font-semibold {{ $plots > 0 ? 'text-emerald-300' : 'text-slate-400' }}">{{ $plots }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">{{ __('City:') }}</dt>
                            <dd class="mt-0.5 font-semibold text-slate-200">{{ $scheme->city ?: '—' }}</dd>
                        </div>
                    </dl>

                    <span class="mt-auto inline-flex items-center justify-between text-sm font-semibold text-amber-300">
                        {{ __('View Complete Details') }}
                        <i class="fa-solid fa-arrow-right text-xs rtl:rotate-180 group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform" aria-hidden="true"></i>
                    </span>
                </div>
            </article>
        @empty
            <div class="col-span-full rounded-2xl bg-slate-800/30 border border-slate-700/40 px-6 py-16 text-center">
                <i class="fa-solid fa-building-circle-exclamation text-4xl mb-4 text-slate-600" aria-hidden="true"></i>
                <p class="text-slate-200 font-medium">
                    @if($hasSearch)
                        {{ $t("No housing schemes found for “{$q}”.", "“{$q}” کے لیے کوئی ہاؤسنگ سکیم نہیں ملی۔") }}
                    @else
                        {{ $t('No housing schemes found.', 'کوئی ہاؤسنگ سکیم نہیں ملی۔') }}
                    @endif
                </p>
                @if($hasSearch)
                    <p class="text-sm text-slate-500 mt-1">{{ $t('Try a different name or location.', 'کوئی اور نام یا مقام آزمائیں۔') }}</p>
                    <button type="button" wire:click="$set('search', '')"
                            class="mt-5 inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-semibold text-sm px-5 py-2.5 rounded-xl cursor-pointer transition-colors">
                        <i class="fa-solid fa-rotate-left text-xs" aria-hidden="true"></i>{{ $t('Show all schemes', 'تمام سکیمیں دکھائیں') }}
                    </button>
                @endif
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($isPaginated && $schemes->hasPages())
        <nav class="mt-10" aria-label="Pagination">
            {{ $schemes->links() }}
        </nav>
    @endif

    {{-- Buyer guide: useful content + internal links (sirf pehle page par, search ke baghair) --}}
    @if(!$hasSearch && $currentPage === 1)
        <section class="mt-20" aria-labelledby="scheme-guide-heading">
            <div class="max-w-2xl mb-8">
                <p class="text-amber-300/90 text-xs font-semibold uppercase tracking-[0.18em] mb-3">{{ $t('Buyer guide', 'خریدار گائیڈ') }}</p>
                <h2 id="scheme-guide-heading" class="text-2xl font-bold text-slate-50">
                    {{ $t('How to check a housing scheme before you buy', 'پلاٹ خریدنے سے پہلے ہاؤسنگ سکیم کیسے چیک کریں') }}
                </h2>
                <p class="text-slate-400 text-sm leading-relaxed mt-3">
                    {{ $t('A few quick checks can save you from illegal or unapproved schemes.', 'چند آسان چیکس آپ کو غیر قانونی یا غیر منظور شدہ سکیموں سے بچا سکتے ہیں۔') }}
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('tools.society-directory') }}" wire:navigate class="az-card p-5 rounded-2xl bg-slate-800/40 border border-slate-700/50 block">
                    <span class="w-10 h-10 rounded-xl bg-amber-400/10 text-amber-300 flex items-center justify-center mb-3"><i class="fa-solid fa-building-circle-check" aria-hidden="true"></i></span>
                    <h3 class="text-sm font-semibold text-slate-50">{{ $t('1. Confirm the NOC', '1. NOC کی تصدیق کریں') }}</h3>
                    <p class="text-xs text-slate-400 leading-relaxed mt-1.5">{{ $t('Check the approval status in the Society NOC Directory.', 'سوسائٹی NOC ڈائریکٹری میں منظوری کی حیثیت دیکھیں۔') }}</p>
                </a>
                <div class="p-5 rounded-2xl bg-slate-800/40 border border-slate-700/50">
                    <span class="w-10 h-10 rounded-xl bg-amber-400/10 text-amber-300 flex items-center justify-center mb-3"><i class="fa-solid fa-map-location-dot" aria-hidden="true"></i></span>
                    <h3 class="text-sm font-semibold text-slate-50">{{ $t('2. Visit the site', '2. جگہ کا وزٹ کریں') }}</h3>
                    <p class="text-xs text-slate-400 leading-relaxed mt-1.5">{{ $t('Use the Map button to find the location and see the development on the ground.', 'نقشہ بٹن سے مقام تلاش کریں اور موقع پر ترقیاتی کام خود دیکھیں۔') }}</p>
                </div>
                <a href="{{ route('tools.legal-scanner') }}" wire:navigate class="az-card p-5 rounded-2xl bg-slate-800/40 border border-slate-700/50 block">
                    <span class="w-10 h-10 rounded-xl bg-amber-400/10 text-amber-300 flex items-center justify-center mb-3"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i></span>
                    <h3 class="text-sm font-semibold text-slate-50">{{ $t('3. Check the title', '3. ٹائٹل چیک کریں') }}</h3>
                    <p class="text-xs text-slate-400 leading-relaxed mt-1.5">{{ $t('Scan the fard and registry for title risks before paying.', 'ادائیگی سے پہلے فرد اور رجسٹری کے ٹائٹل رسک چیک کریں۔') }}</p>
                </a>
                <a href="{{ route('tools.tax-calculator') }}" wire:navigate class="az-card p-5 rounded-2xl bg-slate-800/40 border border-slate-700/50 block">
                    <span class="w-10 h-10 rounded-xl bg-amber-400/10 text-amber-300 flex items-center justify-center mb-3"><i class="fa-solid fa-calculator" aria-hidden="true"></i></span>
                    <h3 class="text-sm font-semibold text-slate-50">{{ $t('4. Plan the costs', '4. اخراجات کا حساب لگائیں') }}</h3>
                    <p class="text-xs text-slate-400 leading-relaxed mt-1.5">{{ $t('Calculate stamp duty, TMA and FBR taxes in advance.', 'اسٹامپ ڈیوٹی، TMA اور FBR ٹیکس پہلے سے حساب کریں۔') }}</p>
                </a>
            </div>
        </section>
    @endif
</div>