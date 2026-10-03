<div class="bg-slate-900 min-h-screen text-slate-100 py-10 px-4 sm:px-6 lg:px-8 space-y-10">
    <div class="max-w-7xl mx-auto space-y-8">
        
        <h2 class="text-3xl font-extrabold text-white">Admin: Payment Methods & Deposits</h2>

        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-6 py-4 rounded-2xl">
                <i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="bg-rose-500/10 border border-rose-500/30 text-rose-400 px-6 py-4 rounded-2xl">
                <i class="fa-solid fa-circle-exclamation mr-2"></i>{{ session('error') }}
            </div>
        @endif

        <!-- Section 1: Add Payment Method -->
        <div class="bg-slate-800/80 border border-slate-700/80 rounded-3xl p-6 space-y-6">
            <h3 class="text-xl font-bold text-amber-400">Add New Payment Account</h3>
            
            <form wire:submit.prevent="createPaymentMethod" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="text-xs text-slate-400 block mb-1">Method Name (e.g. EasyPaisa)</label>
                    <input type="text" wire:model="method_name" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-xs text-white">
                </div>
                <div>
                    <label class="text-xs text-slate-400 block mb-1">Account Title</label>
                    <input type="text" wire:model="account_title" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-xs text-white">
                </div>
                <div>
                    <label class="text-xs text-slate-400 block mb-1">Account / IBAN / Mobile No.</label>
                    <input type="text" wire:model="account_number" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-xs text-white">
                </div>
                <div class="sm:col-span-3">
                    <label class="text-xs text-slate-400 block mb-1">Instructions (Optional)</label>
                    <input type="text" wire:model="instructions" placeholder="e.g. Send payment and share screenshot with account name." class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-xs text-white">
                </div>
                <div class="sm:col-span-3">
                    <button type="submit" class="bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold px-6 py-2.5 rounded-xl text-xs">
                        Save Payment Method
                    </button>
                </div>
            </form>

            <!-- Display Active Methods -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-slate-700/60">
                @foreach($methods as $m)
                    <div class="bg-slate-900/80 p-4 rounded-2xl border border-slate-700/60 space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-white text-sm">{{ $m->method_name }}</span>
                            <button type="button" wire:click="toggleMethodStatus({{ $m->id }})" class="text-[10px] px-2 py-1 rounded-full font-bold {{ $m->is_active ? 'bg-emerald-500/20 text-emerald-400' : 'bg-slate-700 text-slate-400' }}">
                                {{ $m->is_active ? 'Active' : 'Disabled' }}
                            </button>
                        </div>
                        <p class="text-xs text-amber-400 font-semibold">{{ $m->account_title }} - {{ $m->account_number }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Section 2: Manage Deposits -->
        <div class="bg-slate-800/80 border border-slate-700/80 rounded-3xl p-6 space-y-4">
            <h3 class="text-xl font-bold text-white">User Deposit Requests</h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-900 text-slate-400 uppercase font-bold text-[10px] border-b border-slate-700">
                        <tr>
                            <th class="p-3">User</th>
                            <th class="p-3">Method & Sender Details</th>
                            <th class="p-3">Amount</th>
                            <th class="p-3">Screenshot</th>
                            <th class="p-3">Status</th>
                            <th class="p-3">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @foreach($pendingDeposits as $dep)
                            <tr>
                                <td class="p-3 font-bold text-white">
                                    {{ $dep->user->name ?? 'User' }}
                                    <span class="block text-[10px] text-slate-400 font-normal">{{ $dep->user->email ?? '' }}</span>
                                </td>
                                <td class="p-3">
                                    <span class="text-amber-400 font-bold block">{{ $dep->payment_method }}</span>
                                    <span class="text-slate-300 text-[11px]">{{ $dep->sender_account_title }} ({{ $dep->sender_account_number }})</span>
                                </td>
                                <td class="p-3 font-extrabold text-amber-400 text-sm">PKR {{ number_format($dep->amount) }}</td>
                                <td class="p-3">
                                    @if($dep->screenshot)
                                        <a href="{{ asset($dep->screenshot) }}" target="_blank" class="bg-slate-900 border border-slate-700 px-3 py-1.5 rounded-lg text-amber-400 text-[11px] font-bold hover:bg-slate-950 inline-flex items-center gap-1">
                                            <i class="fa-solid fa-eye"></i> View SS
                                        </a>
                                    @endif
                                </td>
                                <td class="p-3">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $dep->status === 'approved' ? 'bg-emerald-500/20 text-emerald-400' : ($dep->status === 'pending' ? 'bg-amber-500/20 text-amber-400' : 'bg-rose-500/20 text-rose-400') }}">
                                        {{ $dep->status }}
                                    </span>
                                </td>
                                <td class="p-3">
                                    @if($dep->status === 'pending')
                                        <div class="flex items-center gap-2">
                                            <button type="button" wire:click="approveDeposit({{ $dep->id }})" class="bg-emerald-600 hover:bg-emerald-500 text-white px-3 py-1.5 rounded-lg text-xs font-bold">Approve</button>
                                            <button type="button" wire:click="rejectDeposit({{ $dep->id }})" class="bg-rose-600 hover:bg-rose-500 text-white px-3 py-1.5 rounded-lg text-xs font-bold">Reject</button>
                                        </div>
                                    @else
                                        <span class="text-slate-500 text-[11px]">Completed</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pt-4">
                {{ $pendingDeposits->links() }}
            </div>
        </div>

    </div>
</div>