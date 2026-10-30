<div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 bg-gray-950 text-gray-100 min-h-screen">

    <!-- Booking / cancel / transfer / details modals -->
    <livewire:town-owner.plot-booking-manager :town-id="$plot->town_id" />

    <!-- Top Navigation Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center bg-gray-900/80 backdrop-blur-md p-6 rounded-2xl border border-gray-800/80 shadow-2xl gap-4">
        <div>
            <h2 class="text-2xl font-black text-white tracking-tight flex items-center gap-2">
                <span>💳</span> Installment Ledger: Plot #{{ $plot->plot_number }}
            </h2>
            <p class="text-xs text-gray-400 mt-1 font-medium">
                {{ $plot->town->name }} — {{ $plot->block_name ?? 'Main' }} · {{ $plot->size }} · {{ ucfirst($plot->type) }}
                @if ($booking) · <span class="text-gray-300">Allotment {{ $booking->allotmentNumber() }}</span> @endif
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if ($booking)
                <a href="{{ route('town_owner.allotment.letter', $booking->id) }}" target="_blank" class="inline-flex items-center gap-2 bg-amber-600/20 hover:bg-amber-600/30 text-amber-300 border border-amber-500/30 px-4 py-2 rounded-xl text-xs font-bold">📄 Allotment Letter</a>
            @endif
            @if ($plot->status !== 'available')
                <button type="button" x-on:click="$dispatch('open-plot-action', { plotId: {{ $plot->id }}, action: 'details' })" class="inline-flex items-center gap-2 bg-sky-600/20 hover:bg-sky-600/30 text-sky-300 border border-sky-500/30 px-4 py-2 rounded-xl text-xs font-bold">📋 Transfer / Cancel</button>
            @endif
            <a href="{{ route('town_owner.plots', $plot->town_id) }}" wire:navigate class="inline-flex items-center gap-2 bg-gray-800 hover:bg-gray-700 text-gray-300 border border-gray-700/80 px-4 py-2 rounded-xl text-xs font-semibold">
                <span>←</span> Back to Plots
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session('success'))
        <div wire:key="flash-success-{{ md5(session('success')) }}" x-data="{ show: true }" x-show="show" x-transition
             class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-5 py-3.5 rounded-xl text-sm font-medium flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3"><span>✅</span><span>{{ session('success') }}</span></div>
            <div class="flex items-center gap-3">
                @if ($justPaid && ($whatsapp[$justPaid->id] ?? null))
                    <a href="{{ $whatsapp[$justPaid->id] }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 bg-green-600/20 hover:bg-green-600/30 text-green-300 border border-green-500/30 px-3 py-1.5 rounded-xl text-xs font-bold">💬 Receipt WhatsApp par bhejein</a>
                @endif
                <button type="button" @click="show = false" class="text-emerald-400/60 hover:text-emerald-400">&times;</button>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-rose-500/10 border border-rose-500/30 text-rose-400 px-5 py-3.5 rounded-xl text-sm font-medium">⛔ {{ session('error') }}</div>
    @endif

    <!-- Overview Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-gray-900/80 p-5 rounded-2xl border border-gray-800/80 border-l-4 border-l-purple-500">
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Buyer</p>
            @if ($booking?->customer_id)
                <a href="{{ route('town_owner.customers', ['customer' => $booking->customer_id]) }}" wire:navigate class="block text-lg font-black text-white hover:text-indigo-300 mt-1">{{ $buyerName }} ›</a>
            @else
                <p class="text-lg font-black text-white mt-1">{{ $buyerName ?: 'Not Assigned' }}</p>
            @endif
            <p class="text-xs text-gray-400">{{ $buyerPhone ?: 'N/A' }}</p>
        </div>
        <div class="bg-gray-900/80 p-5 rounded-2xl border border-gray-800/80 border-l-4 border-l-indigo-500">
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Price</p>
            <p class="text-xl font-black text-white mt-1">Rs. {{ number_format($booking->final_price ?? $plot->total_price) }}</p>
            <p class="text-xs text-gray-400">Schedule total: Rs. {{ number_format($stats['total']) }}</p>
        </div>
        <div class="bg-gray-900/80 p-5 rounded-2xl border border-gray-800/80 border-l-4 border-l-emerald-500">
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Received / Remaining</p>
            <p class="text-xl font-black text-emerald-400 mt-1">Rs. {{ number_format($stats['paid']) }}</p>
            <p class="text-xs text-amber-400">Remaining: Rs. {{ number_format($stats['remaining']) }}</p>
            <div class="mt-2 h-1.5 w-full bg-gray-800 rounded-full overflow-hidden"><div class="h-full bg-emerald-500" style="width: {{ $stats['percent'] }}%"></div></div>
        </div>
        <div class="bg-gray-900/80 p-5 rounded-2xl border border-gray-800/80 border-l-4 {{ $stats['overdue'] > 0 ? 'border-l-red-500' : 'border-l-amber-500' }}">
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">{{ $stats['overdue'] > 0 ? 'Overdue' : 'Next Due' }}</p>
            @if ($stats['overdue'] > 0)
                <p class="text-xl font-black text-red-400 mt-1">Rs. {{ number_format($stats['overdue']) }}</p>
                <p class="text-xs text-red-400/80">{{ $stats['overdue_count'] }} qist(ein) late</p>
            @elseif ($stats['next'])
                <p class="text-xl font-black text-amber-400 mt-1">{{ \Carbon\Carbon::parse($stats['next']->due_date)->format('d M, Y') }}</p>
                <p class="text-xs text-gray-400">Rs. {{ number_format($stats['next']->amount) }}</p>
            @else
                <p class="text-xl font-black text-emerald-400 mt-1">{{ $installments->isEmpty() ? 'No Plan' : 'All Paid ✓' }}</p>
            @endif
        </div>
    </div>

    <!-- Buyer Portal -->
    @if ($plot->status !== 'available')
        <div class="bg-gradient-to-r from-indigo-900/40 to-gray-900/80 p-5 rounded-2xl border border-indigo-500/30 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <p class="font-bold text-white flex items-center gap-2">🔗 Buyer Portal</p>
                <p class="text-xs text-gray-400 mt-0.5">Customer is link par apni qistein, baqaya aur receipts khud dekh sakta hai — login ki zaroorat nahi. Transfer ya cancel par purana link khud band ho jata hai.</p>
            </div>
            @if ($portalUrl)
                <div class="flex flex-wrap gap-2" x-data="{ copied: false }">
                    <a href="{{ $portalUrl }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 bg-gray-800 hover:bg-gray-700 text-gray-200 border border-gray-700 px-3 py-2 rounded-xl text-xs font-bold">👁 Open</a>
                    <button type="button" x-on:click="navigator.clipboard.writeText(@js($portalUrl)); copied = true; setTimeout(() => copied = false, 2000)" class="inline-flex items-center gap-1.5 bg-gray-800 hover:bg-gray-700 text-gray-200 border border-gray-700 px-3 py-2 rounded-xl text-xs font-bold">
                        <span x-text="copied ? '✓ Copied' : '📋 Copy Link'"></span>
                    </button>
                    @if ($portalShare)
                        <a href="{{ $portalShare }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 bg-green-600/20 hover:bg-green-600/30 text-green-300 border border-green-500/30 px-3 py-2 rounded-xl text-xs font-bold">💬 Send on WhatsApp</a>
                    @endif
                </div>
            @else
                <button type="button" wire:click="enablePortal" class="inline-flex items-center gap-1.5 bg-indigo-600/20 hover:bg-indigo-600/30 text-indigo-300 border border-indigo-500/30 px-4 py-2 rounded-xl text-xs font-bold">✨ Portal Link Banayein</button>
            @endif
        </div>
    @endif

    @if ($installments->isEmpty())
        <div class="bg-gray-900/80 p-10 rounded-2xl border border-gray-800/80 text-center space-y-3">
            @if ($plot->status === 'available')
                <p class="text-lg font-bold text-white">Ye plot abhi available hai</p>
                <p class="text-sm text-gray-400">Booking karte hi customer ka record aur qiston ka schedule khud ban jayega.</p>
                <button type="button" x-on:click="$dispatch('open-plot-action', { plotId: {{ $plot->id }}, action: 'book' })" class="inline-flex items-center gap-2 bg-amber-600/20 hover:bg-amber-600/30 text-amber-300 border border-amber-500/30 font-bold py-3 px-6 rounded-xl text-sm">📝 Book this Plot</button>
            @else
                <p class="text-lg font-bold text-white">Is booking ka koi installment schedule nahi</p>
                <p class="text-sm text-gray-400">"Transfer / Cancel" se booking cancel karke dobara book karein — naya schedule ban jayega.</p>
            @endif
        </div>
    @else
        <!-- Installments Table -->
        <div class="bg-gray-900/80 rounded-2xl border border-gray-800/80 shadow-2xl overflow-hidden">
            <div class="p-6 border-b border-gray-800/80 flex justify-between items-center">
                <h3 class="text-lg font-extrabold text-white flex items-center gap-2"><span>📋</span> Installments Schedule</h3>
                <div class="flex items-center gap-3">
                    @if ($totalLateFees > 0)
                        <span class="text-xs text-orange-300">Late fees: Rs. {{ number_format($totalLateFees) }}</span>
                    @endif
                    <span class="text-xs text-gray-400">{{ $stats['paid_count'] }}/{{ $stats['count'] }} paid</span>
                    @if ($unpaidRegular->isNotEmpty())
                        <button type="button" wire:click="openReschedule" class="inline-flex items-center gap-1 bg-violet-600/20 hover:bg-violet-600/30 text-violet-300 border border-violet-500/30 px-3 py-1.5 rounded-xl text-xs font-bold">🔁 Reschedule</button>
                    @endif
                </div>
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
                            @php
                                $isPaid = $inst->status === 'paid';
                                $due = \Carbon\Carbon::parse($inst->due_date);
                                $late = ! $isPaid && $due->lt(today());
                            @endphp
                            <tr wire:key="inst-{{ $inst->id }}" class="hover:bg-gray-800/40 transition-colors {{ $late ? 'bg-red-500/5' : '' }}">
                                <td class="px-6 py-4 font-bold text-white whitespace-nowrap">
                                    {{ $inst->installment_number == 0 ? 'Down Payment' : 'Installment #' . $inst->installment_number }}
                                    @if ($inst->remarks && $inst->installment_number != 0)
                                        <span class="block text-[11px] font-normal text-gray-500">{{ $inst->remarks }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-black text-white whitespace-nowrap">Rs. {{ number_format($inst->amount) }}</td>
                                <td class="px-6 py-4 font-semibold whitespace-nowrap {{ $late ? 'text-red-400' : 'text-amber-400' }}">
                                    {{ $due->format('d M, Y') }}
                                    @if ($late)
                                        <span class="block text-[11px] font-normal">{{ (int) $due->diffInDays(today()) }} din late</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-400 whitespace-nowrap">
                                    {{ $inst->paid_date ? \Carbon\Carbon::parse($inst->paid_date)->format('d M, Y') : '-' }}
                                    @if ($inst->late_fee > 0)
                                        <span class="block text-[11px] text-orange-300">+ Rs. {{ number_format($inst->late_fee) }} late fee</span>
                                    @endif
                                    @if ($inst->receivedBy)
                                        <span class="block text-[11px] text-gray-500">by {{ $inst->receivedBy->name }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($isPaid)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Paid</span>
                                    @elseif ($late)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-500/10 text-red-400 border border-red-500/20">Overdue</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">Pending</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap space-x-1">
                                    @if (! $isPaid)
                                        <button type="button" wire:click="startReceive({{ $inst->id }})" class="inline-flex items-center gap-1 bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-300 border border-emerald-500/30 px-3.5 py-1.5 rounded-xl text-xs font-bold">Receive Payment</button>
                                        @if ($whatsapp[$inst->id] ?? null)
                                            <a href="{{ $whatsapp[$inst->id] }}" target="_blank" rel="noopener" title="WhatsApp reminder" class="inline-flex items-center bg-green-600/10 hover:bg-green-600/20 text-green-300 border border-green-500/20 px-2.5 py-1.5 rounded-xl text-xs font-bold">💬</a>
                                        @endif
                                    @else
                                        <a href="{{ route('town_owner.receipt.print', $inst->id) }}" target="_blank" class="inline-flex items-center gap-1.5 bg-blue-600/20 hover:bg-blue-600/30 text-blue-300 border border-blue-500/30 px-3 py-1.5 rounded-xl text-xs font-bold">🖨️ Receipt</a>
                                        @if ($whatsapp[$inst->id] ?? null)
                                            <a href="{{ $whatsapp[$inst->id] }}" target="_blank" rel="noopener" title="Receipt WhatsApp par bhejein" class="inline-flex items-center bg-green-600/10 hover:bg-green-600/20 text-green-300 border border-green-500/20 px-2.5 py-1.5 rounded-xl text-xs font-bold">💬</a>
                                        @endif
                                        <button type="button" wire:click="undoPayment({{ $inst->id }})" wire:confirm="Ye wusooli ghalat darj hui thi? Qist dobara pending ho jayegi." title="Undo payment" class="inline-flex items-center bg-gray-800 hover:bg-gray-700 text-gray-400 border border-gray-700 px-2.5 py-1.5 rounded-xl text-xs font-bold">↩</button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Reschedule Modal -->
    @if ($showReschedule)
        <div class="fixed inset-0 z-50 flex items-start sm:items-center justify-center p-4 overflow-y-auto" x-data x-on:keydown.escape.window="$wire.closeReschedule()">
            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" wire:click="closeReschedule"></div>
            <form wire:submit="confirmReschedule" class="relative w-full max-w-lg bg-gray-900 border border-gray-800 rounded-2xl shadow-2xl p-6 space-y-4 my-8">
                <div>
                    <h3 class="text-lg font-black text-white">🔁 Reschedule Payment Plan</h3>
                    <p class="text-sm text-gray-400 mt-1">
                        {{ $unpaidRegular->count() }} baqi qistein · <span class="font-bold text-amber-400">Rs. {{ number_format($unpaidRegular->sum('amount')) }}</span>
                        — ye hat kar nayi qiston mein bant jayengi. Paid qistein waisi hi rahengi.
                    </p>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-1">New Count *</label>
                        <input type="number" min="1" max="360" wire:model.live.debounce.400ms="rs_count" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-3 py-2 text-sm">
                        @error('rs_count') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-1">Every</label>
                        <select wire:model.live="rs_interval" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-3 py-2 text-sm">
                            <option value="1">1 month</option>
                            <option value="3">3 months</option>
                            <option value="6">6 months</option>
                            <option value="12">12 months</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-1">First Due *</label>
                        <input type="date" min="{{ now()->toDateString() }}" wire:model.live="rs_first_due" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-3 py-2 text-sm [color-scheme:dark]">
                        @error('rs_first_due') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-1">Reason</label>
                    <input type="text" wire:model="rs_note" maxlength="255" placeholder="e.g. Customer ki darkhwast par, 6 mahine ki mohlat" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-3 py-2 text-sm placeholder-gray-600">
                </div>

                @if (count($reschedulePreview))
                    <div class="bg-gray-950/60 border border-gray-800 rounded-xl p-3 text-xs">
                        <p class="text-gray-400">
                            Nayi qist: <span class="font-black text-indigo-400">Rs. {{ number_format($reschedulePreview[0]['amount']) }}</span>
                            · {{ $reschedulePreview[0]['due_date']->format('d M Y') }} se {{ end($reschedulePreview)['due_date']->format('d M Y') }} tak
                        </p>
                    </div>
                @endif

                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="closeReschedule" class="bg-gray-800 hover:bg-gray-700 text-gray-300 border border-gray-700 font-bold py-2.5 px-5 rounded-xl text-sm">Cancel</button>
                    <button type="submit" wire:confirm="Purani baqi qistein hat kar naya schedule banega. Continue?" wire:loading.attr="disabled" wire:target="confirmReschedule" class="bg-violet-600/20 hover:bg-violet-600/30 text-violet-300 border border-violet-500/30 font-bold py-2.5 px-6 rounded-xl text-sm disabled:opacity-50">✓ Reschedule</button>
                </div>
            </form>
        </div>
    @endif

    <!-- Receive Payment Modal -->
    @if ($receiving)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data x-on:keydown.escape.window="$wire.cancelReceive()">
            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" wire:click="cancelReceive"></div>
            <form wire:submit="confirmReceive" class="relative w-full max-w-md bg-gray-900 border border-gray-800 rounded-2xl shadow-2xl p-6 space-y-4">
                <div>
                    <h3 class="text-lg font-black text-white">💰 Receive Payment</h3>
                    <p class="text-sm text-gray-400 mt-1">
                        {{ $receiving->installment_number == 0 ? 'Down Payment' : 'Installment #' . $receiving->installment_number }}
                        · <span class="font-bold text-white">Rs. {{ number_format($receiving->amount) }}</span>
                        · due {{ \Carbon\Carbon::parse($receiving->due_date)->format('d M Y') }}
                    </p>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Payment Date *</label>
                    <input type="date" max="{{ now()->toDateString() }}" wire:model.live="received_date" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 [color-scheme:dark]">
                    @error('received_date') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                </div>
                @if ($receiving->installment_number != 0)
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-400">Late Fee (Rs)</label>
                            @if ($received_late_fee > 0)
                                <button type="button" wire:click="waiveLateFee" class="text-[11px] font-bold text-amber-400 hover:text-amber-300">Maaf karein (waive)</button>
                            @endif
                        </div>
                        <input type="number" min="0" step="1" wire:model.live.debounce.400ms="received_late_fee" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        <span class="text-[11px] text-gray-500 block mt-1">
                            @if ($lateFeePercent > 0)
                                Town setting: {{ rtrim(rtrim(number_format($lateFeePercent, 2), '0'), '.') }}% har late mahine ka. Suggestion badal sakte hain.
                            @else
                                Late fee ki setting Schemes → Edit mein "Late fee % per month" se lagayein.
                            @endif
                        </span>
                        @error('received_late_fee') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <p class="text-sm text-gray-300">Kul wusooli: <span class="font-black text-emerald-400">Rs. {{ number_format((float) $receiving->amount + (float) ($received_late_fee ?: 0)) }}</span></p>
                @endif
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Remarks</label>
                    <input type="text" wire:model="received_remarks" placeholder="e.g. Cash / Bank transfer / Cheque #1234" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 placeholder-gray-600">
                    @error('received_remarks') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" wire:click="cancelReceive" class="bg-gray-800 hover:bg-gray-700 text-gray-300 border border-gray-700 font-bold py-2.5 px-5 rounded-xl text-sm">Cancel</button>
                    <button type="submit" wire:loading.attr="disabled" wire:target="confirmReceive" class="bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-300 border border-emerald-500/30 font-bold py-2.5 px-6 rounded-xl text-sm disabled:opacity-50">✓ Confirm Received</button>
                </div>
            </form>
        </div>
    @endif

</div>
