<div class="p-4 sm:p-6 lg:p-8 space-y-6 min-h-screen bg-slate-950 text-slate-100">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800/80 pb-5">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight">{{ __('Admin Activity Log') }}</h1>
            <p class="text-xs text-slate-400 mt-1">{{ __('Every money and account action taken in the admin panel, with who did it and when.') }}</p>
        </div>
    </div>

    <div class="bg-slate-900 rounded-2xl p-4 border border-slate-800 flex flex-col sm:flex-row gap-3">
        <div class="flex flex-wrap gap-2 text-[11px] font-bold">
            <button type="button" wire:click="$set('action', '')" class="px-3 py-1.5 rounded-lg border {{ $action === '' ? 'bg-amber-500 text-slate-950 border-amber-500' : 'bg-slate-950 text-slate-400 border-slate-800' }}">{{ __('All') }}</button>
            @foreach ($actions as $key => $label)
                <button type="button" wire:click="$set('action', '{{ $key }}')" class="px-3 py-1.5 rounded-lg border {{ $action === $key ? 'bg-amber-500 text-slate-950 border-amber-500' : 'bg-slate-950 text-slate-400 border-slate-800' }}">{{ $label }}</button>
            @endforeach
        </div>
        <input type="search" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search summary, admin name or target ID...') }}"
               class="flex-1 bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-amber-500">
    </div>

    <div class="bg-slate-900 rounded-2xl border border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-start text-sm text-slate-300">
                <thead class="bg-slate-950/60 text-xs uppercase text-slate-400 border-b border-slate-800 font-bold tracking-wider">
                    <tr>
                        <th class="px-5 py-3 text-start">{{ __('When') }}</th>
                        <th class="px-5 py-3 text-start">{{ __('Admin') }}</th>
                        <th class="px-5 py-3 text-start">{{ __('Action') }}</th>
                        <th class="px-5 py-3 text-start">{{ __('Details') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse ($logs as $log)
                        @php
                            $color = match (true) {
                                str_starts_with($log->action, 'wallet.') => 'text-emerald-400',
                                str_starts_with($log->action, 'topup.') => 'text-sky-400',
                                str_contains($log->action, 'deleted') || str_contains($log->action, 'rejected') || str_contains($log->action, 'blocked') => 'text-rose-400',
                                default => 'text-amber-400',
                            };
                        @endphp
                        <tr wire:key="log-{{ $log->id }}" class="hover:bg-slate-800/40 cursor-pointer" wire:click="toggle({{ $log->id }})">
                            <td class="px-5 py-3 text-xs text-slate-400 whitespace-nowrap">{{ $log->created_at->format('d M Y, h:i A') }}</td>
                            <td class="px-5 py-3 text-xs whitespace-nowrap">
                                <span class="text-slate-200 font-semibold">{{ $log->admin?->name ?? __('System') }}</span>
                                <span class="block text-[10px] text-slate-500">{{ $log->ip_address }}</span>
                            </td>
                            <td class="px-5 py-3 text-xs font-bold whitespace-nowrap {{ $color }}">{{ $log->action }}</td>
                            <td class="px-5 py-3 text-xs">
                                {{ $log->summary }}
                                @if ($log->target_type)
                                    <span class="text-slate-500">· {{ $log->target_type }} #{{ $log->target_id }}</span>
                                @endif
                                @if ($expandedId === $log->id && $log->details)
                                    <pre class="mt-2 text-[11px] bg-slate-950 border border-slate-800 rounded-lg p-3 text-slate-300 overflow-x-auto" dir="ltr">{{ json_encode($log->details, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-12 text-center text-slate-500 text-sm">{{ __('No admin activity recorded yet.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($logs->hasPages())
            <div class="p-4 border-t border-slate-800">{{ $logs->links() }}</div>
        @endif
    </div>
</div>
