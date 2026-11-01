<div class="no-print space-y-2">
    @if(count($issues))
        <div class="rounded-2xl border border-amber-500/40 bg-amber-500/10 p-4">
            <p class="text-sm font-bold text-amber-200">{{ __('Complete these before printing:') }}</p>
            <ul class="mt-2 space-y-1 text-xs text-amber-100 list-disc ps-4">
                @foreach($issues as $issue)
                    <li>{{ $issue }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <p class="text-xs text-slate-400">{{ __('The agreement text stays in Urdu for use on stamp paper. Review it with a lawyer before signing.') }}</p>
</div>
