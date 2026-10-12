<div class="p-6 bg-slate-950 min-h-screen text-slate-100">
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Top Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-800 pb-5">
            <div>
                <h1 class="text-2xl font-black text-white flex items-center gap-2">
                    <i class="fa-brands fa-whatsapp text-emerald-500"></i>
                    {{ __('WhatsApp Outreach & Marketing') }}
                </h1>
                <p class="text-xs text-slate-400 mt-1">
                    {{ __('Send Welcome messages, listing alerts, and custom updates to Normal Users, Agents, and Town Owners.') }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-bold rounded-full">
                    <i class="fa-solid fa-bolt mr-1"></i> Instant 1-Click Launch
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left Panel: Message Composer & Quick Templates -->
            <div class="lg:col-span-1 space-y-5">
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 space-y-4 shadow-xl">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-message text-amber-400"></i>
                        {{ __('Compose Message') }}
                    </h3>

                    <!-- Quick Templates -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 uppercase mb-2">{{ __('Quick Templates') }}</label>
                        <div class="flex flex-wrap gap-2">
                            <!-- Normal Users Templates -->
                            <button type="button" wire:click="setTemplate('user_welcome')" class="px-2.5 py-1 text-[11px] bg-slate-800 hover:bg-slate-700 text-emerald-400 font-bold rounded-lg border border-slate-700">
                                👋 User Welcome
                            </button>
                            <button type="button" wire:click="setTemplate('user_custom')" class="px-2.5 py-1 text-[11px] bg-slate-800 hover:bg-slate-700 text-cyan-400 font-bold rounded-lg border border-slate-700">
                                🔍 User Follow-up
                            </button>

                            <!-- Agents / Towns Templates -->
                            <button type="button" wire:click="setTemplate('agent_marketing')" class="px-2.5 py-1 text-[11px] bg-slate-800 hover:bg-slate-700 text-amber-400 font-bold rounded-lg border border-slate-700">
                                📢 Agent Marketing
                            </button>
                            <button type="button" wire:click="setTemplate('listing_reminder')" class="px-2.5 py-1 text-[11px] bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold rounded-lg border border-slate-700">
                                🏠 Add Listing
                            </button>
                        </div>
                    </div>

                    <!-- Message Body -->
                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <label class="text-[11px] font-bold text-slate-400 uppercase">{{ __('Custom Message') }}</label>
                            <span class="text-[10px] text-amber-400 font-mono font-bold">{name} = User Name</span>
                        </div>
                        <textarea wire:model.live.debounce.300ms="message" rows="7" 
                                  placeholder="Type your message here... Use {name} for user name."
                                  class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-3.5 text-xs text-white focus:outline-none focus:border-emerald-500 leading-relaxed"></textarea>
                    </div>

                    <div class="p-3 bg-slate-950/80 rounded-xl border border-slate-800/80 text-[11px] text-slate-400 space-y-1">
                        <p class="font-bold text-slate-300"><i class="fa-solid fa-circle-info text-amber-400 mr-1"></i> Device Support:</p>
                        <p>• <strong>Mobile:</strong> Direct WhatsApp App bina intermediate webpage ke khulegi.</p>
                        <p>• <strong>PC/Laptop:</strong> WhatsApp Desktop app ya WhatsApp Web tab mein pre-filled message khulega.</p>
                    </div>
                </div>
            </div>

            <!-- Right Panel: User List & Role Selector -->
            <div class="lg:col-span-2 space-y-4">
                
                <!-- Filter Tabs & Search -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 flex flex-col sm:flex-row items-center justify-between gap-3 shadow-xl">
                    <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                        <!-- Normal Users Tab -->
                        <button type="button" wire:click="$set('targetRole', 'user')" 
                                class="px-3.5 py-2 rounded-xl text-xs font-bold transition {{ $targetRole === 'user' ? 'bg-emerald-500 text-slate-950 font-extrabold shadow-md shadow-emerald-500/20' : 'bg-slate-800 text-slate-300 hover:text-white' }}">
                            <i class="fa-solid fa-user mr-1"></i> Users ({{ $totalNormalUsers }})
                        </button>
                        
                        <!-- Agents Tab -->
                        <button type="button" wire:click="$set('targetRole', 'agent')" 
                                class="px-3.5 py-2 rounded-xl text-xs font-bold transition {{ $targetRole === 'agent' ? 'bg-amber-500 text-slate-950 font-extrabold shadow-md shadow-amber-500/20' : 'bg-slate-800 text-slate-300 hover:text-white' }}">
                            <i class="fa-solid fa-user-tie mr-1"></i> Agents ({{ $totalAgents }})
                        </button>

                        <!-- Town Owners Tab -->
                        <button type="button" wire:click="$set('targetRole', 'town_owner')" 
                                class="px-3.5 py-2 rounded-xl text-xs font-bold transition {{ $targetRole === 'town_owner' ? 'bg-purple-500 text-white font-extrabold shadow-md shadow-purple-500/20' : 'bg-slate-800 text-slate-300 hover:text-white' }}">
                            <i class="fa-solid fa-city mr-1"></i> Town Owners ({{ $totalTownOwners }})
                        </button>

                        <!-- All Tab -->
                        <button type="button" wire:click="$set('targetRole', 'all')" 
                                class="px-3.5 py-2 rounded-xl text-xs font-bold transition {{ $targetRole === 'all' ? 'bg-blue-600 text-white font-extrabold shadow-md shadow-blue-600/20' : 'bg-slate-800 text-slate-300 hover:text-white' }}">
                            All
                        </button>
                    </div>

                    <div class="w-full sm:w-60">
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search name or phone..." 
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                    </div>
                </div>

                <!-- Users Table -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl overflow-hidden shadow-2xl">
                    <table class="w-full text-left text-xs text-slate-300">
                        <thead class="bg-slate-950/80 text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-800">
                            <tr>
                                <th class="p-4">{{ __('Name / Contact') }}</th>
                                <th class="p-4">{{ __('Account Type') }}</th>
                                <th class="p-4">{{ __('Phone') }}</th>
                                <th class="p-4 text-right">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 font-medium">
                            @forelse($users as $user)
                                <tr class="hover:bg-slate-800/40 transition-colors">
                                    <td class="p-4">
                                        <div class="font-bold text-white text-sm">{{ $user->name }}</div>
                                        <div class="text-[10px] text-slate-500">{{ $user->email }}</div>
                                    </td>
                                    <td class="p-4">
                                        @if(($user->role ?? 'user') === 'user')
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                                Normal User
                                            </span>
                                        @elseif($user->role === 'agent')
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                                Agent
                                            </span>
                                        @elseif($user->role === 'town_owner')
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-purple-500/10 text-purple-400 border border-purple-500/20">
                                                Town Owner
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-800 text-slate-400 border border-slate-700">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4 font-mono text-slate-300 font-bold">
                                        {{ $user->phone }}
                                    </td>
                                    <td class="p-4 text-right">
                                        <button type="button"
                                                wire:click="sendWhatsAppMessage('{{ $user->phone }}', '{{ addslashes($user->name) }}')"
                                                wire:loading.attr="disabled"
                                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition shadow-md shadow-emerald-600/20 hover:scale-105 transform cursor-pointer disabled:opacity-50">
                                            <i class="fa-brands fa-whatsapp text-sm"></i>
                                            <span>Send WhatsApp</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-8 text-center text-slate-500">
                                        {{ __('No users found with valid phone numbers in this category.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="p-4 border-t border-slate-800">
                        {{ $users->links() }}
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Livewire Dispatch Listener -->
    <script>
        document.addEventListener('livewire:navigated', initWhatsAppListener);
        document.addEventListener('DOMContentLoaded', initWhatsAppListener);

        function initWhatsAppListener() {
            if (window._whatsappListenerAttached) return;
            window._whatsappListenerAttached = true;

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

            window.addEventListener('notify-error', (event) => {
                alert(event.detail.message);
            });
        }
    </script>
</div>