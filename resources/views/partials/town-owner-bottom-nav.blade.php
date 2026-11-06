@php
    // Mobile par neeche fixed menu (app ki tarah). Sirf woh hisse jin ki team member ko ijazat hai.
    use App\Support\Tenant;

    $items = array_values(array_filter([
        ['dashboard', 'town_owner.dashboard', 'fa-chart-pie', 'Dashboard'],
        ['recovery', 'town_owner.recovery', 'fa-hand-holding-dollar', 'Wusooli'],
        ['customers', 'town_owner.customers', 'fa-address-book', 'Customers'],
        ['towns', 'town_owner.towns', 'fa-city', 'Schemes'],
        ['expenses', 'town_owner.expenses', 'fa-receipt', 'Kharche'],
    ], fn ($i) => Tenant::can($i[0])));
@endphp
@if (count($items) > 1)
    <nav class="fixed inset-x-0 bottom-0 z-40 border-t border-slate-800 bg-slate-950/95 backdrop-blur md:hidden" style="padding-bottom: env(safe-area-inset-bottom)" aria-label="Town menu">
        <div class="mx-auto grid max-w-lg" style="grid-template-columns: repeat({{ count($items) }}, minmax(0, 1fr))">
            @foreach ($items as [$section, $route, $icon, $label])
                @php $active = request()->routeIs($route . '*'); @endphp
                <a href="{{ route($route) }}" wire:navigate
                   class="flex flex-col items-center gap-1 py-2.5 text-[11px] font-bold {{ $active ? 'text-amber-300' : 'text-slate-400 hover:text-white' }}">
                    <i class="fa-solid {{ $icon }} text-lg"></i>
                    <span>{{ $label }}</span>
                </a>
            @endforeach
        </div>
    </nav>
@endif
