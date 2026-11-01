<div class="py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto space-y-8">
        <div class="max-w-2xl">
            <h1 class="text-3xl sm:text-4xl font-black text-white">{{ __('Free Property Tools for Pakistan') }}</h1>
            <p class="mt-3 text-slate-400 leading-relaxed">{{ __('Free calculators and document makers for buying, selling, renting and inheriting property in Pakistan.') }}</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($tools as $tool)
                <a href="{{ route($tool['route']) }}" class="az-card block rounded-2xl border border-slate-800 bg-slate-900/60 p-5">
                    <i class="fa-solid {{ $tool['icon'] }} text-xl text-amber-400" aria-hidden="true"></i>
                    <h2 class="mt-3 font-bold text-white">{{ __($tool['name']) }}</h2>
                    <p class="mt-1.5 text-sm text-slate-400 leading-relaxed">{{ __($tool['desc']) }}</p>
                </a>
            @endforeach
        </div>

        <p class="text-xs text-slate-500 leading-relaxed">
            {{ __('This tool gives general guidance only and is not legal, tax or religious advice. Please confirm with a qualified professional or the relevant office.') }}
            <a href="{{ route('disclaimer') }}" class="underline hover:text-amber-300">{{ __('Read the disclaimer') }}</a>
        </p>
    </div>
</div>
