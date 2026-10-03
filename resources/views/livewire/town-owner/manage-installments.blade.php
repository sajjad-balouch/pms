<div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 bg-gray-950 text-gray-100 min-h-screen">

    <!-- Top Navigation Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-gray-900/80 backdrop-blur-md p-6 rounded-2xl border border-gray-800/80 shadow-2xl gap-4">
        <div>
            <h2 class="text-2xl font-black text-white tracking-tight flex items-center gap-2">
                <span>💳</span> Installment Ledger: Plot #{{ $plot->plot_number }}
            </h2>
            <p class="text-xs text-gray-400 mt-1 font-medium">
                {{ $plot->town->name }} — ({{ $plot->size }} - {{ ucfirst($plot->type) }})
            </p>
        </div>
        <a href="{{ route('town_owner.plots', $plot->town_id) }}" class="inline-flex items-center gap-2 bg-gray-800 hover:bg-gray-700 text-gray-300 border border-gray-700/80 px-4 py-2 rounded-xl text-xs font-semibold transition-all duration-200 shadow-sm">
            <span>←</span> Back to Plots
        </a>
    </div>

    <!-- Alert / Session Messages -->
    @if (session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-5 py-3.5 rounded-xl text-sm font-medium flex items-center gap-3">
            <span>✅</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('message'))
        <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-5 py-3.5 rounded-xl text-sm font-medium flex items-center gap-3">
            <span>✅</span>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Payment Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gray-900/80 backdrop-blur-md p-6 rounded-2xl border border-gray-800/80 border-l-4 border-l-indigo-500 shadow-xl space-y-1">
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Price / Down Payment</p>
            <p class="text-2xl font-black text-white tracking-tight">
                Rs. {{ number_format($plot->total_price ?? $plot->price) }}
            </p>
            <p class="text-xs text-emerald-400 font-bold">
                Adv: Rs. {{ number_format($plot->down_payment) }}
            </p>
        </div>

        <div class="bg-gray-900/80 backdrop-blur-md p-6 rounded-2xl border border-gray-800/80 border-l-4 border-l-amber-500 shadow-xl space-y-1">
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Next Upcoming Due Date</p>
            @if ($nextDue)
                <p class="text-2xl font-black text-amber-400 tracking-tight">
                    {{ \Carbon\Carbon::parse($nextDue->due_date)->format('d M, Y') }}
                </p>
                <p class="text-xs text-gray-400">
                    Amount: Rs. {{ number_format($nextDue->amount) }} (Inst #{{ $nextDue->installment_number }})
                </p>
            @else
                <p class="text-2xl font-black text-emerald-400 tracking-tight">All Paid / No Plan</p>
            @endif
        </div>

        <div class="bg-gray-900/80 backdrop-blur-md p-6 rounded-2xl border border-gray-800/80 border-l-4 border-l-purple-500 shadow-xl space-y-1">
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Buyer Details</p>
            <p class="text-lg font-black text-white tracking-tight">{{ $buyer_name ?: 'Not Assigned' }}</p>
            <p class="text-xs text-gray-400 font-medium">{{ $buyer_phone ?: 'N/A' }}</p>
        </div>
    </div>

    <!-- Form Section (When no installments generated) -->
    @if ($installments->isEmpty())
        <div class="bg-gray-900/80 backdrop-blur-md p-6 rounded-2xl border border-gray-800/80 shadow-2xl space-y-6">
            <div class="border-b border-gray-800/80 pb-4">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <span>👤</span> Buyer Details & Payment Method
                </h3>
                <p class="text-xs text-gray-400 mt-0.5">Kharidar ke makhsoos credentials darj karke installment plan generate karein.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Buyer Name *</label>
                    <input type="text" wire:model="buyer_name" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder-gray-600 transition-colors" placeholder="e.g. Ali Raza" required />
                    @error('buyer_name') <span class="text-xs text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Buyer Phone *</label>
                    <input type="text" wire:model="buyer_phone" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder-gray-600 transition-colors" placeholder="e.g. 03001234567" required />
                    @error('buyer_phone') <span class="text-xs text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 border-t border-gray-800/80 pt-5">
                <button type="button" wire:click="generateInstallmentPlan" wire:loading.attr="disabled" class="flex-1 inline-flex items-center justify-center gap-2 bg-indigo-600/20 hover:bg-indigo-600/30 text-indigo-300 border border-indigo-500/30 font-bold py-3 px-4 rounded-xl text-sm shadow-sm transition-all duration-200">
                    <span wire:loading.remove>📅 Generate {{ $plot->total_installments }} Months Plan</span>
                    <span wire:loading class="animate-pulse">Generating Schedule...</span>
                </button>

                <button type="button" wire:click="markAsFullPaid" wire:confirm="Kya aap buyer ke details ke sath full payment (Rs. {{ number_format($plot->total_price ?? $plot->price) }}) submit karna chahte hain?" wire:loading.attr="disabled" class="flex-1 inline-flex items-center justify-center gap-2 bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-300 border border-emerald-500/30 font-bold py-3 px-4 rounded-xl text-sm shadow-sm transition-all duration-200">
                    <span wire:loading.remove>💰 Direct Full Payment</span>
                    <span wire:loading class="animate-pulse">Processing Payment...</span>
                </button>
            </div>
        </div>
    @else
        <!-- Installments List Table -->
        <div class="bg-gray-900/80 rounded-2xl border border-gray-800/80 shadow-2xl overflow-hidden">
            <div class="p-6 border-b border-gray-800/80 flex justify-between items-center">
                <h3 class="text-lg font-extrabold text-white flex items-center gap-2">
                    <span>📋</span> Installments Schedule
                </h3>
                @if ($plot->status === 'sold')
                    <span class="inline-flex items-center gap-1 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-bold px-3 py-1 rounded-full text-xs">
                        ✅ Paid In Full
                    </span>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-800/80 text-sm">
                    <thead class="bg-gray-950/50 text-gray-400 text-xs uppercase tracking-wider">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left font-bold">Inst #</th>
                            <th scope="col" class="px-6 py-4 text-left font-bold">Amount</th>
                            <th scope="col" class="px-6 py-4 text-left font-bold">Due Date</th>
                            <th scope="col" class="px-6 py-4 text-left font-bold">Paid Date</th>
                            <th scope="col" class="px-6 py-4 text-left font-bold">Status</th>
                            <th scope="col" class="px-6 py-4 text-right font-bold">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/60 text-gray-300">
                        @foreach ($installments as $inst)
                            <tr class="hover:bg-gray-800/40 transition-colors">
                                <td class="px-6 py-4 font-bold text-white whitespace-nowrap">
                                    Installment #{{ $inst->installment_number }}
                                </td>
                                <td class="px-6 py-4 font-black text-white whitespace-nowrap">
                                    Rs. {{ number_format($inst->amount) }}
                                </td>
                                <td class="px-6 py-4 font-semibold text-amber-400 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($inst->due_date)->format('d M, Y') }}
                                </td>
                                <td class="px-6 py-4 text-gray-400 whitespace-nowrap">
                                    {{ $inst->paid_date ? \Carbon\Carbon::parse($inst->paid_date)->format('d M, Y') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($inst->status === 'paid')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                            Paid
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    @if ($inst->status === 'pending')
                                        <button wire:click="markAsPaid({{ $inst->id }})" 
                                                wire:confirm="Kya kist vasool ho gayi hai?" 
                                                class="inline-flex items-center gap-1 bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-300 border border-emerald-500/30 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all duration-200 shadow-sm">
                                            Receive Payment
                                        </button>
                                    @else
                                        <div class="flex items-center justify-end gap-3">
                                            <span class="text-xs text-emerald-400 font-bold">✓ Cleared</span>
                                            <a href="{{ route('town_owner.receipt.print', $inst->id) }}" 
                                               target="_blank" 
                                               class="inline-flex items-center gap-1.5 bg-blue-600/20 hover:bg-blue-600/30 text-blue-300 border border-blue-500/30 px-3 py-1.5 rounded-xl text-xs font-bold transition-all duration-200">
                                                <span>🖨️</span> Receipt
                                            </a>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>