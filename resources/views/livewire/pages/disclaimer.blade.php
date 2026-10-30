@php
    $isUr = app()->getLocale() === 'ur';
    $t    = fn (string $en, string $ur) => $isUr ? $ur : $en;

    $sections = [
        [
            'title' => $t('Property listings', 'پراپرٹی لسٹنگز'),
            'text'  => $t(
                'Listings are posted by agents, town owners and other users. Assan Zameen does not own or sell these properties unless clearly stated. Prices, sizes, availability and photos can change, so confirm every detail with the seller before you make a decision.',
                'لسٹنگز ایجنٹس، ٹاؤن اونرز اور دوسرے صارفین لگاتے ہیں۔ جب تک واضح طور پر نہ لکھا ہو، یہ پراپرٹیز آسان زمین کی ملکیت نہیں اور نہ ہی آسان زمین انہیں فروخت کرتی ہے۔ قیمت، سائز، دستیابی اور تصاویر بدل سکتی ہیں، اس لیے کوئی بھی فیصلہ کرنے سے پہلے ہر بات بیچنے والے سے خود کنفرم کریں۔'
            ),
        ],
        [
            'title' => $t('Tools and calculators', 'ٹولز اور کیلکولیٹرز'),
            'text'  => $t(
                'Tax, registry, land, zakat and inheritance results are estimates for general guidance. Rates and rules change, so results may differ from the amount or decision of the relevant office.',
                'ٹیکس، رجسٹری، اراضی، زکوٰۃ اور وراثت کے نتائج عمومی رہنمائی کے لیے اندازے ہیں۔ ریٹس اور قوانین بدلتے رہتے ہیں، اس لیے نتیجہ متعلقہ دفتر کی رقم یا فیصلے سے مختلف ہو سکتا ہے۔'
            ),
        ],
        [
            'title' => $t('No legal or financial advice', 'قانونی یا مالی مشورہ نہیں'),
            'text'  => $t(
                'Articles, AI answers and document templates on this site are not legal, financial or religious advice. Please consult a qualified lawyer, registrar or scholar before any transaction or agreement.',
                'اس سائٹ کے مضامین، AI جوابات اور دستاویزات کے نمونے قانونی، مالی یا مذہبی مشورہ نہیں ہیں۔ کسی بھی لین دین یا معاہدے سے پہلے مستند وکیل، رجسٹرار یا عالم سے مشورہ کریں۔'
            ),
        ],
        [
            'title' => $t('Advertising and external links', 'اشتہارات اور بیرونی لنکس'),
            'text'  => $t(
                'This site shows third-party advertisements, including ads served by Google, and links to other websites. We do not control or endorse those ads or websites and are not responsible for their content.',
                'اس سائٹ پر تیسرے فریق کے اشتہارات دکھائے جاتے ہیں، جن میں Google کے اشتہارات بھی شامل ہیں، اور دوسری ویب سائٹس کے لنکس بھی ہوتے ہیں۔ ان اشتہارات یا ویب سائٹس پر ہمارا کنٹرول نہیں، نہ ہم ان کی توثیق کرتے ہیں، اور ان کے مواد کے ذمہ دار نہیں ہیں۔'
            ),
        ],
    ];
@endphp

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
    <h1 class="text-3xl font-extrabold text-white">{{ $t('Disclaimer', 'اعلانِ لاتعلقی') }}</h1>

    <div class="mt-8 space-y-8">
        @foreach($sections as $section)
            <section>
                <h2 class="text-lg font-bold text-white">{{ $section['title'] }}</h2>
                <p class="mt-2 text-sm sm:text-base leading-relaxed text-slate-300">{{ $section['text'] }}</p>
            </section>
        @endforeach

        <p class="text-sm text-slate-400">
            {{ $t('Questions about this page?', 'اس صفحے کے بارے میں سوال ہے؟') }}
            <a href="{{ route('contact') }}" class="text-amber-300 hover:text-amber-200 underline">{{ __('Contact Us') }}</a>
        </p>
    </div>
</div>
