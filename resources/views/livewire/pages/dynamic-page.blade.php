@php
    use Illuminate\Support\Str;

    $isUr = app()->getLocale() === 'ur';
    $t    = fn (string $en, string $ur) => $isUr ? $ur : $en;

    $slug    = (string) ($page->slug ?? '');
    $rawHtml = htmlspecialchars_decode((string) $page->content);

    // DOM-START
    // HTML ko safe karna, headings ko id dena, fehrist (TOC) aur (FAQ page ho to) sawal-jawab nikalna
    $bodyHtml = $rawHtml;
    $toc      = [];
    $faqItems = [];
    $isFaq    = (bool) preg_match('/faq|frequently/i', $slug);

    if (trim($rawHtml) !== '' && class_exists(\DOMDocument::class)) {
        $prevErrors = libxml_use_internal_errors(true);
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $loaded = $dom->loadHTML('<?xml encoding="UTF-8"><div>' . $rawHtml . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($prevErrors);

        $root = $loaded ? $dom->getElementsByTagName('div')->item(0) : null;

        if ($root) {
            $xp = new \DOMXPath($dom);

            // 1) Safai: script/object/embed/style hatao, on* attributes aur javascript: links hatao
            foreach (iterator_to_array($xp->query('//script|//object|//embed|//style')) as $node) {
                $node->parentNode?->removeChild($node);
            }
            foreach (iterator_to_array($xp->query('//@*[starts-with(name(), "on")]')) as $attr) {
                $attr->ownerElement?->removeAttributeNode($attr);
            }
            foreach (iterator_to_array($xp->query('//@*[name()="href" or name()="src" or name()="xlink:href"]')) as $attr) {
                if (preg_match('/^\s*(javascript|data|vbscript):/i', $attr->value)) {
                    $attr->ownerElement?->removeAttributeNode($attr);
                }
            }
            // Bahar jane wale links: noopener
            foreach (iterator_to_array($xp->query('//a[@target="_blank"]')) as $a) {
                $a->setAttribute('rel', 'noopener noreferrer');
            }

            // 2) Headings ko id do + TOC (sirf h2)
            $n = 0;
            foreach (iterator_to_array($xp->query('//h2|//h3')) as $h) {
                $n++;
                $text = trim(preg_replace('/\s+/u', ' ', $h->textContent));
                if ($text === '') continue;
                $id = $h->getAttribute('id') ?: 'section-' . $n;
                $h->setAttribute('id', $id);
                if ($h->nodeName === 'h2') {
                    $toc[] = ['id' => $id, 'text' => $text];
                }
            }

            // 3) FAQ: heading = sawal, agle heading tak ka text = jawab
            if ($isFaq) {
                foreach (iterator_to_array($xp->query('//h2|//h3')) as $h) {
                    $q = trim(preg_replace('/\s+/u', ' ', $h->textContent));
                    $answer = '';
                    for ($sib = $h->nextSibling; $sib; $sib = $sib->nextSibling) {
                        if ($sib->nodeType === XML_ELEMENT_NODE && in_array($sib->nodeName, ['h1', 'h2', 'h3', 'h4'], true)) break;
                        $answer .= ' ' . $sib->textContent;
                    }
                    $answer = trim(preg_replace('/\s+/u', ' ', $answer));
                    if ($q !== '' && $answer !== '') {
                        $faqItems[] = ['q' => $q, 'a' => Str::limit($answer, 1000, '…')];
                    }
                }
                if (count($faqItems) < 2) $faqItems = [];
            }

            $out = '';
            foreach ($root->childNodes as $child) {
                $out .= $dom->saveHTML($child);
            }
            $bodyHtml = $out;
        }
    }
    // DOM-END

    $plain        = trim(preg_replace('/\s+/u', ' ', strip_tags($rawHtml)));
    $contentIsRtl = (bool) preg_match('/\p{Arabic}/u', $plain);
    $contentDir   = $contentIsRtl ? 'rtl' : 'ltr';
    $contentLang  = $contentIsRtl ? 'ur' : 'en';

    $showToc   = count($toc) >= 3;
    $updatedAt = $page->updated_at ?? null;

    // ---- SEO ----
    $pageUrl        = route('dynamic.page', $page->slug);
    $seoTitle       = Str::limit($page->title, 60, '…') . ' | Assan Zameen';
    $seoDescription = Str::limit($plain !== '' ? $plain : $page->title, 158);

    view()->share([
        'title'       => $seoTitle,
        'description' => $seoDescription,
        'canonical'   => $pageUrl,
    ]);

    $webPage = array_filter([
        '@type'         => $faqItems ? 'FAQPage' : 'WebPage',
        '@id'           => $pageUrl . '#webpage',
        'url'           => $pageUrl,
        'name'          => $page->title,
        'description'   => $seoDescription,
        'inLanguage'    => $contentLang,
        'dateModified'  => optional($updatedAt)->toAtomString(),
        'isPartOf'      => ['@type' => 'WebSite', 'name' => 'Assan Zameen', 'url' => url('/')],
        'mainEntity'    => $faqItems ? array_map(fn ($i) => [
            '@type'          => 'Question',
            'name'           => $i['q'],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $i['a']],
        ], $faqItems) : null,
    ]);

    $jsonLd = [
        '@context' => 'https://schema.org',
        '@graph'   => [
            [
                '@type'           => 'BreadcrumbList',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home',      'item' => url('/')],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => $page->title, 'item' => $pageUrl],
                ],
            ],
            $webPage,
        ],
    ];
@endphp

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-24">

    @push('head')
        <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
        <style>
            .az-doc { color: #cbd5e1; font-size: 1.0125rem; overflow-wrap: anywhere; }
            .az-doc--rtl { line-height: 2.2; }
            .az-doc--ltr { line-height: 1.8; }
            .az-doc > * + * { margin-top: 1.1em; }
            .az-doc h2, .az-doc h3, .az-doc h4 { scroll-margin-top: 6rem; color: #f8fafc; font-weight: 700; line-height: 1.5; }
            .az-doc h2 { font-size: 1.4em; margin-top: 2.2em; padding-bottom: .5em; border-bottom: 1px solid #1e293b; }
            .az-doc h3 { font-size: 1.2em; margin-top: 1.7em; }
            .az-doc h4 { font-size: 1.05em; margin-top: 1.4em; }
            .az-doc strong, .az-doc b { color: #fcd34d; font-weight: 700; }
            .az-doc a { color: #fcd34d; text-decoration: underline; text-underline-offset: 4px; text-decoration-color: rgba(252, 211, 77, .4); }
            .az-doc a:hover { color: #fde68a; text-decoration-color: currentColor; }
            .az-doc ul { list-style: disc; padding-inline-start: 1.4em; }
            .az-doc ol { list-style: decimal; padding-inline-start: 1.4em; }
            .az-doc li + li { margin-top: .45em; }
            .az-doc li::marker { color: #f59e0b; }
            .az-doc img { max-width: 100%; height: auto; border-radius: .75rem; }
            .az-doc blockquote { background: rgba(30, 41, 59, .45); border-inline-start: 3px solid #f59e0b; border-radius: .5rem; padding: .8em 1.1em; color: #94a3b8; }
            .az-doc table { display: block; width: 100%; overflow-x: auto; border-collapse: collapse; font-size: .95em; }
            .az-doc th, .az-doc td { border: 1px solid #334155; padding: .55em .8em; text-align: start; }
            .az-doc th { background: #1e293b; color: #f8fafc; font-weight: 600; }
            .az-doc hr { border: 0; border-top: 1px solid #334155; margin-block: 2em; }

            @media print {
                body { background: #fff !important; color: #000 !important; }
                header, footer, .no-print { display: none !important; }
                .az-doc, .az-doc h2, .az-doc h3, .az-doc h4, .az-doc strong, .az-doc a { color: #000 !important; }
                .az-doc h2 { border-color: #ccc; }
            }
        </style>
    @endpush

    {{-- Breadcrumb --}}
    <nav aria-label="Breadcrumb" class="mb-6 no-print">
        <ol class="flex items-center gap-2 text-xs text-slate-500">
            <li><a href="{{ url('/') }}" class="hover:text-amber-300 transition-colors">{{ __('Home') }}</a></li>
            <li aria-hidden="true"><i class="fa-solid fa-chevron-right text-[9px] rtl:rotate-180"></i></li>
            <li class="text-slate-300 truncate max-w-[16rem]" aria-current="page">{{ $page->title }}</li>
        </ol>
    </nav>

    {{-- Header --}}
    <header class="pb-8 border-b border-slate-800">
        <p class="text-amber-300/90 text-xs font-semibold uppercase tracking-[0.18em] mb-3">
            <i class="fa-solid fa-file-contract me-1.5" aria-hidden="true"></i>{{ $t('Portal Document', 'پورٹل دستاویز') }}
        </p>
        <h1 class="text-3xl sm:text-4xl font-bold text-slate-50 tracking-tight leading-tight">{{ $page->title }}</h1>

        <div class="flex flex-wrap items-center justify-between gap-3 mt-5 text-xs text-slate-400">
            <p class="flex items-center gap-1.5">
                <i class="fa-regular fa-clock text-amber-300/80" aria-hidden="true"></i>
                {{ $t('Last Updated:', 'آخری اپ ڈیٹ:') }}
                @if($updatedAt)
                    <time datetime="{{ $updatedAt->toDateString() }}">{{ $updatedAt->format('M d, Y') }}</time>
                @else
                    {{ $t('Recently', 'حال ہی میں') }}
                @endif
            </p>

            <button type="button" onclick="window.print()"
                    class="no-print inline-flex items-center gap-2 text-slate-300 hover:text-amber-300 border border-slate-700 hover:border-amber-300/40 rounded-lg px-3 py-1.5 cursor-pointer transition-colors">
                <i class="fa-solid fa-print text-[11px]" aria-hidden="true"></i>{{ $t('Print', 'پرنٹ') }}
            </button>
        </div>
    </header>

    <div class="mt-10 {{ $showToc ? 'grid grid-cols-1 lg:grid-cols-4 gap-10' : '' }}">

        {{-- Table of contents --}}
        @if($showToc)
            <aside class="lg:col-span-1 no-print order-first lg:order-none" aria-label="{{ $t('On this page', 'اس صفحے پر') }}">
                <div class="lg:sticky lg:top-24 rounded-2xl bg-slate-800/40 border border-slate-700/50 p-5">
                    <p class="text-xs font-semibold text-slate-400 mb-3">{{ $t('On this page', 'اس صفحے پر') }}</p>
                    <ol class="space-y-2 text-sm">
                        @foreach($toc as $item)
                            <li>
                                <a href="#{{ $item['id'] }}" class="block text-slate-300 hover:text-amber-300 transition-colors leading-snug line-clamp-2">{{ $item['text'] }}</a>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </aside>
        @endif

        {{-- Content --}}
        <article class="{{ $showToc ? 'lg:col-span-3' : 'max-w-3xl mx-auto' }} min-w-0">
            <div class="az-doc az-doc--{{ $contentDir }}" dir="{{ $contentDir }}" lang="{{ $contentLang }}">
                {!! $bodyHtml !!}
            </div>
        </article>
    </div>

    {{-- Back link --}}
    <div class="mt-14 text-center no-print">
        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-400 hover:text-amber-300 transition-colors">
            <i class="fa-solid fa-arrow-left text-xs rtl:rotate-180" aria-hidden="true"></i>{{ $t('Back to Home', 'ہوم پر واپس') }}
        </a>
    </div>
</div>