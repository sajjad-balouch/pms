@php
    $catalog = \App\Support\Tools\ToolCatalog::all();
    $currentKey = $toolKey ?? null;
    $current = $currentKey ? $catalog[$currentKey] : null;
    $related = collect($catalog)->except($currentKey)->take(4);
@endphp

<div class="no-print space-y-6 pt-4">
    @if($current)
        <section class="bg-slate-900/70 rounded-2xl border border-slate-800 p-5 sm:p-6">
            <h2 class="text-base font-bold text-white mb-3">{{ __('How to use this tool') }}</h2>
            <ul class="space-y-2 text-sm text-slate-300 leading-relaxed list-disc ps-5">
                @foreach($current['guide'] as $line)
                    <li>{{ __($line) }}</li>
                @endforeach
            </ul>
        </section>
    @endif

    <p class="text-xs text-slate-500 leading-relaxed">
        {{ __('This tool gives general guidance only and is not legal, tax or religious advice. Please confirm with a qualified professional or the relevant office.') }}
        <a href="{{ route('disclaimer') }}" class="underline hover:text-amber-300">{{ __('Read the disclaimer') }}</a>
    </p>

    <nav aria-label="{{ __('Other tools') }}">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-bold text-white">{{ __('Other tools') }}</h2>
            <a href="{{ route('tools.index') }}" class="text-xs text-amber-300 hover:text-amber-200">{{ __('All tools') }}</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            @foreach($related as $tool)
                <a href="{{ route($tool['route']) }}" class="flex items-center gap-3 rounded-xl border border-slate-800 bg-slate-900/60 hover:border-slate-600 p-3 text-sm text-slate-200 transition-colors">
                    <i class="fa-solid {{ $tool['icon'] }} text-amber-400" aria-hidden="true"></i>
                    <span>{{ __($tool['name']) }}</span>
                </a>
            @endforeach
        </div>
    </nav>
</div>
