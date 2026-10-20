@props([
    'slotId' => null, 
    'type' => 'adsense',
    'customImage' => null,
    'customUrl' => null,
    'class' => ''
])

<div class="w-full my-6 flex flex-col items-center justify-center overflow-hidden rounded-2xl bg-slate-900/40 border border-slate-800/80 p-3 text-center {{ $class }}">
    <span class="text-[9px] uppercase tracking-widest text-slate-500 mb-1 font-bold">Sponsored Advertisement</span>

    @if($type === 'adsense')
        <div class="min-h-[90px] w-full flex items-center justify-center text-xs text-slate-500 font-mono">
            <span class="p-4 border border-dashed border-slate-800 rounded-xl text-slate-500">
                [Google AdSense Unit - Slot: {{ $slotId ?: 'Default' }}]
            </span>
        </div>
    @elseif($type === 'custom' && $customImage)
        <a href="{{ $customUrl ?: '#' }}" target="_blank" rel="noopener noreferrer" class="block w-full transition-transform hover:scale-[1.01]">
            <img src="{{ asset('public/' . $customImage) }}" alt="Sponsored Ad" class="w-full max-h-32 object-cover rounded-xl shadow-md">
        </a>
    @endif
</div>