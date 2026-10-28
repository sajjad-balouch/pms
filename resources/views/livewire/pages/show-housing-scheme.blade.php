@php
    use Illuminate\Support\Str;

    $isUr = app()->getLocale() === 'ur';
    $t    = fn (string $en, string $ur) => $isUr ? $ur : $en;

    // ---- Basic data ----
    $cityName   = $scheme->city?->name ?? (is_string($scheme->city) ? $scheme->city : '');
    $location   = trim((string) ($scheme->location ?? ''));
    $plotsCount = (int) ($scheme->available_plots_count ?? 0);
    $nocLabel   = $scheme->noc_number ?: null;
    $addressLine = trim($location . ($cityName ? ', ' . $cityName : ''), ' ,');

    $rawMap = trim((string) ($scheme->google_map_url ?? ''));
    $mapUrl = preg_match('#^https?://#i', $rawMap)
        ? $rawMap
        : 'https://www.google.com/maps/dir/?api=1&destination=' . urlencode($addressLine);

    $hasMaster = !empty($scheme->master_plan_map);
    $gallery   = is_array($scheme->gallery_images) ? array_values(array_filter($scheme->gallery_images)) : [];

    // Lightbox images: master plan pehle, phir gallery
    $images = [];
    if ($hasMaster) {
        $images[] = ['src' => asset('public/' . $scheme->master_plan_map), 'title' => $scheme->name . ' – ' . $t('Official Master Plan', 'سرکاری ماسٹر پلان')];
    }
    foreach ($gallery as $n => $photo) {
        $images[] = ['src' => asset('public/' . $photo), 'title' => $scheme->name . ' – ' . $t('Site Photo', 'سائٹ کی تصویر') . ' #' . ($n + 1)];
    }
    $galleryOffset = $hasMaster ? 1 : 0;
    $visibleThumbs = 6;

    // Price helper: 8300000 -> "83 Lakh"
    $shortPrice = function ($n) use ($isUr) {
        $n = (float) $n;
        if ($n >= 10000000) return rtrim(rtrim(number_format($n / 10000000, 2), '0'), '.') . ($isUr ? ' کروڑ' : ' Crore');
        if ($n >= 100000)   return rtrim(rtrim(number_format($n / 100000, 2), '0'), '.') . ($isUr ? ' لاکھ' : ' Lakh');
        return null;
    };

    $statusMap = [
        'available' => [$t('Available', 'دستیاب'),     'bg-emerald-400/10 text-emerald-300 ring-emerald-300/20'],
        'sold'      => [$t('Sold', 'فروخت شدہ'),        'bg-rose-400/10 text-rose-300 ring-rose-300/20'],
        'booked'    => [$t('Booked', 'بُک'),            'bg-amber-400/10 text-amber-300 ring-amber-300/20'],
        'reserved'  => [$t('Reserved', 'ریزرو'),        'bg-amber-400/10 text-amber-300 ring-amber-300/20'],
    ];

    // ---- SEO ----
    $searchPlotTerm = trim((string) ($searchPlot ?? ''));
    $hasPlotSearch  = $searchPlotTerm !== '';
    $plotPage       = method_exists($plots, 'currentPage') ? $plots->currentPage() : 1;
    $baseUrl        = route('housing-schemes.show', $scheme->id);
    $canonical      = $plotPage > 1 && !$hasPlotSearch ? $baseUrl . '?page=' . $plotPage : $baseUrl;

    $seoTitle = $scheme->name . ' – Plots, Map & NOC Details' . ($cityName ? " in {$cityName}" : '') . ' | Assan Zameen'
              . ($plotPage > 1 ? " (Page {$plotPage})" : '');

    $seoDescription = Str::limit(
        $scheme->name . ($addressLine ? " in {$addressLine}" : '') . '. '
        . ($plotsCount > 0 ? "{$plotsCount} plots available. " : '')
        . 'View the master plan, photos, location map and plot prices on Assan Zameen.',
        158
    );

    $shareImage = $images[0]['src'] ?? ($scheme->cover_image ? asset('storage/' . $scheme->cover_image) : asset('public/hero-image.jpg'));

    view()->share([
        'title'       => $seoTitle,
        'description' => $seoDescription,
        'canonical'   => $canonical,
        'image'       => $shareImage,
        'ogType'      => 'article',
        'robots'      => $hasPlotSearch ? 'noindex, follow' : 'index, follow, max-image-preview:large',
    ]);

    // ---- Structured data ----
    $place = array_filter([
        '@type'       => 'Place',
        '@id'         => $baseUrl . '#place',
        'name'        => $scheme->name,
        'description' => $seoDescription,
        'url'         => $baseUrl,
        'hasMap'      => $mapUrl,
        'image'       => array_slice(array_column($images, 'src'), 0, 6) ?: null,
        'address'     => array_filter([
            '@type'           => 'PostalAddress',
            'streetAddress'   => $location ?: null,
            'addressLocality' => $cityName ?: null,
            'addressCountry'  => 'PK',
        ]),
    ]);

    $jsonLd = [
        '@context' => 'https://schema.org',
        '@graph'   => [
            [
                '@type'           => 'BreadcrumbList',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home',            'item' => url('/')],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => 'Housing Schemes', 'item' => route('housing-schemes')],
                    ['@type' => 'ListItem', 'position' => 3, 'name' => $scheme->name,     'item' => $baseUrl],
                ],
            ],
            $place,
        ],
    ];

    $whatsappShare = 'https://wa.me/?text=' . rawurlencode($scheme->name . ' – ' . $baseUrl);
@endphp

<div x-data="{
        open: false,
        i: 0,
        images: @js($images),
        get cur() { return this.images[this.i] ?? { src: '', title: '' } },
        show(n) { this.i = n; this.open = true },
        next() { this.i = (this.i + 1) % this.images.length },
        prev() { this.i = (this.i - 1 + this.images.length) % this.images.length }
     }"
     @keydown.escape.window="open = false"
     @keydown.arrow-right.window="open && next()"
     @keydown.arrow-left.window="open && prev()"
     class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-24">

    @push('head')
        <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
    @endpush

    {{-- Breadcrumb --}}
    <nav aria-label="Breadcrumb" class="mb-6">
        <ol class="flex flex-wrap items-center gap-2 text-xs text-slate-500">
            <li><a href="{{ url('/') }}" class="hover:text-amber-300 transition-colors">{{ __('Home') }}</a></li>
            <li aria-hidden="true"><i class="fa-solid fa-chevron-right text-[9px] rtl:rotate-180"></i></li>
            <li><a href="{{ route('housing-schemes') }}" class="hover:text-amber-300 transition-colors">{{ __('Housing Schemes') }}</a></li>
            <li aria-hidden="true"><i class="fa-solid fa-chevron-right text-[9px] rtl:rotate-180"></i></li>
            <li class="text-slate-300 truncate max-w-[14rem]" aria-current="page">{{ $scheme->name }}</li>
        </ol>
    </nav>

    {{-- Scheme header --}}
    <header class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 rounded-2xl bg-slate-800/40 border border-slate-700/50 p-6 lg:p-8">
            <p class="inline-flex items-center gap-1.5 text-amber-300 text-xs font-semibold bg-amber-400/10 border border-amber-300/20 px-3 py-1 rounded-full">
                <i class="fa-solid fa-circle-check text-[10px]" aria-hidden="true"></i>
                {{ $nocLabel ? 'NOC: ' . $nocLabel : __('Approved') }}
            </p>

            <h1 class="text-3xl sm:text-4xl font-bold text-slate-50 tracking-tight mt-4">{{ $scheme->name }}</h1>

            @if($addressLine)
                <p class="text-slate-400 text-sm flex items-start gap-2 mt-3">
                    <i class="fa-solid fa-location-dot text-amber-300/80 mt-1" aria-hidden="true"></i>
                    <span>{{ $addressLine }}</span>
                </p>
            @endif

            <dl class="grid grid-cols-2 sm:grid-cols-3 gap-3 mt-7">
                <div class="rounded-xl bg-slate-900/50 border border-slate-700/40 p-4">
                    <dt class="text-[11px] text-slate-500 font-medium">{{ $t('Available Plots', 'دستیاب پلاٹس') }}</dt>
                    <dd class="text-xl font-bold mt-1 {{ $plotsCount > 0 ? 'text-emerald-300' : 'text-slate-300' }}">{{ $plotsCount }}</dd>
                </div>
                <div class="rounded-xl bg-slate-900/50 border border-slate-700/40 p-4">
                    <dt class="text-[11px] text-slate-500 font-medium">{{ $t('Total Area', 'کل رقبہ') }}</dt>
                    <dd class="text-xl font-bold text-slate-100 mt-1">{{ $scheme->total_area ?: '—' }}</dd>
                </div>
                <div class="rounded-xl bg-slate-900/50 border border-slate-700/40 p-4 col-span-2 sm:col-span-1">
                    <dt class="text-[11px] text-slate-500 font-medium">{{ $t('City', 'شہر') }}</dt>
                    <dd class="text-xl font-bold text-slate-100 mt-1">{{ $cityName ?: '—' }}</dd>
                </div>
            </dl>
        </div>

        {{-- Actions --}}
        <aside class="rounded-2xl bg-slate-800/40 border border-slate-700/50 p-6 flex flex-col gap-4" aria-label="{{ $t('Location and sharing', 'مقام اور شیئر') }}">
            <div class="flex items-center gap-3">
                <span class="w-11 h-11 rounded-xl bg-amber-400/10 text-amber-300 flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-map-location-dot" aria-hidden="true"></i>
                </span>
                <div>
                    <h2 class="text-sm font-semibold text-slate-50">{{ $t('Location Guidance', 'مقام کی رہنمائی') }}</h2>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $t('Navigate to the society via Google Maps.', 'گوگل میپس سے سوسائٹی تک پہنچیں۔') }}</p>
                </div>
            </div>

            <a href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer"
               class="inline-flex items-center justify-center gap-2 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-semibold text-sm py-3 rounded-xl transition-colors">
                <i class="fa-solid fa-route text-xs" aria-hidden="true"></i>{{ $t('Open Google Maps', 'گوگل میپس کھولیں') }}
            </a>
            <a href="{{ $whatsappShare }}" target="_blank" rel="noopener noreferrer"
               class="inline-flex items-center justify-center gap-2 border border-slate-600 hover:border-emerald-400/60 hover:text-emerald-300 text-slate-200 font-semibold text-sm py-3 rounded-xl transition-colors">
                <i class="fa-brands fa-whatsapp text-base" aria-hidden="true"></i>{{ $t('Share on WhatsApp', 'واٹس ایپ پر شیئر کریں') }}
            </a>

            <div class="mt-auto pt-4 border-t border-slate-700/50 text-xs space-y-2">
                <p class="text-slate-500 font-medium">{{ $t('Check before you buy', 'خریدنے سے پہلے چیک کریں') }}</p>
                <a href="{{ route('tools.society-directory') }}" wire:navigate class="flex items-center gap-2 text-slate-300 hover:text-amber-300 transition-colors">
                    <i class="fa-solid fa-building-circle-check text-amber-300/80 w-4 text-center" aria-hidden="true"></i>{{ $t('Society NOC Directory', 'سوسائٹی NOC ڈائریکٹری') }}
                </a>
                <a href="{{ route('tools.legal-scanner') }}" wire:navigate class="flex items-center gap-2 text-slate-300 hover:text-amber-300 transition-colors">
                    <i class="fa-solid fa-shield-halved text-amber-300/80 w-4 text-center" aria-hidden="true"></i>{{ $t('AI Property Wakeel', 'AI پراپرٹی وکیل') }}
                </a>
            </div>
        </aside>
    </header>

    {{-- Master plan & gallery --}}
    @if($hasMaster || count($gallery) > 0)
        <section class="mt-14" aria-labelledby="gallery-heading">
            <div class="mb-6">
                <h2 id="gallery-heading" class="text-2xl font-bold text-slate-50">{{ $t('Master Plan & Society Gallery', 'ماسٹر پلان اور سوسائٹی گیلری') }}</h2>
                <p class="text-sm text-slate-400 mt-1.5">
                    {{ $t('Visual layout and on-ground development snapshots of', 'ترقیاتی کام اور لے آؤٹ کی تصاویر:') }} {{ $scheme->name }}.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
                @if($hasMaster)
                    <figure class="{{ count($gallery) > 0 ? 'lg:col-span-5' : 'lg:col-span-12' }} rounded-2xl bg-slate-800/40 border border-slate-700/50 p-4">
                        <button type="button" @click="show(0)"
                                aria-label="{{ $t('Zoom master plan', 'ماسٹر پلان بڑا کریں') }}"
                                class="relative group block w-full rounded-xl overflow-hidden border border-slate-700/50 bg-slate-900 {{ count($gallery) > 0 ? 'aspect-[4/3]' : 'aspect-[16/9]' }} cursor-zoom-in focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400/60">
                            <img src="{{ asset('public/' . $scheme->master_plan_map) }}"
                                 alt="{{ $scheme->name }} {{ $t('master plan', 'ماسٹر پلان') }}"
                                 width="800" height="600" loading="lazy" decoding="async"
                                 class="w-full h-full object-cover group-hover:scale-[1.02] transition-transform duration-300">
                            <span class="absolute bottom-3 ltr:right-3 rtl:left-3 inline-flex items-center gap-1.5 bg-slate-950/75 backdrop-blur text-amber-300 text-[11px] font-semibold px-2.5 py-1 rounded-full">
                                <i class="fa-solid fa-up-right-and-down-left-from-center text-[10px]" aria-hidden="true"></i>{{ $t('Zoom', 'بڑا کریں') }}
                            </span>
                        </button>
                        <figcaption class="text-xs text-slate-400 text-center mt-3">
                            {{ $t('Approved society map showing blocks, roads, and amenity plots.', 'منظور شدہ سوسائٹی نقشہ: بلاکس، سڑکیں اور سہولیات کے پلاٹس۔') }}
                        </figcaption>
                    </figure>
                @endif

                @if(count($gallery) > 0)
                    <div class="{{ $hasMaster ? 'lg:col-span-7' : 'lg:col-span-12' }} rounded-2xl bg-slate-800/40 border border-slate-700/50 p-4">
                        <div class="flex items-center justify-between mb-4 px-1">
                            <h3 class="text-sm font-semibold text-slate-100">{{ $t('Development Photos & Amenities', 'ترقیاتی تصاویر اور سہولیات') }}</h3>
                            <span class="text-xs text-slate-500">{{ count($gallery) }} {{ $t('Photos', 'تصاویر') }}</span>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach(array_slice($gallery, 0, $visibleThumbs, true) as $index => $photo)
                                @php $remaining = count($gallery) - $visibleThumbs; @endphp
                                <button type="button" @click="show({{ $galleryOffset + $index }})"
                                        aria-label="{{ $t('Open photo', 'تصویر کھولیں') }} {{ $index + 1 }}"
                                        class="relative group block rounded-xl overflow-hidden border border-slate-700/50 bg-slate-900 aspect-[4/3] cursor-zoom-in focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400/60">
                                    <img src="{{ asset('public/' . $photo) }}"
                                         alt="{{ $scheme->name }} – {{ $t('site photo', 'سائٹ کی تصویر') }} {{ $index + 1 }}"
                                         width="400" height="300" loading="lazy" decoding="async"
                                         class="w-full h-full object-cover group-hover:scale-[1.04] transition-transform duration-300">
                                    @if($loop->last && $remaining > 0)
                                        <span class="absolute inset-0 bg-slate-950/70 flex items-center justify-center text-slate-50 font-semibold text-lg">+{{ $remaining }}</span>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </section>
    @endif

    {{-- Available plots --}}
    <section class="mt-16" aria-labelledby="plots-heading">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 pb-5 border-b border-slate-800">
            <div>
                <h2 id="plots-heading" class="text-2xl font-bold text-slate-50">{{ $t('Available Plots & Pricing', 'دستیاب پلاٹس اور قیمتیں') }}</h2>
                <p class="text-sm text-slate-400 mt-1.5">{{ $t('Review plot sizes, categories and prices.', 'پلاٹ کے سائز، کیٹیگری اور قیمتیں دیکھیں۔') }}</p>
            </div>

            <form role="search" wire:submit.prevent class="w-full sm:w-64">
                <label for="plot-search" class="sr-only">{{ $t('Search plot number', 'پلاٹ نمبر تلاش کریں') }}</label>
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute top-1/2 -translate-y-1/2 ltr:left-4 rtl:right-4 text-slate-500 text-sm pointer-events-none" aria-hidden="true"></i>
                    <input id="plot-search" type="search" wire:model.live.debounce.300ms="searchPlot" autocomplete="off"
                           placeholder="{{ $t('Search plot #...', 'پلاٹ نمبر تلاش کریں...') }}"
                           class="w-full bg-slate-800/60 border border-slate-700/60 rounded-xl ltr:pl-11 rtl:pr-11 ltr:pr-10 rtl:pl-10 py-2.5 text-sm text-slate-100 placeholder:text-slate-500 focus:outline-none focus:border-amber-400/70 focus:ring-2 focus:ring-amber-400/15 transition">
                    <span wire:loading wire:target="searchPlot" class="absolute top-1/2 -translate-y-1/2 ltr:right-4 rtl:left-4 text-amber-300 text-xs">
                        <i class="fa-solid fa-circle-notch fa-spin" aria-hidden="true"></i>
                    </span>
                    @if($hasPlotSearch)
                        <button type="button" wire:click="$set('searchPlot', '')" wire:loading.remove wire:target="searchPlot"
                                aria-label="{{ $t('Clear search', 'تلاش صاف کریں') }}"
                                class="absolute top-1/2 -translate-y-1/2 ltr:right-3 rtl:left-3 w-6 h-6 rounded-full text-slate-400 hover:text-white hover:bg-slate-700 flex items-center justify-center cursor-pointer transition-colors">
                            <i class="fa-solid fa-xmark text-xs" aria-hidden="true"></i>
                        </button>
                    @endif
                </div>
            </form>
        </div>

        <div wire:loading.class="opacity-60" wire:target="searchPlot"
             class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mt-6 transition-opacity">
            @forelse($plots as $plot)
                @php
                    $statusKey = strtolower((string) ($plot->status ?? 'available'));
                    [$statusLabel, $statusClass] = $statusMap[$statusKey] ?? [ucfirst($statusKey), 'bg-slate-400/10 text-slate-300 ring-slate-300/20'];
                    $price = (float) ($plot->total_price ?? 0);
                    $short = $price > 0 ? $shortPrice($price) : null;
                @endphp

                <article wire:key="plot-{{ $plot->id ?? $plot->plot_number }}" class="az-card rounded-2xl bg-slate-800/40 border border-slate-700/50 p-5 flex flex-col gap-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-[11px] text-slate-500 font-medium">{{ $t('Plot No.', 'پلاٹ نمبر') }}</p>
                            <h3 class="text-2xl font-bold text-slate-50 mt-0.5">{{ $plot->plot_number }}</h3>
                        </div>
                        <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full ring-1 {{ $statusClass }}">{{ $statusLabel }}</span>
                    </div>

                    <dl class="grid grid-cols-2 gap-3 text-xs rounded-xl bg-slate-900/50 border border-slate-700/40 p-3">
                        <div>
                            <dt class="text-slate-500">{{ $t('Size / Area', 'سائز / رقبہ') }}</dt>
                            <dd class="mt-0.5 font-semibold text-slate-200">{{ $plot->size ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">{{ $t('Category', 'کیٹیگری') }}</dt>
                            <dd class="mt-0.5 font-semibold text-slate-200">{{ $plot->type ?: $t('Residential', 'رہائشی') }}</dd>
                        </div>
                    </dl>

                    <div class="mt-auto pt-4 border-t border-slate-700/50">
                        <p class="text-[11px] text-slate-500">{{ $t('Total Price', 'کل قیمت') }}</p>
                        @if($price > 0)
                            <p class="text-xl font-bold text-amber-300 mt-0.5" dir="ltr">PKR {{ number_format($price) }}</p>
                            @if($short)
                                <p class="text-xs text-slate-400 mt-0.5">≈ {{ $short }}</p>
                            @endif
                        @else
                            <p class="text-sm font-medium text-slate-400 mt-1">{{ $t('Price on request', 'قیمت رابطہ پر') }}</p>
                        @endif
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-2xl bg-slate-800/30 border border-slate-700/40 px-6 py-14 text-center">
                    <i class="fa-solid fa-map text-3xl mb-4 text-slate-600" aria-hidden="true"></i>
                    <p class="text-slate-200 font-medium">
                        @if($hasPlotSearch)
                            {{ $t("No plots found for “{$searchPlotTerm}”.", "“{$searchPlotTerm}” کے لیے کوئی پلاٹ نہیں ملا۔") }}
                        @else
                            {{ $t('No plots available in this housing scheme right now.', 'اس ہاؤسنگ سکیم میں فی الحال کوئی پلاٹ دستیاب نہیں۔') }}
                        @endif
                    </p>
                    @if($hasPlotSearch)
                        <button type="button" wire:click="$set('searchPlot', '')"
                                class="mt-5 inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-semibold text-sm px-5 py-2.5 rounded-xl cursor-pointer transition-colors">
                            <i class="fa-solid fa-rotate-left text-xs" aria-hidden="true"></i>{{ $t('Show all plots', 'تمام پلاٹس دکھائیں') }}
                        </button>
                    @else
                        <a href="{{ route('housing-schemes') }}" class="mt-5 inline-flex items-center gap-2 text-amber-300 hover:text-amber-200 text-sm font-semibold transition-colors">
                            <i class="fa-solid fa-arrow-left text-xs rtl:rotate-180" aria-hidden="true"></i>{{ __('Housing Schemes') }}
                        </a>
                    @endif
                </div>
            @endforelse
        </div>

        @if(method_exists($plots, 'hasPages') && $plots->hasPages())
            <nav class="mt-10" aria-label="Pagination">
                {{ $plots->links() }}
            </nav>
        @endif
    </section>

    {{-- Lightbox --}}
    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click.self="open = false"
         role="dialog" aria-modal="true" :aria-label="cur.title"
         class="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-slate-950/90 backdrop-blur-sm">

        <div x-trap.noscroll="open" class="relative w-full max-w-5xl max-h-[92vh] flex flex-col">
            <div class="flex items-center justify-between gap-3 pb-3 text-slate-300">
                <p class="text-sm font-medium truncate" x-text="cur.title"></p>
                <div class="flex items-center gap-2 shrink-0">
                    <span class="text-xs text-slate-500" x-show="images.length > 1" x-text="(i + 1) + ' / ' + images.length"></span>
                    <a :href="cur.src" target="_blank" rel="noopener"
                       class="text-xs text-amber-300 hover:text-amber-200 font-semibold bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-700">
                        <i class="fa-solid fa-arrow-up-right-from-square me-1" aria-hidden="true"></i>{{ $t('Open original', 'اصل تصویر کھولیں') }}
                    </a>
                    <button type="button" @click="open = false" aria-label="{{ $t('Close', 'بند کریں') }}"
                            class="w-8 h-8 rounded-lg bg-slate-800 border border-slate-700 text-slate-300 hover:text-white flex items-center justify-center cursor-pointer">
                        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <div class="relative flex-1 min-h-0 rounded-2xl overflow-hidden border border-slate-700/60 bg-slate-900 flex items-center justify-center">
                <img :src="cur.src" :alt="cur.title" class="w-full h-auto max-h-[78vh] object-contain">

                <button type="button" x-show="images.length > 1" @click="prev()" aria-label="{{ $t('Previous', 'پچھلی') }}"
                        class="absolute top-1/2 -translate-y-1/2 left-3 w-10 h-10 rounded-full bg-slate-950/70 hover:bg-slate-950 text-slate-100 flex items-center justify-center cursor-pointer transition-colors">
                    <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
                </button>
                <button type="button" x-show="images.length > 1" @click="next()" aria-label="{{ $t('Next', 'اگلی') }}"
                        class="absolute top-1/2 -translate-y-1/2 right-3 w-10 h-10 rounded-full bg-slate-950/70 hover:bg-slate-950 text-slate-100 flex items-center justify-center cursor-pointer transition-colors">
                    <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </div>
</div>