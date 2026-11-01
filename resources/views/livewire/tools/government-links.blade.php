<div class="py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto space-y-10">
        <div class="max-w-3xl">
            <h1 class="text-3xl sm:text-4xl font-black text-white">{{ __('Government Links Guide') }}</h1>
            <p class="mt-3 text-slate-400 leading-relaxed">{{ __('Official portals for fard, e-Stamp, DC valuation, registry and filer status, with the steps for each task.') }}</p>
            <p class="mt-3 rounded-xl border border-amber-500/30 bg-amber-500/10 px-4 py-3 text-sm text-amber-100 leading-relaxed">
                {{ __('Assan Zameen is not a government website. These links open official portals. Do not share your CNIC, password or payment details with anyone claiming to act for these offices.') }}
            </p>
        </div>

        @foreach($groups as $group => $services)
            <section aria-labelledby="group-{{ $loop->index }}">
                <h2 id="group-{{ $loop->index }}" class="text-lg font-bold text-white mb-4">{{ __($group) }}</h2>
                <div class="grid md:grid-cols-2 gap-4">
                    @foreach($services as $service)
                        <article class="flex flex-col rounded-2xl border border-slate-800 bg-slate-900/60 p-5">
                            <h3 class="font-bold text-white flex items-center gap-2.5">
                                <i class="fa-solid {{ $service['icon'] }} text-amber-400" aria-hidden="true"></i>
                                {{ __($service['title']) }}
                            </h3>
                            <p class="mt-2 text-sm text-slate-400 leading-relaxed">{{ __($service['desc']) }}</p>

                            @if(! empty($service['steps']))
                                <ol class="mt-3 space-y-2 text-sm text-slate-300 leading-relaxed list-decimal ps-5">
                                    @foreach($service['steps'] as $step)
                                        <li>{{ __($step) }}</li>
                                    @endforeach
                                </ol>
                            @endif

                            <div class="mt-auto pt-4 flex flex-wrap items-center gap-x-4 gap-y-2">
                                <a href="{{ $service['url'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-sm px-4 py-2 rounded-xl transition-colors">
                                    {{ __($service['link']) }}
                                    <i class="fa-solid fa-arrow-up-right-from-square text-xs" aria-hidden="true"></i>
                                </a>
                                @if(! empty($service['guide']))
                                    <a href="{{ $service['guide'] }}" target="_blank" rel="noopener noreferrer" class="text-sm text-amber-300 hover:text-amber-200 underline">{{ __('Official guide') }}</a>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endforeach

        <p class="text-xs text-slate-500">{{ __('Links last checked on :date. Government websites can change, and fees and steps are decided by the offices concerned.', ['date' => \App\Support\Tools\GovernmentServices::CHECKED_ON]) }}</p>

        @include('livewire.tools.partials.tool-footer', ['toolKey' => 'government'])
    </div>
</div>
