<div class="p-6 bg-slate-950 min-h-screen text-slate-100">
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Header & Stats Cards -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-800 pb-5">
            <div>
                <h1 class="text-2xl font-black text-white flex items-center gap-2">
                    <i class="fa-solid fa-chart-line text-amber-500"></i>
                    {{ __('Website Traffic & Page Analytics') }}
                </h1>
                <p class="text-xs text-slate-400 mt-1">{{ __('Daily user visits, page frequency, and geo-locations.') }}</p>
            </div>

            <!-- Date Selector Filter -->
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-400">{{ __('Select Date:') }}</span>
                <input type="date" wire:model.live="date_filter" class="bg-slate-900 border border-slate-700 text-xs text-amber-400 rounded-xl px-3 py-2 font-bold focus:outline-none focus:border-amber-500">
            </div>
        </div>

        <!-- Metric Badges -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-slate-900/80 border border-slate-800 p-4 rounded-2xl flex items-center justify-between">
                <div>
                    <span class="text-[11px] text-slate-400 font-bold uppercase">{{ __('Total Hits / Views') }}</span>
                    <h3 class="text-2xl font-black text-amber-400 mt-1">{{ number_format($totalViewsToday) }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-eye"></i>
                </div>
            </div>

            <div class="bg-slate-900/80 border border-slate-800 p-4 rounded-2xl flex items-center justify-between">
                <div>
                    <span class="text-[11px] text-slate-400 font-bold uppercase">{{ __('Unique Visitors') }}</span>
                    <h3 class="text-2xl font-black text-white mt-1">{{ number_format($uniqueVisitorsToday) }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="flex items-center gap-3 border-b border-slate-800">
            <button wire:click="$set('activeTab', 'logs')" class="pb-3 text-xs font-bold transition-all border-b-2 {{ $activeTab === 'logs' ? 'text-amber-400 border-amber-500' : 'text-slate-400 border-transparent hover:text-white' }}">
                <i class="fa-solid fa-list mr-1"></i> {{ __('Live User Logs') }}
            </button>
            <button wire:click="$set('activeTab', 'pages')" class="pb-3 text-xs font-bold transition-all border-b-2 {{ $activeTab === 'pages' ? 'text-amber-400 border-amber-500' : 'text-slate-400 border-transparent hover:text-white' }}">
                <i class="fa-solid fa-file-lines mr-1"></i> {{ __('Page Wise Summary') }}
            </button>
        </div>

        @if($activeTab === 'logs')
            <!-- Detailed Logs Table -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl overflow-hidden shadow-2xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-300">
                        <thead class="bg-slate-950/70 text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-800">
                            <tr>
                                <th class="p-4">{{ __('User / Visitor') }}</th>
                                <th class="p-4">{{ __('Page') }}</th>
                                <th class="p-4">{{ __('Location (City/Country)') }}</th>
                                <th class="p-4">{{ __('IP Address') }}</th>
                                <th class="p-4">{{ __('Device') }}</th>
                                <th class="p-4">{{ __('Time') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 font-medium">
                            @forelse($logs as $log)
                                <tr class="hover:bg-slate-800/40 transition-colors">
                                    <td class="p-4">
                                        @if($log->user)
                                            <div class="font-bold text-amber-400">{{ $log->user->name }}</div>
                                            <div class="text-[10px] text-slate-500">{{ $log->user->email }} ({{ ucfirst($log->user->role ?? 'User') }})</div>
                                        @else
                                            <span class="px-2 py-0.5 text-[10px] bg-slate-800 text-slate-400 rounded-md border border-slate-700">
                                                <i class="fa-solid fa-user-secret mr-1"></i> Guest Visitor
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4">
                                        <div class="font-bold text-slate-200">{{ $log->page_title }}</div>
                                        <a href="{{ $log->url }}" target="_blank" class="text-[10px] text-slate-500 hover:text-amber-400 truncate block max-w-xs">{{ $log->url }}</a>
                                    </td>
                                    <td class="p-4">
                                        <span class="flex items-center gap-1.5 text-slate-200">
                                            <i class="fa-solid fa-location-dot text-rose-500 text-[10px]"></i>
                                            {{ $log->city }}, {{ $log->country }}
                                        </span>
                                    </td>
                                    <td class="p-4 font-mono text-slate-400">{{ $log->ip_address }}</td>
                                    <td class="p-4">
                                        <span class="text-slate-300"><i class="fa-solid {{ $log->device === 'Mobile' ? 'fa-mobile-screen' : 'fa-laptop' }} mr-1"></i> {{ $log->device }}</span>
                                        <span class="text-[10px] text-slate-500 block">{{ $log->browser }}</span>
                                    </td>
                                    <td class="p-4 text-slate-400 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($log->viewed_at)->format('h:i:s A') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-slate-500">{{ __('No visits recorded for this date.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-slate-800">
                    {{ $logs->links() }}
                </div>
            </div>
        @else
            <!-- Page-wise aggregated table -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl overflow-hidden shadow-2xl">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950/70 text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-800">
                        <tr>
                            <th class="p-4">{{ __('Page Title') }}</th>
                            <th class="p-4">{{ __('URL') }}</th>
                            <th class="p-4 text-center">{{ __('Total Views') }}</th>
                            <th class="p-4 text-center">{{ __('Unique IPs') }}</th>
                            <th class="p-4 text-center">{{ __('Logged-in Users') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 font-medium">
                        @forelse($pageSummary as $page)
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <td class="p-4 font-bold text-slate-100">{{ $page->page_title }}</td>
                                <td class="p-4 text-slate-400 font-mono text-[11px] truncate max-w-sm">{{ $page->url }}</td>
                                <td class="p-4 text-center"><span class="px-2.5 py-1 bg-amber-500/10 text-amber-400 rounded-lg font-black border border-amber-500/20">{{ $page->total_views }}</span></td>
                                <td class="p-4 text-center font-bold text-slate-200">{{ $page->unique_visitors }}</td>
                                <td class="p-4 text-center text-slate-400">{{ $page->logged_in_users }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-slate-500">{{ __('No data available.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

    </div>
</div>