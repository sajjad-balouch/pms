<div x-data="{ showImageModal: false, activeImage: '' }" class="p-6 bg-slate-900 min-h-screen text-slate-100">
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Header -->
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">Wallet Top-Up Requests</h1>
                <p class="text-slate-400 text-xs mt-1">Review, approve, or reject user wallet deposit requests.</p>
            </div>
        </div>

        <!-- Alerts -->
        @if (session()->has('success'))
            <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 p-4 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="bg-rose-500/10 border border-rose-500/30 text-rose-400 p-4 rounded-xl text-sm">
                {{ session('error') }}
            </div>
        @endif

        <!-- Filters & Search -->
        <div class="bg-slate-800/60 p-4 rounded-2xl border border-slate-700/60 flex flex-wrap gap-4 items-center justify-between">
            <div class="w-full sm:w-72">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by user, title or account..." class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-amber-400">
            </div>
            
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-400 font-bold">Status:</span>
                <select wire:model.live="statusFilter" class="bg-slate-900 border border-slate-700 text-xs text-white rounded-xl px-3 py-2 focus:outline-none">
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                    <option value="all">All Statuses</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-slate-800/40 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-800 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-700/50">
                        <tr>
                            <th class="p-4">User</th>
                            <th class="p-4">Amount</th>
                            <th class="p-4">Method & Sender Details</th>
                            <th class="p-4">Screenshot</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Date</th>
                            <th class="p-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse ($transactions as $tx)
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="p-4 font-semibold text-white">
                                    {{ $tx->user->name ?? 'N/A' }}
                                    <span class="block text-[10px] text-slate-400 font-normal">{{ $tx->user->email ?? '' }}</span>
                                </td>
                                <td class="p-4 font-bold text-amber-400 text-sm">
                                    PKR {{ number_format($tx->amount) }}
                                </td>
                                <td class="p-4 space-y-0.5">
                                    <span class="bg-slate-700 text-slate-200 px-2 py-0.5 rounded text-[10px] font-bold">{{ $tx->payment_method }}</span>
                                    <p class="text-white font-medium mt-1">{{ $tx->sender_account_title }}</p>
                                    <p class="text-slate-400 text-[10px]">{{ $tx->sender_account_number }}</p>
                                </td>
                                <td class="p-4">
                                    @if ($tx->screenshot)
                                        <button @click="activeImage = '{{ asset('uploads/' . $tx->screenshot) }}'; showImageModal = true" class="text-amber-400 underline text-[11px] hover:text-amber-300">
                                            View SS Proof
                                        </button>
                                    @else
                                        <span class="text-slate-500">No Image</span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    @if ($tx->status === 'pending')
                                        <span class="px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20 font-bold text-[10px]">Pending</span>
                                    @elseif ($tx->status === 'approved')
                                        <span class="px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-bold text-[10px]">Approved</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full bg-rose-500/10 text-rose-400 border border-rose-500/20 font-bold text-[10px]">Rejected</span>
                                    @endif
                                </td>
                                <td class="p-4 text-slate-400 text-[11px]">
                                    {{ $tx->created_at->format('d M Y, h:i A') }}
                                </td>
                                <td class="p-4 text-center">
                                    @if ($tx->status === 'pending')
                                        <div class="flex items-center justify-center gap-2">
                                            <button wire:click="approve({{ $tx->id }})" wire:confirm="Are you sure you want to approve this top-up?" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold px-3 py-1.5 rounded-lg text-[11px] transition">
                                                Approve
                                            </button>
                                            <button wire:click="openRejectModal({{ $tx->id }})" class="bg-rose-600 hover:bg-rose-500 text-white font-bold px-3 py-1.5 rounded-lg text-[11px] transition">
                                                Reject
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-slate-500 text-[11px] italic">Completed</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-slate-500">No requests found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-800">
                {{ $transactions->links() }}
            </div>
        </div>

    </div>

    <!-- Image Preview Modal -->
    <div x-show="showImageModal" x-cloak class="fixed inset-0 z-50 bg-black/80 flex items-center justify-center p-4">
        <div @click.away="showImageModal = false" class="bg-slate-900 border border-slate-700 p-4 rounded-2xl max-w-2xl w-full relative">
            <button @click="showImageModal = false" class="absolute top-3 right-3 text-slate-400 hover:text-white">✕</button>
            <img :src="activeImage" class="w-full h-auto max-h-[80vh] object-contain rounded-xl">
        </div>
    </div>

    <!-- Rejection Modal -->
    @if ($showRejectModal)
        <div class="fixed inset-0 z-50 bg-black/80 flex items-center justify-center p-4">
            <div class="bg-slate-900 border border-slate-700 p-6 rounded-2xl max-w-md w-full space-y-4">
                <h3 class="text-lg font-bold text-white">Reject Request</h3>
                <p class="text-xs text-slate-400">Specify the reason for rejecting this top-up request.</p>

                <textarea wire:model="rejectionReason" class="w-full bg-slate-800 border border-slate-700 rounded-xl p-3 text-xs text-white focus:outline-none focus:border-rose-400" rows="3" placeholder="Reason (e.g. Invalid screenshot or payment not received)"></textarea>
                @error('rejectionReason') <span class="text-rose-400 text-xs block">{{ $message }}</span> @enderror

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button wire:click="$set('showRejectModal', false)" class="bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold px-4 py-2 rounded-xl text-xs">Cancel</button>
                    <button wire:click="reject" class="bg-rose-600 hover:bg-rose-500 text-white font-bold px-4 py-2 rounded-xl text-xs">Confirm Reject</button>
                </div>
            </div>
        </div>
    @endif
</div>