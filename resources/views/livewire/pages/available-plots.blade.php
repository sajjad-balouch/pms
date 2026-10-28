@php
    $isUr = app()->getLocale() === 'ur';
    $t    = fn (string $en, string $ur) => $isUr ? $ur : $en;

    $region = 'Rahim Yar Khan'; // SEO title/description mein use hota hai

    // ---- Filters ----
    $q       = trim((string) ($search ?? ''));
    $typeV   = (string) ($type ?? 'all');
    $sizeV   = (string) ($size ?? 'all');

    $hasSearch = $q !== '';
    $hasType   = !in_array($typeV, ['', 'all'], true);
    $hasSize   = !in_array($sizeV, ['', 'all'], true);
    $filterCount = (int) $hasSearch + (int) $hasType + (int) $hasSize;

    $typeLabels = [
        'residential' => $t('Residential', 'رہائشی'),
        'commercial'  => $t('Commercial', 'کمرشل'),
    ];
    $sizeLabels = [
        '5'  => $t('5 Marla', '5 مرلہ'),
        '10' => $t('10 Marla', '10 مرلہ'),
        '1'  => $t('1 Kanal', '1 کنال'),
    ];
    $typeLabel = $hasType ? ($typeLabels[$typeV] ?? ucfirst($typeV)) : null;
    $sizeLabel = $hasSize ? ($sizeLabels[$sizeV] ?? $sizeV) : null;

    // ---- Results ----
    $isPaginated = method_exists($plots, 'total');
    $total       = $isPaginated ? $plots->total() : $plots->count();
    $currentPage = $isPaginated ? $plots->currentPage() : 1;

    // ---- SEO ----
    $headingEn = trim(($hasSize ? ($sizeLabels[$sizeV] ?? $sizeV) . ' ' : '') . ($hasType ? ($typeLabels[$typeV] ?? ucfirst($typeV)) . ' ' : '') . 'Plots');
    $seoHeading = $isUr
        ? trim(($sizeLabel ? $sizeLabel . ' ' : '') . ($typeLabel ? $typeLabel . ' ' : '') . 'پلاٹس')
        : $headingEn;

    $seoTitle = $hasSearch
        ? $t("Search results for “{$q}” – Available Plots | Assan Zameen", "“{$q}” کے نتائج – دستیاب پلاٹس | آسان زمین")
        : $t("{$seoHeading} for Sale in {$region} | Assan Zameen", "{$region} میں {$seoHeading} برائے فروخت | آسان زمین")
          . ($currentPage > 1 ? " – " . $t("Page {$currentPage}", "صفحہ {$currentPage}") : '');

    $seoDescription = $t(
        "Browse " . ($total > 0 ? "{$total} " : '') . strtolower($headingEn) . " for sale in {$region}. Compare plot size, block, category, price and town location on Assan Zameen.",
        "{$region} میں " . ($total > 0 ? "{$total} " : '') . "{$seoHeading} دیکھیں۔ پلاٹ کا سائز، بلاک، کیٹیگری، قیمت اور ٹاؤن کا مقام آسان زمین پر موازنہ کریں۔"
    );

    $canonicalParams = array_filter([
        'type' => $hasType ? $typeV : null,
        'size' => $hasSize ? $sizeV : null,
        'page' => $currentPage > 1 ? $currentPage : null,
    ]);
    $canonical = route('available-plots', $canonicalParams);

    view()->share([
        'title'       => $seoTitle,
        'description' => $seoDescription,
        'canonical'   => $canonical,
        'robots'      => $hasSearch ? 'noindex, follow' : 'index, follow, max-image-preview:large',
    ]);

    $jsonLd = [
        '@context' => 'https://schema.org',
        '@graph'   => [
            [
                '@type'           => 'BreadcrumbList',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home',            'item' => url('/')],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => 'Available Plots', 'item' => route('available-plots')],
                ],
            ],
            [
                '@type'       => 'CollectionPage',
                'name'        => "{$headingEn} for Sale in {$region}",
                'description' => $seoDescription,
                'url'         => $canonical,
            ],
        ],
    ];

    // ---- Helpers ----
    $shortPrice = function ($n) use ($isUr) {
        $n = (float) $n;
        if ($n >= 10000000) return rtrim(rtrim(number_format($n / 10000000, 2), '0'), '.') . ($isUr ? ' کروڑ' : ' Crore');
        if ($n >= 100000)   return rtrim(rtrim(number_format($n / 100000, 2), '0'), '.') . ($isUr ? ' لاکھ' : ' Lakh');
        return null;
    };
    $telHref  = fn ($p) => preg_replace('/[^0-9+]/', '', (string) $p);
    $waNumber = function ($phone) {
        $d = preg_replace('/\D+/', '', (string) $phone);
        if ($d === '') return null;
        if (str_starts_with($d, '00')) $d = substr($d, 2);
        if (str_starts_with($d, '0'))  $d = '92' . substr($d, 1);
        return $d;
    };
    $statusMap = [
        'available' => [$t('Available', 'دستیاب'),  'bg-emerald-400/10 text-emerald-300 ring-emerald-300/20'],
        'sold'      => [$t('Sold', 'فروخت شدہ'),     'bg-rose-400/10 text-rose-300 ring-rose-300/20'],
        'booked'    => [$t('Booked', 'بُک'),         'bg-amber-400/10 text-amber-300 ring-amber-300/20'],
        'reserved'  => [$t('Reserved', 'ریزرو'),     'bg-amber-400/10 text-amber-300 ring-amber-300/20'],
    ];

    $fieldClass = 'w-full bg-slate-800/60 border border-slate-700/60 rounded-xl px-3.5 py-2.5 text-sm text-slate-100 placeholder:text-slate-500 focus:outline-none focus:border-amber-400/70 focus:ring-2 focus:ring-amber-400/15 transition';
@endphp

<div x-data="{ filtersOpen: false }" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-24">

    @push('head')
        <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
    @endpush

    {{-- Breadcrumb --}}
    <nav aria-label="Breadcrumb" class="mb-6">
        <ol class="flex items-center gap-2 text-xs text-slate-500">
            <li><a href="{{ url('/') }}" class="hover:text-amber-300 transition-colors">{{ __('Home') }}</a></li>
            <li aria-hidden="true"><i class="fa-solid fa-chevron-right text-[9px] rtl:rotate-180"></i></li>
            <li class="text-slate-300" aria-current="page">{{ __('Available Plots') }}</li>
        </ol>
    </nav>

    {{-- Page header --}}
    <header class="pb-8 border-b border-slate-800">
        <p class="text-amber-300/90 text-xs font-semibold uppercase tracking-[0.18em] mb-3">
            <i class="fa-solid fa-vector-square me-1.5" aria-hidden="true"></i>{{ __('Inventory Marketplace') }}
        </p>
        <h1 class="text-3xl sm:text-4xl font-bold text-slate-50 tracking-tight">
            {{ ($hasType || $hasSize) && !$hasSearch ? $seoHeading : __('Available Plots') }}
        </h1>
        <p class="text-slate-400 text-sm sm:text-base leading-relaxed mt-3 max-w-2xl">
            {{ __('Filter and inspect verified residential & commercial property inventory.') }}
        </p>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 mt-8">

        {{-- Filters --}}
        <aside class="lg:col-span-1" aria-label="{{ __('Filter Inventory') }}">
            <button type="button" @click="filtersOpen = !filtersOpen" :aria-expanded="filtersOpen.toString()"
                    class="lg:hidden w-full flex items-center justify-between bg-slate-800/50 border border-slate-700/60 rounded-xl px-4 py-3 text-sm font-semibold text-slate-100 cursor-pointer">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-sliders text-amber-300" aria-hidden="true"></i>{{ __('Filter Inventory') }}
                    @if($filterCount > 0)
                        <span class="text-[10px] font-bold bg-amber-400 text-slate-950 rounded-full w-5 h-5 flex items-center justify-center">{{ $filterCount }}</span>
                    @endif
                </span>
                <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform" :class="filtersOpen ? 'rotate-180' : ''" aria-hidden="true"></i>
            </button>

            <div class="hidden lg:block lg:sticky lg:top-24 mt-3 lg:mt-0" :class="filtersOpen && '!block'">
                <form wire:submit.prevent class="rounded-2xl bg-slate-800/40 border border-slate-700/50 p-5 space-y-5">
                    <h2 class="hidden lg:flex text-sm font-semibold text-slate-50 items-center gap-2">
                        <i class="fa-solid fa-sliders text-amber-300" aria-hidden="true"></i>{{ __('Filter Inventory') }}
                    </h2>

                    <div>
                        <label for="plot-search" class="text-xs text-slate-400 block mb-1.5">{{ __('Search Plot No.') }}</label>
                        <div class="relative">
                            <input id="plot-search" type="search" wire:model.live.debounce.300ms="search" autocomplete="off"
                                   placeholder="{{ $t('e.g. B-104', 'مثلاً B-104') }}" class="{{ $fieldClass }}">
                            <span wire:loading wire:target="search" class="absolute top-1/2 -translate-y-1/2 ltr:right-3 rtl:left-3 text-amber-300 text-xs">
                                <i class="fa-solid fa-circle-notch fa-spin" aria-hidden="true"></i>
                            </span>
                        </div>
                    </div>

                    <div>
                        <label for="plot-type" class="text-xs text-slate-400 block mb-1.5">{{ __('Category Type') }}</label>
                        <select id="plot-type" wire:model.live="type" class="{{ $fieldClass }}">
                            <option value="all">{{ __('All Categories') }}</option>
                            <option value="residential">{{ __('Residential Plot') }}</option>
                            <option value="commercial">{{ __('Commercial Plot') }}</option>
                        </select>
                    </div>

                    <div>
                        <label for="plot-size" class="text-xs text-slate-400 block mb-1.5">{{ __('Plot Size') }}</label>
                        <select id="plot-size" wire:model.live="size" class="{{ $fieldClass }}">
                            <option value="all">{{ __('Any Size') }}</option>
                            <option value="5">{{ __('5 Marla') }}</option>
                            <option value="10">{{ __('10 Marla') }}</option>
                            <option value="1">{{ __('1 Kanal') }}</option>
                        </select>
                    </div>

                    @if($filterCount > 0)
                        <button type="button"
                                @click="$wire.set('search', ''); $wire.set('type', 'all'); $wire.set('size', 'all')"
                                class="w-full inline-flex items-center justify-center gap-2 text-xs font-semibold text-slate-300 hover:text-amber-300 border border-slate-700 hover:border-amber-300/40 rounded-xl py-2.5 cursor-pointer transition-colors">
                            <i class="fa-solid fa-rotate-left text-[11px]" aria-hidden="true"></i>{{ $t('Reset filters', 'فلٹرز ہٹائیں') }}
                        </button>
                    @endif
                </form>
            </div>
        </aside>

        {{-- Results --}}
        <section class="lg:col-span-3" aria-label="{{ __('Available Plots') }}">

            {{-- Summary + active chips --}}
            <div class="flex flex-wrap items-center gap-2 mb-5 min-h-[2rem]" aria-live="polite">
                <p class="text-xs text-slate-500 me-2">
                    @if($total > 0)
                        @if($isPaginated && $plots->firstItem())
                            {{ $t("Showing {$plots->firstItem()}–{$plots->lastItem()} of {$total} plots", "کل {$total} پلاٹس میں سے {$plots->firstItem()}–{$plots->lastItem()}") }}
                        @else
                            {{ $t("{$total} plots", "{$total} پلاٹس") }}
                        @endif
                    @endif
                </p>

                @if($hasSearch)
                    <button type="button" wire:click="$set('search', '')" class="inline-flex items-center gap-1.5 text-xs bg-amber-400/10 text-amber-200 border border-amber-300/20 rounded-full ps-3 pe-2 py-1 cursor-pointer hover:bg-amber-400/20 transition-colors">
                        “{{ $q }}” <i class="fa-solid fa-xmark text-[10px]" aria-hidden="true"></i><span class="sr-only">{{ $t('Remove', 'ہٹائیں') }}</span>
                    </button>
                @endif
                @if($hasType)
                    <button type="button" wire:click="$set('type', 'all')" class="inline-flex items-center gap-1.5 text-xs bg-amber-400/10 text-amber-200 border border-amber-300/20 rounded-full ps-3 pe-2 py-1 cursor-pointer hover:bg-amber-400/20 transition-colors">
                        {{ $typeLabel }} <i class="fa-solid fa-xmark text-[10px]" aria-hidden="true"></i><span class="sr-only">{{ $t('Remove', 'ہٹائیں') }}</span>
                    </button>
                @endif
                @if($hasSize)
                    <button type="button" wire:click="$set('size', 'all')" class="inline-flex items-center gap-1.5 text-xs bg-amber-400/10 text-amber-200 border border-amber-300/20 rounded-full ps-3 pe-2 py-1 cursor-pointer hover:bg-amber-400/20 transition-colors">
                        {{ $sizeLabel }} <i class="fa-solid fa-xmark text-[10px]" aria-hidden="true"></i><span class="sr-only">{{ $t('Remove', 'ہٹائیں') }}</span>
                    </button>
                @endif
            </div>

            {{-- Plots grid --}}
            <div wire:loading.class="opacity-60" wire:target="search,type,size"
                 class="grid grid-cols-1 md:grid-cols-2 gap-5 transition-opacity">

                @forelse($plots as $plot)
                    @php
                        $town = $plot->town;

                        $rawMap = trim((string) ($town->google_map_url ?? ''));
                        $townMapUrl = preg_match('#^https?://#i', $rawMap)
                            ? $rawMap
                            : 'https://www.google.com/maps/search/?api=1&query=' . urlencode(trim(($town->location ?? '') . ', ' . ($town->city ?? ''), ' ,'));

                        $statusKey = strtolower((string) ($plot->status ?? 'available'));
                        [$statusLabel, $statusClass] = $statusMap[$statusKey] ?? [ucfirst($statusKey), 'bg-slate-400/10 text-slate-300 ring-slate-300/20'];

                        $price = (float) ($plot->total_price ?? 0);
                        $short = $price > 0 ? $shortPrice($price) : null;

                        $ownerName  = $town->user->name ?? null;
                        $ownerPhone = $town->user->phone ?? null;
                        $ownerId    = $town->user_id ?? null;
                        $isUnlocked = (isset($unlockedPlotIds) && in_array($plot->id, $unlockedPlotIds))
                                   || ($ownerId && isset($unlockedUserIds) && in_array($ownerId, $unlockedUserIds));
                        $wa = $isUnlocked ? $waNumber($ownerPhone) : null;
                    @endphp

                    <article wire:key="plot-{{ $plot->id }}" class="az-card rounded-2xl bg-slate-800/40 border border-slate-700/50 p-5 flex flex-col gap-4">

                        {{-- Header --}}
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <span class="inline-block px-2.5 py-1 text-[11px] font-semibold rounded-full ring-1 {{ $statusClass }}">{{ $statusLabel }}</span>
                                <h2 class="text-xl font-bold text-slate-50 mt-2.5">{{ __('Plot') }} #{{ $plot->plot_number }}</h2>

                                <div class="text-xs mt-1.5 flex items-center gap-x-2 gap-y-1 flex-wrap">
                                    <i class="fa-solid fa-city text-amber-300/80" aria-hidden="true"></i>
                                    @if(!empty($town->id))
                                        <a href="{{ route('housing-schemes.show', $town->id) }}" class="text-amber-300 font-semibold hover:text-amber-200 hover:underline underline-offset-2">{{ $town->name ?? 'N/A' }}</a>
                                    @else
                                        <span class="text-amber-300 font-semibold">{{ $town->name ?? 'N/A' }}</span>
                                    @endif
                                    @if(!empty($town->location))
                                        <span class="text-slate-500">({{ $town->location }})</span>
                                    @endif
                                    <a href="{{ $townMapUrl }}" target="_blank" rel="noopener noreferrer"
                                       aria-label="{{ $t('View town on Google Maps', 'گوگل میپس پر ٹاؤن دیکھیں') }}"
                                       class="inline-flex items-center gap-1 text-[10px] bg-emerald-400/10 hover:bg-emerald-400/20 text-emerald-300 border border-emerald-300/20 px-2 py-0.5 rounded-md transition-colors">
                                        <i class="fa-solid fa-location-dot" aria-hidden="true"></i>{{ __('Map') }}
                                    </a>
                                </div>
                            </div>

                            <div class="text-end shrink-0">
                                @if($price > 0)
                                    <p class="text-lg font-bold text-amber-300" dir="ltr">PKR {{ number_format($price) }}</p>
                                    @if($short)<p class="text-[11px] text-slate-400 mt-0.5">≈ {{ $short }}</p>@endif
                                @else
                                    <p class="text-sm font-medium text-slate-400">{{ $t('Price on request', 'قیمت رابطہ پر') }}</p>
                                @endif
                            </div>
                        </div>

                        {{-- Meta --}}
                        <dl class="grid grid-cols-3 text-center text-xs rounded-xl bg-slate-900/50 border border-slate-700/40 py-3 divide-x divide-slate-700/40 rtl:divide-x-reverse">
                            <div class="px-2">
                                <dt class="text-[11px] text-slate-500">{{ $t('Size', 'سائز') }}</dt>
                                <dd class="mt-0.5 font-semibold text-slate-200">{{ $plot->size ?: '—' }}</dd>
                            </div>
                            <div class="px-2">
                                <dt class="text-[11px] text-slate-500">{{ $t('Type', 'قسم') }}</dt>
                                <dd class="mt-0.5 font-semibold text-slate-200">{{ $plot->type ? ($typeLabels[strtolower($plot->type)] ?? ucfirst($plot->type)) : '—' }}</dd>
                            </div>
                            <div class="px-2">
                                <dt class="text-[11px] text-slate-500">{{ $t('Block', 'بلاک') }}</dt>
                                <dd class="mt-0.5 font-semibold text-slate-200">{{ $plot->block_name ?: '—' }}</dd>
                            </div>
                        </dl>

                        {{-- Owner / contact --}}
                        <div class="mt-auto pt-4 border-t border-slate-700/50 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-8 h-8 rounded-full bg-slate-700/60 text-amber-300 flex items-center justify-center font-bold text-xs shrink-0">
                                    {{ mb_strtoupper(mb_substr($ownerName ?? 'A', 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[11px] text-slate-500">{{ $t('Town Owner', 'ٹاؤن اونر') }}</p>
                                    <p class="text-xs font-semibold text-slate-200 truncate">{{ $ownerName ?? $t('Administrator', 'ایڈمنسٹریٹر') }}</p>
                                </div>
                            </div>

                            @if($isUnlocked)
                                <div class="flex items-center gap-2 shrink-0">
                                    <a href="tel:{{ $telHref($ownerPhone) }}" dir="ltr"
                                       class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-300 bg-emerald-400/10 border border-emerald-300/20 hover:bg-emerald-400/20 rounded-xl px-3 py-2 transition-colors">
                                        <i class="fa-solid fa-phone text-[10px]" aria-hidden="true"></i>{{ $ownerPhone ?: 'N/A' }}
                                    </a>
                                    @if($wa)
                                        <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp"
                                           class="w-9 h-9 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 flex items-center justify-center transition-colors">
                                            <i class="fa-brands fa-whatsapp text-base" aria-hidden="true"></i>
                                        </a>
                                    @endif
                                </div>
                            @else
                                <button type="button" wire:click="inquirePlot({{ $plot->id }})" wire:loading.attr="disabled" wire:target="inquirePlot({{ $plot->id }})"
                                        aria-label="{{ $t('Inquire about plot', 'پلاٹ کے بارے میں معلومات') }} {{ $plot->plot_number }}"
                                        class="shrink-0 inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-400 disabled:opacity-60 text-slate-950 font-semibold text-xs px-4 py-2.5 rounded-xl cursor-pointer transition-colors">
                                    <i class="fa-solid fa-circle-notch fa-spin hidden" wire:loading.class.remove="hidden" wire:target="inquirePlot({{ $plot->id }})" aria-hidden="true"></i>
                                    {{ $t('Inquire', 'معلومات لیں') }}
                                </button>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-2xl bg-slate-800/30 border border-slate-700/40 px-6 py-16 text-center">
                        <i class="fa-solid fa-map text-4xl mb-4 text-slate-600" aria-hidden="true"></i>
                        <p class="text-slate-200 font-medium">{{ $t('No available plots found.', 'کوئی دستیاب پلاٹ نہیں ملا۔') }}</p>
                        @if($filterCount > 0)
                            <p class="text-sm text-slate-500 mt-1">{{ $t('Try removing a filter or searching a different plot number.', 'کوئی فلٹر ہٹائیں یا کوئی اور پلاٹ نمبر تلاش کریں۔') }}</p>
                            <button type="button" @click="$wire.set('search', ''); $wire.set('type', 'all'); $wire.set('size', 'all')"
                                    class="mt-5 inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-semibold text-sm px-5 py-2.5 rounded-xl cursor-pointer transition-colors">
                                <i class="fa-solid fa-rotate-left text-xs" aria-hidden="true"></i>{{ $t('Reset filters', 'فلٹرز ہٹائیں') }}
                            </button>
                        @endif
                    </div>
                @endforelse
            </div>

            @if($isPaginated && $plots->hasPages())
                <nav class="mt-10" aria-label="Pagination">
                    {{ $plots->links() }}
                </nav>
            @endif
        </section>
    </div>

    {{-- Browse by category: internal links (sirf bina filter ke pehle page par) --}}
    @if($filterCount === 0 && $currentPage === 1)
        <section class="mt-20" aria-labelledby="plots-browse-heading">
            <h2 id="plots-browse-heading" class="text-xl font-bold text-slate-50 mb-5">{{ $t('Browse plots by size and category', 'سائز اور کیٹیگری کے مطابق پلاٹس دیکھیں') }}</h2>
            <div class="flex flex-wrap gap-3">
                @foreach([
                    [route('available-plots', ['size' => '5']),            $t('5 Marla Plots', '5 مرلہ پلاٹس')],
                    [route('available-plots', ['size' => '10']),           $t('10 Marla Plots', '10 مرلہ پلاٹس')],
                    [route('available-plots', ['size' => '1']),            $t('1 Kanal Plots', '1 کنال پلاٹس')],
                    [route('available-plots', ['type' => 'commercial']),   $t('Commercial Plots', 'کمرشل پلاٹس')],
                    [route('available-plots', ['type' => 'residential']),  $t('Residential Plots', 'رہائشی پلاٹس')],
                    [route('housing-schemes'),                              $t('Housing Schemes', 'ہاؤسنگ سکیمیں')],
                ] as [$href, $label])
                    <a href="{{ $href }}" class="az-card inline-flex items-center gap-2 text-sm text-slate-200 bg-slate-800/40 border border-slate-700/50 rounded-xl px-4 py-2.5">
                        {{ $label }}<i class="fa-solid fa-chevron-right text-[10px] text-slate-500 rtl:rotate-180" aria-hidden="true"></i>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Unlock contact modal --}}
    @if($showModal && $selectedPlot)
        @php
            $balance = (float) (auth()->user()->wallet_balance ?? 0);
            $fee     = (float) ($unlockFee ?? 0);
        @endphp

        <div wire:click.self="closeModal" @keydown.escape.window="$wire.closeModal()"
             role="dialog" aria-modal="true" aria-labelledby="unlock-title"
             class="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-sm">

            <div x-trap.noscroll="true" class="relative w-full max-w-md rounded-2xl bg-slate-900 border border-slate-700/60 p-6 space-y-5 shadow-2xl">

                <button type="button" wire:click="closeModal" aria-label="{{ $t('Close', 'بند کریں') }}"
                        class="absolute top-4 ltr:right-4 rtl:left-4 w-8 h-8 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 flex items-center justify-center cursor-pointer transition-colors">
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                </button>

                <div class="text-center">
                    <div class="w-12 h-12 rounded-2xl bg-amber-400/10 text-amber-300 flex items-center justify-center mx-auto mb-3 text-xl">
                        <i class="fa-solid fa-lock-open" aria-hidden="true"></i>
                    </div>
                    <h2 id="unlock-title" class="text-lg font-bold text-slate-50">{{ $t('Unlock Contact Details', 'رابطے کی تفصیلات کھولیں') }}</h2>
                    <p class="text-xs text-slate-400 mt-1">{{ __('Plot') }} #{{ $selectedPlot->plot_number }} ({{ $selectedPlot->town->name ?? 'N/A' }})</p>
                </div>

                @if(session()->has('modal_error'))
                    <div role="alert" class="bg-rose-400/10 border border-rose-300/20 text-rose-300 p-3 rounded-xl text-xs text-center">{{ session('modal_error') }}</div>
                @endif
                @if(session()->has('modal_success'))
                    <div role="status" class="bg-emerald-400/10 border border-emerald-300/20 text-emerald-300 p-3 rounded-xl text-xs text-center">{{ session('modal_success') }}</div>
                @endif

                @if($unlockedContactData)
                    @php $modalWa = $waNumber($unlockedContactData['phone'] ?? ''); @endphp

                    <dl class="rounded-xl bg-slate-950/60 border border-slate-700/50 p-4 space-y-3 text-xs">
                        <div class="flex justify-between items-center gap-3 border-b border-slate-800 pb-2.5">
                            <dt class="text-slate-500">{{ $t('Owner / Agent Name', 'اونر / ایجنٹ کا نام') }}</dt>
                            <dd class="font-semibold text-slate-100">{{ $unlockedContactData['name'] }}</dd>
                        </div>
                        <div class="flex justify-between items-center gap-3 border-b border-slate-800 pb-2.5">
                            <dt class="text-slate-500">{{ $t('Phone Number', 'فون نمبر') }}</dt>
                            <dd class="font-semibold text-amber-300 font-mono text-sm" dir="ltr">{{ $unlockedContactData['phone'] }}</dd>
                        </div>
                        <div class="flex justify-between items-center gap-3">
                            <dt class="text-slate-500">{{ $t('Email', 'ای میل') }}</dt>
                            <dd class="font-medium text-slate-200 break-all">{{ $unlockedContactData['email'] }}</dd>
                        </div>
                    </dl>

                    <div class="grid {{ $modalWa ? 'grid-cols-2' : 'grid-cols-1' }} gap-3">
                        <a href="tel:{{ $telHref($unlockedContactData['phone'] ?? '') }}"
                           class="inline-flex items-center justify-center gap-2 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-semibold text-sm py-3 rounded-xl transition-colors">
                            <i class="fa-solid fa-phone text-xs" aria-hidden="true"></i>{{ $t('Call Now', 'کال کریں') }}
                        </a>
                        @if($modalWa)
                            <a href="https://wa.me/{{ $modalWa }}" target="_blank" rel="noopener noreferrer"
                               class="inline-flex items-center justify-center gap-2 border border-emerald-300/40 text-emerald-300 hover:bg-emerald-400/10 font-semibold text-sm py-3 rounded-xl transition-colors">
                                <i class="fa-brands fa-whatsapp text-base" aria-hidden="true"></i>WhatsApp
                            </a>
                        @endif
                    </div>
                @else
                    <dl class="rounded-xl bg-slate-950/60 border border-slate-700/50 p-4 space-y-3 text-xs">
                        <div class="flex justify-between gap-3">
                            <dt class="text-slate-400">{{ $t('Unlock Fee', 'ان لاک فیس') }}</dt>
                            <dd class="text-amber-300 font-bold" dir="ltr">PKR {{ number_format($fee) }}</dd>
                        </div>
                        <div class="flex justify-between gap-3">
                            <dt class="text-slate-400">{{ $t('Your Wallet Balance', 'آپ کے والٹ کا بیلنس') }}</dt>
                            <dd class="font-bold {{ $balance >= $fee ? 'text-slate-100' : 'text-rose-300' }}" dir="ltr">PKR {{ number_format($balance) }}</dd>
                        </div>
                    </dl>

                    @if($balance < $fee)
                        <p class="text-xs text-amber-300/90 text-center">{{ $t('Your balance is lower than the unlock fee.', 'آپ کا بیلنس ان لاک فیس سے کم ہے۔') }}</p>
                    @endif

                    <button type="button" wire:click="processUnlock" wire:loading.attr="disabled" wire:target="processUnlock"
                            class="w-full inline-flex items-center justify-center gap-2 bg-amber-500 hover:bg-amber-400 disabled:opacity-60 text-slate-950 font-semibold text-sm py-3 rounded-xl cursor-pointer transition-colors">
                        <i class="fa-solid fa-circle-notch fa-spin hidden" wire:loading.class.remove="hidden" wire:target="processUnlock" aria-hidden="true"></i>
                        {{ $t('Pay', 'ادا کریں') }} PKR {{ number_format($fee) }} {{ $t('& Unlock Contact', 'اور رابطہ کھولیں') }}
                    </button>
                @endif
            </div>
        </div>
    @endif
</div>