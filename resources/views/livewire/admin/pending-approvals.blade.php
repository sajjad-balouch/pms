<div class="bg-[#0a0f1d] rounded-2xl border border-slate-800/80 p-5 sm:p-6 shadow-xl text-slate-200">
    
    <!-- Header Section -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-800/80 mb-5">
        <div class="flex items-center gap-2.5">
            <span class="text-xl">⏳</span>
            <h3 class="text-base font-bold text-white tracking-wide">
                Pending Approvals <span class="text-xs font-normal text-slate-400 dir-rtl">(منتظر درخواستیں)</span>
            </h3>
        </div>
        
        <span class="px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-extrabold tracking-wider">
            {{ count($pendingUsers ?? []) }} New
        </span>
    </div>

    <!-- Approvals Table Section -->
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-300">
            <thead class="bg-slate-900/80 text-[11px] uppercase text-slate-400 border-b border-slate-800/80 font-black tracking-wider">
                <tr>
                    <th class="px-4 py-3.5">نام (Name)</th>
                    <th class="px-4 py-3.5">ای میل (Email)</th>
                    <th class="px-4 py-3.5">رول (Role)</th>
                    <th class="px-4 py-3.5">رجسٹریشن کی تاریخ (Date)</th>
                    <th class="px-4 py-3.5 text-right">ایکشن (Action)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                @forelse ($pendingUsers ?? [] as $user)
                    <tr class="hover:bg-slate-900/40 transition-colors">
                        <td class="px-4 py-3.5 font-bold text-white">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-slate-800 border border-slate-700 text-amber-400 font-bold flex items-center justify-center text-xs uppercase shrink-0">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <span>{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3.5 text-slate-300">{{ $user->email }}</td>
                        <td class="px-4 py-3.5">
                            <span class="uppercase text-[10px] font-black px-2.5 py-1 rounded-lg bg-slate-800 text-amber-400 border border-slate-700 tracking-wider">
                                {{ str_replace('_', ' ', $user->role) }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-slate-400 font-medium">
                            {{ $user->created_at ? $user->created_at->format('d M, Y') : 'N/A' }}
                        </td>
                        <td class="px-4 py-3.5 text-right">
                            <button wire:click="approve({{ $user->id }})" 
                                    class="px-3.5 py-1.5 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 hover:bg-emerald-600 hover:text-white text-xs font-bold transition-all duration-200 shadow-sm">
                                Approve Access
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-slate-400 font-medium">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <span class="text-2xl">✨</span>
                                <p class="text-sm text-slate-300 font-semibold dir-rtl">کوئی نئی منظور طلب درخواست موجود نہیں ہے۔</p>
                                <p class="text-xs text-slate-500">All registration requests have been processed.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>