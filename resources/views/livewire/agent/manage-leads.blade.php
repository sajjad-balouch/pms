<div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

    <!-- Flash Alert -->
    @if (session()->has('message'))
        <div class="flex items-center gap-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-3 rounded-xl shadow-sm">
            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="text-sm font-semibold">{{ session('message') }}</span>
        </div>
    @endif

    <!-- Header Section & Quick Stats -->
    <div class="bg-slate-800/90 p-6 rounded-2xl shadow-md border border-slate-700/80 space-y-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-2xl font-black text-white flex items-center gap-2">
                    <span>{{ __('Leads CRM & Pipeline') }}</span>
                    <span class="text-xs bg-indigo-500/10 border border-indigo-500/30 text-indigo-400 px-2.5 py-0.5 rounded-full font-bold">{{ __('Pro Suite') }}</span>
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">{{ __('Client categorization, follow-up scheduler, and drag-and-drop sales pipeline') }}</p>
            </div>

            <div class="flex items-center gap-2">
                <!-- Toggle View Mode Button -->
                <div class="bg-slate-900 border border-slate-700 p-1 rounded-xl flex items-center gap-1">
                    <button type="button" wire:click="$set('viewMode', 'kanban')"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 {{ $viewMode === 'kanban' ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:text-white' }}">
                        <i class="fa-solid fa-table-columns"></i>
                        <span>{{ __('Kanban Board') }}</span>
                    </button>
                    <button type="button" wire:click="$set('viewMode', 'table')"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 {{ $viewMode === 'table' ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:text-white' }}">
                        <i class="fa-solid fa-table-list"></i>
                        <span>{{ __('List Table') }}</span>
                    </button>
                </div>

                <button wire:click="create" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-xl font-bold text-xs shadow-md transition-all cursor-pointer">
                    <i class="fa-solid fa-user-plus"></i>
                    <span>{{ __('Add New Lead') }}</span>
                </button>
            </div>
        </div>

        <!-- Quick Activity Pill Counters -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2 border-t border-slate-700/60">
            <button type="button" wire:click="$set('followupFilter', 'overdue')"
                    class="p-3 rounded-xl border text-left transition {{ $followupFilter === 'overdue' ? 'bg-rose-500/20 border-rose-500 text-white' : 'bg-slate-900/60 border-slate-700/80 text-slate-300 hover:border-slate-600' }}">
                <span class="text-[10px] font-bold text-rose-400 uppercase block tracking-wider">🔴 {{ __('Overdue Calls') }}</span>
                <span class="text-lg font-black">{{ $overdueCount }}</span>
            </button>

            <button type="button" wire:click="$set('followupFilter', 'today')"
                    class="p-3 rounded-xl border text-left transition {{ $followupFilter === 'today' ? 'bg-amber-500/20 border-amber-500 text-white' : 'bg-slate-900/60 border-slate-700/80 text-slate-300 hover:border-slate-600' }}">
                <span class="text-[10px] font-bold text-amber-400 uppercase block tracking-wider">🟡 {{ __('Today\'s Follow-ups') }}</span>
                <span class="text-lg font-black">{{ $todayCount }}</span>
            </button>

            <button type="button" wire:click="$set('priorityFilter', 'hot')"
                    class="p-3 rounded-xl border text-left transition {{ $priorityFilter === 'hot' ? 'bg-red-500/20 border-red-500 text-white' : 'bg-slate-900/60 border-slate-700/80 text-slate-300 hover:border-slate-600' }}">
                <span class="text-[10px] font-bold text-red-400 uppercase block tracking-wider">🔥 {{ __('Hot Buyers') }}</span>
                <span class="text-lg font-black">{{ $hotCount }}</span>
            </button>

            <button type="button" wire:click="$set('followupFilter', ''); $set('priorityFilter', '')"
                    class="p-3 rounded-xl border text-left transition {{ empty($followupFilter) && empty($priorityFilter) ? 'bg-indigo-500/20 border-indigo-500 text-white' : 'bg-slate-900/60 border-slate-700/80 text-slate-300 hover:border-slate-600' }}">
                <span class="text-[10px] font-bold text-slate-400 uppercase block tracking-wider">📊 {{ __('Clear Filters') }}</span>
                <span class="text-xs font-bold text-slate-400 block mt-1">{{ __('Show All Deals') }}</span>
            </button>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 bg-slate-800/90 p-4 rounded-2xl shadow-md border border-slate-700/80">
        <div>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search name or phone...') }}" 
                   class="w-full px-3.5 py-2 bg-slate-900/80 border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500">
        </div>

        <div>
            <select wire:model.live="priorityFilter" class="w-full px-3 py-2 bg-slate-900/80 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500">
                <option value="">{{ __('All Temperatures (Hot/Warm/Cold)') }}</option>
                <option value="hot">🔥 {{ __('Hot Deals Only') }}</option>
                <option value="warm">⚡ {{ __('Warm Buyers') }}</option>
                <option value="cold">❄️ {{ __('Cold Leads') }}</option>
            </select>
        </div>

        <div>
            <select wire:model.live="statusFilter" class="w-full px-3 py-2 bg-slate-900/80 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500">
                <option value="">{{ __('All Pipeline Stages') }}</option>
                <option value="new">{{ __('New Inquiry') }}</option>
                <option value="contacted">{{ __('Contacted') }}</option>
                <option value="site_visit">{{ __('Site Visit') }}</option>
                <option value="negotiation">{{ __('Negotiation') }}</option>
                <option value="closed_won">{{ __('Closed Won (Deal)') }}</option>
                <option value="closed_lost">{{ __('Closed Lost') }}</option>
            </select>
        </div>
    </div>

    <!-- VIEW 1: KANBAN BOARD (DRAG AND DROP) -->
    @if($viewMode === 'kanban')
    <div class="overflow-x-auto pb-4">
        <div class="flex gap-4 min-w-[1250px]">
            @php
                $columns = [
                    'new' => ['title' => __('New Leads'), 'color' => 'border-blue-500', 'badge' => 'bg-blue-500/10 text-blue-400'],
                    'contacted' => ['title' => __('Contacted'), 'color' => 'border-purple-500', 'badge' => 'bg-purple-500/10 text-purple-400'],
                    'site_visit' => ['title' => __('Site Visit Scheduled'), 'color' => 'border-amber-500', 'badge' => 'bg-amber-500/10 text-amber-400'],
                    'negotiation' => ['title' => __('Under Offer / Meeting'), 'color' => 'border-indigo-500', 'badge' => 'bg-indigo-500/10 text-indigo-400'],
                    'closed_won' => ['title' => __('Won / Token Given'), 'color' => 'border-emerald-500', 'badge' => 'bg-emerald-500/10 text-emerald-400'],
                    'closed_lost' => ['title' => __('Lost / Dropped'), 'color' => 'border-rose-500', 'badge' => 'bg-rose-500/10 text-rose-400'],
                ];
            @endphp

            @foreach($columns as $colKey => $colData)
                <div class="w-72 shrink-0 bg-slate-900/90 rounded-2xl border border-slate-800 p-3 space-y-3 flex flex-col max-h-[75vh]"
                     ondragover="event.preventDefault(); this.classList.add('bg-slate-800/80');"
                     ondragleave="this.classList.remove('bg-slate-800/80');"
                     ondrop="
                        this.classList.remove('bg-slate-800/80');
                        let leadId = event.dataTransfer.getData('text/plain');
                        @this.updateLeadStatus(leadId, '{{ $colKey }}');
                     ">
                    
                    <!-- Column Header -->
                    <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                        <span class="text-xs font-bold text-white uppercase flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full {{ str_replace('text', 'bg', $colData['badge']) }}"></span>
                            {{ $colData['title'] }}
                        </span>
                        <span class="text-xs font-mono font-bold px-2 py-0.5 rounded-full {{ $colData['badge'] }}">
                            {{ count($kanbanColumns[$colKey]) }}
                        </span>
                    </div>

                    <!-- Cards Container -->
                    <div class="space-y-2.5 overflow-y-auto pr-1 flex-1">
                        @forelse($kanbanColumns[$colKey] as $kLead)
                            <div draggable="true"
                                 ondragstart="event.dataTransfer.setData('text/plain', '{{ $kLead->id }}');"
                                 class="bg-slate-950 p-3 rounded-xl border border-slate-800/80 hover:border-slate-600 transition shadow-sm space-y-2 cursor-grab active:cursor-grabbing">
                                
                                <!-- Card Header: Client Name & Priority Badge -->
                                <div class="flex items-start justify-between gap-1">
                                    <div>
                                        <div class="font-bold text-white text-xs">{{ $kLead->client_name }}</div>
                                        <div class="font-mono text-[11px] text-slate-400">{{ $kLead->client_phone }}</div>
                                    </div>

                                    <!-- Priority Temperature -->
                                    @if($kLead->priority === 'hot')
                                        <span class="text-[10px] bg-red-500/10 border border-red-500/30 text-red-400 font-bold px-1.5 py-0.5 rounded">🔥 {{ __('Hot') }}</span>
                                    @elseif($kLead->priority === 'warm')
                                        <span class="text-[10px] bg-amber-500/10 border border-amber-500/30 text-amber-400 font-bold px-1.5 py-0.5 rounded">⚡ {{ __('Warm') }}</span>
                                    @else
                                        <span class="text-[10px] bg-slate-800 text-slate-400 font-bold px-1.5 py-0.5 rounded">❄️ {{ __('Cold') }}</span>
                                    @endif
                                </div>

                                <!-- Property & Source Info -->
                                <div class="text-[11px] text-slate-400">
                                    <span class="text-amber-400 font-medium block truncate">
                                        {{ $kLead->town?->name ?? __('General Plot Inquiry') }}
                                        @if($kLead->plot) #{{ $kLead->plot->plot_number }} @endif
                                    </span>
                                    @if($kLead->budget)
                                        <span class="text-emerald-400 font-bold block">PKR {{ number_format($kLead->budget) }}</span>
                                    @endif
                                </div>

                                <!-- Follow-up Clock Badge -->
                                @if($kLead->next_followup_at)
                                    @php
                                        $fDate = \Carbon\Carbon::parse($kLead->next_followup_at);
                                        $isOverdue = $fDate->isPast();
                                        $isToday = $fDate->isToday();
                                    @endphp
                                    <div class="text-[10px] px-2 py-1 rounded-md font-bold flex items-center gap-1 {{ $isOverdue ? 'bg-rose-500/10 text-rose-400 border border-rose-500/20' : ($isToday ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'bg-slate-900 text-slate-400 border border-slate-800') }}">
                                        <i class="fa-regular fa-clock text-[9px]"></i>
                                        <span>{{ $fDate->format('d M, h:i A') }}</span>
                                    </div>
                                @endif

                                <!-- Bottom Actions -->
                                <div class="flex items-center justify-between pt-1 border-t border-slate-900 text-xs">
                                    <button type="button" wire:click="openWhatsAppModal({{ $kLead->id }})"
                                            class="text-emerald-400 hover:text-emerald-300 font-bold inline-flex items-center gap-1 text-[11px]">
                                        <i class="fa-brands fa-whatsapp text-xs"></i>
                                        <span>{{ __('WhatsApp') }}</span>
                                    </button>

                                    <div class="flex items-center gap-2">
                                        <button type="button" wire:click="edit({{ $kLead->id }})" class="text-indigo-400 hover:text-indigo-300 font-semibold text-[11px]">Edit</button>
                                        <button type="button" wire:click="delete({{ $kLead->id }})" onclick="confirm('Delete this lead?') || event.stopImmediatePropagation()" class="text-rose-400 hover:text-rose-300 text-[11px]">&times;</button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-slate-600 text-xs border border-dashed border-slate-800 rounded-xl">
                                {{ __('Drop deals here') }}
                            </div>
                        @endforelse
                    </div>

                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- VIEW 2: TABLE LIST VIEW -->
    @if($viewMode === 'table')
    <div class="bg-slate-800/90 rounded-2xl shadow-md border border-slate-700/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-900/60 text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-700/80">
                        <th class="px-5 py-4">{{ __('Client Name') }}</th>
                        <th class="px-5 py-4">{{ __('Phone / Channel') }}</th>
                        <th class="px-5 py-4">{{ __('Temperature') }}</th>
                        <th class="px-5 py-4">{{ __('Next Follow-Up') }}</th>
                        <th class="px-5 py-4">{{ __('Status') }}</th>
                        <th class="px-5 py-4 text-center">{{ __('Action') }}</th>
                        <th class="px-5 py-4 text-right">{{ __('Edit') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/60 text-sm">
                    @forelse ($leads as $lead)
                        <tr class="hover:bg-slate-700/30 transition-colors">
                            <td class="px-5 py-4 font-semibold text-white">
                                {{ $lead->client_name }}
                                <div class="text-[11px] text-amber-400 font-normal">
                                    {{ $lead->town?->name ?? __('General') }}
                                    @if($lead->plot) #{{ $lead->plot->plot_number }} @endif
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-medium text-slate-200 font-mono">{{ $lead->client_phone }}</div>
                                <span class="text-[10px] text-slate-400 uppercase font-bold">{{ $lead->source ?? 'portal' }}</span>
                            </td>
                            <td class="px-5 py-4">
                                @if($lead->priority === 'hot')
                                    <span class="text-[11px] bg-red-500/10 border border-red-500/30 text-red-400 font-bold px-2 py-0.5 rounded-full">🔥 {{ __('Hot') }}</span>
                                @elseif($lead->priority === 'warm')
                                    <span class="text-[11px] bg-amber-500/10 border border-amber-500/30 text-amber-400 font-bold px-2 py-0.5 rounded-full">⚡ {{ __('Warm') }}</span>
                                @else
                                    <span class="text-[11px] bg-slate-700 text-slate-300 font-bold px-2 py-0.5 rounded-full">❄️ {{ __('Cold') }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if($lead->next_followup_at)
                                    @php
                                        $fDate = \Carbon\Carbon::parse($lead->next_followup_at);
                                    @endphp
                                    <span class="text-xs font-bold font-mono {{ $fDate->isPast() ? 'text-rose-400' : 'text-slate-200' }}">
                                        {{ $fDate->format('d M, h:i A') }}
                                    </span>
                                @else
                                    <span class="text-xs text-slate-500">{{ __('Not Set') }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 capitalize">
                                <span class="text-xs font-bold px-2.5 py-1 rounded-md bg-slate-900 border border-slate-700 text-slate-200">
                                    {{ str_replace('_', ' ', $lead->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <button type="button" wire:click="openWhatsAppModal({{ $lead->id }})"
                                        class="px-2.5 py-1 bg-emerald-600/20 hover:bg-emerald-600 text-emerald-400 hover:text-white border border-emerald-500/30 rounded-lg text-xs font-bold transition">
                                    <i class="fa-brands fa-whatsapp text-sm"></i>
                                    <span>{{ __('Chat') }}</span>
                                </button>
                            </td>
                            <td class="px-5 py-4 text-right space-x-2">
                                <button wire:click="edit({{ $lead->id }})" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300">Edit</button>
                                <button wire:click="delete({{ $lead->id }})" onclick="confirm('Delete?') || event.stopImmediatePropagation()" class="text-xs font-semibold text-rose-400 hover:text-rose-300">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-500">{{ __('No leads found matching criteria.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-700/80">
            {{ $leads->links() }}
        </div>
    </div>
    @endif

    <!-- WhatsApp Outreach Modal -->
    @if($isWhatsAppModalOpen && $selectedLeadForWhatsApp)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md">
        <div class="bg-slate-900 rounded-3xl shadow-2xl w-full max-w-lg border border-slate-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-800 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                        <i class="fa-brands fa-whatsapp text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-white">{{ __('WhatsApp Outreach') }}</h3>
                        <p class="text-[11px] text-slate-400">{{ $selectedLeadForWhatsApp->client_name }} ({{ $selectedLeadForWhatsApp->client_phone }})</p>
                    </div>
                </div>
                <button wire:click="$set('isWhatsAppModalOpen', false)" class="text-slate-400 hover:text-white text-xl font-bold">&times;</button>
            </div>

            <form wire:submit.prevent="sendWhatsAppMessage" class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2">{{ __('Select Message Template') }}</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" wire:click="$set('whatsappMessageType', 'intro')"
                                class="p-2.5 rounded-xl border text-left text-xs font-bold transition flex items-center gap-2 {{ $whatsappMessageType === 'intro' ? 'border-emerald-500 bg-emerald-500/10 text-emerald-400' : 'border-slate-800 bg-slate-950 text-slate-400' }}">
                            <i class="fa-solid fa-handshake"></i>
                            <span>{{ __('Intro') }}</span>
                        </button>
                        
                        <button type="button" wire:click="$set('whatsappMessageType', 'site_visit')"
                                class="p-2.5 rounded-xl border text-left text-xs font-bold transition flex items-center gap-2 {{ $whatsappMessageType === 'site_visit' ? 'border-emerald-500 bg-emerald-500/10 text-emerald-400' : 'border-slate-800 bg-slate-950 text-slate-400' }}">
                            <i class="fa-solid fa-map-location-dot"></i>
                            <span>{{ __('Site Visit') }}</span>
                        </button>

                        <button type="button" wire:click="$set('whatsappMessageType', 'negotiation')"
                                class="p-2.5 rounded-xl border text-left text-xs font-bold transition flex items-center gap-2 {{ $whatsappMessageType === 'negotiation' ? 'border-emerald-500 bg-emerald-500/10 text-emerald-400' : 'border-slate-800 bg-slate-950 text-slate-400' }}">
                            <i class="fa-solid fa-comments-dollar"></i>
                            <span>{{ __('Meeting / Rate') }}</span>
                        </button>

                        <button type="button" wire:click="$set('whatsappMessageType', 'token')"
                                class="p-2.5 rounded-xl border text-left text-xs font-bold transition flex items-center gap-2 {{ $whatsappMessageType === 'token' ? 'border-emerald-500 bg-emerald-500/10 text-emerald-400' : 'border-slate-800 bg-slate-950 text-slate-400' }}">
                            <i class="fa-solid fa-lock"></i>
                            <span>{{ __('Token / Bayana') }}</span>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-1">{{ __('Custom Note / Time (Optional)') }}</label>
                    <input type="text" wire:model="customMessageNote" placeholder="{{ __('e.g. Please visit the office tomorrow at 11 AM') }}" 
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-emerald-500">
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-slate-800">
                    <button type="button" wire:click="$set('isWhatsAppModalOpen', false)" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-xl text-xs font-bold">{{ __('Cancel') }}</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-md shadow-emerald-600/20 cursor-pointer">
                        <i class="fa-brands fa-whatsapp text-sm"></i>
                        <span>{{ __('Launch WhatsApp') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Create / Edit Lead Modal -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div class="bg-slate-800 rounded-2xl shadow-2xl w-full max-w-lg border border-slate-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-700 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white">
                    {{ $lead_id ? __('Edit Client Details') : __('Add New Client / Lead') }}
                </h3>
                <button wire:click="closeModal" class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <form wire:submit.prevent="store" class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-300 mb-1">{{ __('Client Name') }} *</label>
                    <input type="text" wire:model="client_name" class="w-full rounded-xl border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-indigo-500">
                    @error('client_name') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-300 mb-1">{{ __('Phone') }} *</label>
                        <input type="text" wire:model="client_phone" class="w-full rounded-xl border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-indigo-500">
                        @error('client_phone') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-300 mb-1">{{ __('Email') }}</label>
                        <input type="email" wire:model="client_email" class="w-full rounded-xl border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <!-- Priority (Temperature) & Lead Source -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-300 mb-1">{{ __('Deal Temperature') }} *</label>
                        <select wire:model="priority" class="w-full rounded-xl border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-indigo-500">
                            <option value="hot">🔥 {{ __('Hot (Ready Buyer)') }}</option>
                            <option value="warm">⚡ {{ __('Warm (Considering)') }}</option>
                            <option value="cold">❄️ {{ __('Cold (General Query)') }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-300 mb-1">{{ __('Marketing Source') }}</label>
                        <select wire:model="source" class="w-full rounded-xl border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-indigo-500">
                            <option value="portal">{{ __('AssanZameen Portal') }}</option>
                            <option value="whatsapp">{{ __('WhatsApp Group / Chat') }}</option>
                            <option value="call">{{ __('Direct Phone Call') }}</option>
                            <option value="walk_in">{{ __('Office Walk-in') }}</option>
                            <option value="banner">{{ __('Panaflex Banner') }}</option>
                            <option value="social_media">{{ __('Social Media (FB / Insta)') }}</option>
                        </select>
                    </div>
                </div>

                <!-- Town & Plot Selection -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-300 mb-1">{{ __('Town / Scheme') }}</label>
                        <select wire:model.live="town_id" class="w-full rounded-xl border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-indigo-500">
                            <option value="">{{ __('Select Scheme') }}</option>
                            @foreach($towns as $town)
                                <option value="{{ $town->id }}">{{ $town->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-300 mb-1">{{ __('Plot Number') }}</label>
                        <select wire:model="plot_id" class="w-full rounded-xl border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-indigo-500">
                            <option value="">{{ __('Select Plot') }}</option>
                            @foreach($availablePlots as $plot)
                                <option value="{{ $plot->id }}">Plot # {{ $plot->plot_number }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Status & Budget -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-300 mb-1">{{ __('Pipeline Status') }}</label>
                        <select wire:model="status" class="w-full rounded-xl border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-indigo-500">
                            <option value="new">{{ __('New') }}</option>
                            <option value="contacted">{{ __('Contacted') }}</option>
                            <option value="site_visit">{{ __('Site Visit') }}</option>
                            <option value="negotiation">{{ __('Negotiation') }}</option>
                            <option value="closed_won">{{ __('Closed Won') }}</option>
                            <option value="closed_lost">{{ __('Closed Lost') }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-300 mb-1">{{ __('Budget (PKR)') }}</label>
                        <input type="number" wire:model="budget" placeholder="e.g. 5000000" class="w-full rounded-xl border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <!-- Next Follow-up Date/Time Scheduler -->
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-300 mb-1">{{ __('Schedule Next Follow-Up (Call / Visit)') }}</label>
                    <input type="datetime-local" wire:model="next_followup_at" class="w-full rounded-xl border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-indigo-500">
                    <span class="text-[11px] text-slate-400 mt-1 block">{{ __('An alert will show on your dashboard at this date and time.') }}</span>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-300 mb-1">{{ __('Client Notes / Requirement') }}</label>
                    <textarea wire:model="notes" rows="3" class="w-full rounded-xl border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-indigo-500" placeholder="{{ __('e.g. Wants a corner plot, budget up to 50 lac.') }}"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-700">
                    <button type="button" wire:click="closeModal" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-slate-300 rounded-xl font-semibold text-xs">{{ __('Cancel') }}</button>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-semibold text-xs shadow-md">{{ __('Save Lead') }}</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Universal Cross-Device WhatsApp Script -->
    <script>
        document.addEventListener('livewire:navigated', initLeadCRMScript);
        document.addEventListener('DOMContentLoaded', initLeadCRMScript);

        function initLeadCRMScript() {
            if (window._leadCRMScriptAttached) return;
            window._leadCRMScriptAttached = true;

            window.addEventListener('trigger-whatsapp', (event) => {
                const data = event.detail;
                const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

                if (isMobile) {
                    window.location.href = data.appUrl;
                } else {
                    window.location.href = data.appUrl;
                    setTimeout(function() {
                        if (!document.hidden) {
                            window.open(data.webUrl, '_blank');
                        }
                    }, 650);
                }
            });
        }
    </script>
</div>