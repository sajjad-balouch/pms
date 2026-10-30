@props([
    'slotId' => null, 
    'type' => 'adsense',
    'customImage' => null,
    'customUrl' => null,
    'class' => ''
])

<div class="w-full my-6 flex flex-col items-center justify-center overflow-hidden rounded-2xl bg-slate-900/40 border border-slate-800/80 p-3 text-center {{ $class }}">
    <span class="text-[9px] uppercase tracking-widest text-slate-500 mb-1 font-bold">{{ app()->getLocale() === 'ur' ? 'اشتہار' : 'Advertisement' }}</span>

    @if($type === 'adsense' && $slotId)
        <ins class="adsbygoogle block w-full" data-ad-client="{{ config('services.adsense.client') }}" data-ad-slot="{{ $slotId }}" data-ad-format="auto" data-full-width-responsive="true"></ins>
        <script>(window.adsbygoogle = window.adsbygoogle || []).push({});</script>
    @elseif($type === 'custom' && $customImage)
        <a href="{{ $customUrl ?: '#' }}" target="_blank" rel="noopener noreferrer" class="block w-full transition-transform hover:scale-[1.01]">
            <img src="{{ asset('public/' . $customImage) }}" alt="Sponsored Ad" class="w-full max-h-32 object-cover rounded-xl shadow-md">
        </a>
    @endif
</div>