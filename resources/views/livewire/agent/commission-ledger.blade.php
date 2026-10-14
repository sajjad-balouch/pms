<div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

    <!-- Flash Alert -->
    @if (session()->has('message'))
        <div class="flex items-center gap-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-3 rounded-xl shadow-sm">
            <i class="fa-solid fa-circle-check text-lg"></i>
            <span class="text-xs font-bold">{{ session('message') }}</span>
        </div>
    @endif

    <!-- Header Section -->
    <div class="bg-slate-800/90 p-6 rounded-2xl shadow-md border border-slate-700/80 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-black text-white flex items-center gap-2">
                <i class="fa-solid fa-book-bookmark text-amber-400"></i>
                <span>Munshi Khata & Commission Ledger</span>
            </h2>
            <p class="text-xs text-slate-400 mt-1">Apna kul commission hisaab, client udhaar aur partner split ek jagah track karein</p>
        </div>

        <button wire:click="createDeal" class="px-5 py-2.5 bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 text-slate-950 font-black text-xs rounded-xl transition flex items-center gap-2 shadow-lg shadow-amber-500/20 cursor-pointer">
            <i class="fa-solid fa-plus-circle"></i>
            <span>Nayi Deal Darj Karein</span>
        </button>
    </div>

    <!-- Quick Stats Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 shadow-md space-y-1">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Kul Commission (Deals)</span>
            <div class="text-2xl font-black text-white font-mono">PKR {{ number_format($totalEarned) }}</div>
            <span class="text-[10px] text-slate-500">Tamam band hone wali deals</span>
        </div>

        <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 shadow-md space-y-1">
            <span class="text-[11px] font-bold text-emerald-400 uppercase tracking-wider block">Wasool Shuda (Cash In)</span>
            <div class="text-2xl font-black text-emerald-400 font-mono">PKR {{ number_format($totalReceived) }}</div>
            <span class="text-[10px] text-slate-500">Office cash & bank received</span>
        </div>

        <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 shadow-md space-y-1">
            <span class="text-[11px] font-bold text-rose-400 uppercase tracking-wider block">Baqaya Udhaar (Receivable)</span>
            <div class="text-2xl font-black text-rose-400 font-mono">PKR {{ number_format($totalPending) }}</div>
            <span class="text-[10px] text-rose-500/80">Parties ki taraf baqaya</span>
        </div>

        <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 shadow-md space-y-1">
            <span class="text-[11px] font-bold text-indigo-400 uppercase tracking-wider block">Sub-Agent / Team Share</span>
            <div class="text-2xl font-black text-indigo-400 font-mono">PKR {{ number_format($totalPartnerPaid) }}</div>
            <span class="text-[10px] text-slate-500">Boys aur partners ka hissa</span>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-slate-800/90 p-4 rounded-2xl border border-slate-700/80 shadow-md">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Deal ka naam ya client ka phone search karein..." 
               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-amber-500">
        
        <select wire:model.live="statusFilter" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
            <option value="">Tamam Adayegi Status (All Statuses)</option>
            <option value="paid">Mukammal Wasool Shuda (Paid)</option>
            <option value="partial">Juzwi Wasooli (Partial Paid)</option>
            <option value="pending">Baqaya Udhaar (Full Pending)</option>
        </select>
    </div>

    <!-- Ledger Table -->
    <div class="bg-slate-800/90 rounded-2xl shadow-md border border-slate-700/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-900/60 text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-700/80">
                        <th class="px-5 py-4">Deal Title & Date</th>
                        <th class="px-5 py-4">Client Contact</th>
                        <th class="px-5 py-4">Total Comm.</th>
                        <th class="px-5 py-4">Received</th>
                        <th class="px-5 py-4">Pending Udhaar</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4 text-center">Tagaza / Reminder</th>
                        <th class="px-5 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/60 text-sm">
                    @forelse($deals as $deal)
                        <tr class="hover:bg-slate-700/30 transition-colors">
                            <td class="px-5 py-4">
                                <div class="font-bold text-white">{{ $deal->deal_title }}</div>
                                <div class="text-[11px] text-slate-400">{{ \Carbon\Carbon::parse($deal->deal_date)->format('d M, Y') }}</div>
                                @if($deal->partner_name)
                                    <div class="text-[10px] text-indigo-400 font-semibold mt-0.5">Team Boy: {{ $deal->partner_name }} (PKR {{ number_format($deal->partner_share) }})</div>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-medium text-slate-200">{{ $deal->client_name }}</div>
                                <div class="text-xs font-mono text-slate-400">{{ $deal->client_phone }}</div>
                            </td>
                            <td class="px-5 py-4 font-bold text-slate-200 font-mono">
                                PKR {{ number_format($deal->total_commission) }}
                            </td>
                            <td class="px-5 py-4 font-bold text-emerald-400 font-mono">
                                PKR {{ number_format($deal->received_amount) }}
                            </td>
                            <td class="px-5 py-4 font-black font-mono {{ $deal->pending_amount > 0 ? 'text-rose-400' : 'text-slate-500' }}">
                                PKR {{ number_format($deal->pending_amount) }}
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full capitalize
                                    {{ $deal->status === 'paid' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : '' }}
                                    {{ $deal->status === 'partial' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : '' }}
                                    {{ $deal->status === 'pending' ? 'bg-rose-500/10 text-rose-400 border border-rose-500/20' : '' }}">
                                    {{ $deal->status }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if($deal->pending_amount > 0)
                                    <button type="button" wire:click="sendPaymentReminder({{ $deal->id }})" 
                                            class="inline-flex items-center gap-1.5 px-3 py-1 bg-rose-600/20 hover:bg-rose-600 text-rose-400 hover:text-white border border-rose-500/30 rounded-lg text-xs font-bold transition cursor-pointer">
                                        <i class="fa-brands fa-whatsapp text-sm"></i>
                                        <span>WhatsApp Tagaza</span>
                                    </button>
                                @else
                                    <span class="text-xs text-slate-500 font-semibold">Cleared ✓</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right space-x-2">
                                <button wire:click="editDeal({{ $deal->id }})" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 cursor-pointer">Edit</button>
                                <button wire:click="deleteDeal({{ $deal->id }})" onclick="confirm('Is deal record ko delete karna chahte hain?') || event.stopImmediatePropagation()" class="text-xs font-semibold text-rose-400 hover:text-rose-300 cursor-pointer">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-slate-500">Khate mein abhi koi deal darj nahi hai.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-700/80">
            {{ $deals->links() }}
        </div>
    </div>

    <!-- Create / Edit Deal Modal -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md">
        <div class="bg-slate-900 rounded-3xl shadow-2xl w-full max-w-xl border border-slate-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-800 flex justify-between items-center">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-file-invoice-dollar text-amber-400"></i>
                    <span>{{ $deal_id ? 'Deal Khata Update Karein' : 'Nayi Deal Khate Mein Likhein' }}</span>
                </h3>
                <button wire:click="$set('isModalOpen', false)" class="text-slate-400 hover:text-white text-lg font-bold">&times;</button>
            </div>

            <form wire:submit="saveDeal" class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Deal Title *</label>
                    <input type="text" wire:model="deal_title" placeholder="Maslan: 10 Marla House Deal Phase 4" 
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                    @error('deal_title') <span class="text-rose-400 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Client Name *</label>
                        <input type="text" wire:model="client_name" placeholder="Client / Party Name" 
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Client Phone (WhatsApp) *</label>
                        <input type="text" wire:model="client_phone" placeholder="03001234567" 
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Deal Date *</label>
                        <input type="date" wire:model="deal_date" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Kul Deal Value (PKR) *</label>
                        <input type="number" wire:model="deal_value" placeholder="8500000" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 p-3 bg-slate-950/80 rounded-2xl border border-slate-800/80">
                    <div>
                        <label class="block text-xs font-bold text-amber-400 uppercase mb-1">Total Commission *</label>
                        <input type="number" wire:model="total_commission" placeholder="85000" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-amber-500 font-mono font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-emerald-400 uppercase mb-1">Wasool Shuda Raqam *</label>
                        <input type="number" wire:model="received_amount" placeholder="50000" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-emerald-400 focus:outline-none focus:border-emerald-500 font-mono font-bold">
                    </div>
                </div>

                <!-- Sub-Agent / Partner Sharing -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Field Boy / Partner Name</label>
                        <input type="text" wire:model="partner_name" placeholder="Maslan: Ali Raza" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Partner Share Amount (PKR)</label>
                        <input type="number" wire:model="partner_share" placeholder="15000" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-indigo-400 focus:outline-none focus:border-amber-500 font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Notes / Tafseel</label>
                    <textarea wire:model="notes" rows="2" placeholder="Maslan: Baqi 35,000 registry wale din milenge..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-amber-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" wire:click="$set('isModalOpen', false)" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-xl text-xs font-bold">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-black rounded-xl text-xs shadow-md">Khate Mein Save Karein</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- WhatsApp Reminder Direct Trigger Script -->
    <script>
        document.addEventListener('livewire:navigated', initLedgerWhatsAppListener);
        document.addEventListener('DOMContentLoaded', initLedgerWhatsAppListener);

        function initLedgerWhatsAppListener() {
            if (window._ledgerWhatsAppListenerAttached) return;
            window._ledgerWhatsAppListenerAttached = true;

            window.addEventListener('trigger-whatsapp', (event) => {
                const data = event.detail;
                const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

                if (isMobile) {
                    window.location.href = data.appUrl;
                } else {
                    const iframe = document.createElement('iframe');
                    iframe.style.display = 'none';
                    iframe.src = data.appUrl;
                    document.body.appendChild(iframe);

                    setTimeout(() => {
                        document.body.removeChild(iframe);
                        if (!document.hidden) {
                            window.open(data.webUrl, '_blank');
                        }
                    }, 700);
                }
            });
        }
    </script>

</div>