<div class="min-h-screen bg-slate-950 text-slate-100 p-4 sm:p-8 font-sans">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight flex items-center gap-3">
                <i class="fa-solid fa-city text-amber-500"></i> Town Schemes & Societies
            </h1>
            <p class="text-slate-400 text-sm mt-1">Manage society admins (Town Owners) and verify registered housing schemes</p>
        </div>
    </div>

    @if (session('status'))
        <div class="mb-6 p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 text-sm flex items-center justify-between">
            <span>{{ session('status') }}</span>
            <button onclick="this.parentElement.remove()"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    <!-- Table -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-950/60 border-b border-slate-800 text-slate-400 text-xs font-semibold uppercase">
                        <th class="py-4 px-6">Society Name</th>
                        <th class="py-4 px-6">Town Owner</th>
                        <th class="py-4 px-6">City</th>
                        <th class="py-4 px-6 text-center">Listed Plots/Properties</th>
                        <th class="py-4 px-6 text-center">Status</th>
                        <th class="py-4 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-sm">
                    @forelse($towns as $town)
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="py-4 px-6 font-bold text-white">
                                {{ $town->name }}
                                <span class="block text-xs font-normal text-slate-500">{{ $town->location ?? 'N/A' }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-medium text-amber-400">{{ $town->user->name ?? 'Admin Created' }}</div>
                                <div class="text-xs text-slate-400">{{ $town->user->phone ?? 'N/A' }}</div>
                            </td>
                            <td class="py-4 px-6 text-slate-300">{{ $town->city->name ?? 'N/A' }}</td>
                            <td class="py-4 px-6 text-center font-bold text-white">
                                <span class="px-2.5 py-1 rounded-lg bg-slate-800 text-amber-400 text-xs">
                                    {{ $town->properties_count ?? 0 }} Ads
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($town->is_active)
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Approved</span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">Banned/Disabled</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right">
                                <button wire:click="toggleBan({{ $town->id }})" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $town->is_active ? 'bg-rose-500/10 text-rose-400 hover:bg-rose-500/20' : 'bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20' }}">
                                    <i class="fa-solid {{ $town->is_active ? 'fa-ban' : 'fa-check' }} mr-1"></i>
                                    {{ $town->is_active ? 'Ban Scheme' : 'Approve' }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-12 text-center text-slate-500">No town schemes registered yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-800">{{ $towns->links() }}</div>
    </div>
</div>