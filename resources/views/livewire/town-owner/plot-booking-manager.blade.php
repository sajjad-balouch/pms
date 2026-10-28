<div>
    @if ($action && $plot)
        @php
            $input = 'w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder-gray-600';
            $label = 'block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-1';
            $titles = ['book' => '📝 Book Plot', 'cancel' => '⛔ Cancel Booking', 'transfer' => '🔁 Transfer Plot', 'details' => '📋 Plot Details & History'];
            $featureLabels = \App\Support\PlotPricing::featureLabels($plot->features);
        @endphp

        <div class="fixed inset-0 z-50 flex items-start sm:items-center justify-center p-4 overflow-y-auto"
             x-data x-on:keydown.escape.window="$wire.close()">
            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" wire:click="close"></div>

            <div class="relative w-full max-w-3xl bg-gray-900 border border-gray-800 rounded-2xl shadow-2xl my-8">
                <!-- Header -->
                <div class="flex items-start justify-between gap-4 p-6 border-b border-gray-800">
                    <div>
                        <h3 class="text-lg font-black text-white">{{ $titles[$action] }}</h3>
                        <p class="text-sm text-gray-400 mt-1">
                            <span class="font-bold text-white">Plot #{{ $plot->plot_number }}</span>
                            · {{ $plot->block_name ?? 'Main' }} · {{ ucfirst($plot->type) }} · {{ $plot->size }}
                            · <span class="font-bold text-emerald-400">Rs. {{ number_format($plot->total_price) }}</span>
                        </p>
                        @if ($featureLabels)
                            <div class="flex flex-wrap gap-1 mt-2">
                                @foreach ($featureLabels as $f)
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-sky-500/10 text-sky-300 border border-sky-500/20">{{ $f }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <button type="button" wire:click="close" class="h-8 w-8 flex-shrink-0 rounded-full bg-gray-800 hover:bg-gray-700 text-gray-300">✕</button>
                </div>

                <div class="p-6 space-y-6">
                    @error('plot')
                        <div class="bg-rose-500/10 border border-rose-500/30 text-rose-400 px-4 py-2.5 rounded-xl text-sm">{{ $message }}</div>
                    @enderror

                    {{-- ================= BOOK ================= --}}
                    @if ($action === 'book')
                        <form wire:submit="confirmBooking" class="space-y-6">
                            @include('livewire.town-owner.partials.customer-picker', ['title' => 'Buyer / Customer'])

                            <div class="border-t border-gray-800 pt-5 space-y-4">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-gray-300">Payment Plan</h4>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                    <div>
                                        <label class="{{ $label }}">Booking Date *</label>
                                        <input type="date" wire:model.live="booking_date" class="{{ $input }} [color-scheme:dark]">
                                        @error('booking_date') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="{{ $label }}">Discount (Rs)</label>
                                        <input type="number" min="0" step="1" wire:model.live.debounce.400ms="discount" class="{{ $input }}">
                                        @error('discount') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="{{ $label }}">Down Payment (Rs) *</label>
                                        <input type="number" min="0" step="1" wire:model.live.debounce.400ms="down_payment" class="{{ $input }}">
                                        @error('down_payment') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="{{ $label }}">No. of Installments *</label>
                                        <input type="number" min="0" max="360" wire:model.live.debounce.400ms="installments_count" class="{{ $input }}">
                                        @error('installments_count') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="{{ $label }}">Frequency *</label>
                                        <select wire:model.live="interval_months" class="{{ $input }}">
                                            <option value="1">Monthly</option>
                                            <option value="3">Quarterly (3 months)</option>
                                            <option value="6">Half-yearly (6 months)</option>
                                            <option value="12">Yearly</option>
                                        </select>
                                        @error('interval_months') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="{{ $label }}">First Installment Date *</label>
                                        <input type="date" wire:model.live="first_due_date" class="{{ $input }} [color-scheme:dark]">
                                        @error('first_due_date') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <label class="inline-flex items-center gap-2 text-sm text-gray-300 cursor-pointer">
                                    <input type="checkbox" wire:model="down_payment_received" class="rounded bg-gray-950 border-gray-700 text-indigo-500 focus:ring-indigo-500">
                                    Down payment abhi wusool ho gayi hai (paid mark karein)
                                </label>

                                <div>
                                    <label class="{{ $label }}">Remarks</label>
                                    <input type="text" wire:model="remarks" placeholder="Optional" class="{{ $input }}">
                                </div>
                            </div>

                            <!-- Summary + schedule preview -->
                            <div class="bg-gray-950/60 border border-gray-800 rounded-2xl p-4 space-y-3" wire:loading.class="opacity-60" wire:target="discount,down_payment,installments_count,interval_months,first_due_date">
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                                    <div><p class="text-[11px] text-gray-500 uppercase font-bold">Final Price</p><p class="font-black text-white">Rs. {{ number_format($finalPrice) }}</p></div>
                                    <div><p class="text-[11px] text-gray-500 uppercase font-bold">Down</p><p class="font-black text-emerald-400">Rs. {{ number_format((float) $down_payment) }}</p></div>
                                    <div><p class="text-[11px] text-gray-500 uppercase font-bold">Balance</p><p class="font-black text-amber-400">Rs. {{ number_format($balance) }}</p></div>
                                    <div><p class="text-[11px] text-gray-500 uppercase font-bold">Per Installment</p><p class="font-black text-indigo-400">Rs. {{ number_format($schedule[0]['amount'] ?? 0) }}</p></div>
                                </div>

                                @if (count($schedule))
                                    <div class="max-h-48 overflow-y-auto border-t border-gray-800 pt-3">
                                        <table class="w-full text-xs">
                                            <thead class="text-gray-500 uppercase">
                                                <tr><th class="text-left py-1">#</th><th class="text-left py-1">Due Date</th><th class="text-right py-1">Amount</th></tr>
                                            </thead>
                                            <tbody class="text-gray-300">
                                                @foreach ($schedule as $row)
                                                    <tr class="border-t border-gray-800/60">
                                                        <td class="py-1">{{ $row['number'] }}</td>
                                                        <td class="py-1">{{ $row['due_date']->format('d M Y') }}</td>
                                                        <td class="py-1 text-right">Rs. {{ number_format($row['amount']) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <p class="text-[11px] text-gray-500">Aakhri qist: {{ end($schedule)['due_date']->format('d M Y') }}</p>
                                @else
                                    <p class="text-xs text-gray-500 text-center">Poori raqam down payment mein ada — koi installment nahi banegi.</p>
                                @endif
                            </div>

                            <div class="flex justify-end gap-3">
                                <button type="button" wire:click="close" class="bg-gray-800 hover:bg-gray-700 text-gray-300 border border-gray-700 font-bold py-2.5 px-5 rounded-xl text-sm">Cancel</button>
                                <button type="submit" wire:loading.attr="disabled" wire:target="confirmBooking" class="bg-amber-600/20 hover:bg-amber-600/30 text-amber-300 border border-amber-500/30 font-bold py-2.5 px-6 rounded-xl text-sm disabled:opacity-50">
                                    <span wire:loading.remove wire:target="confirmBooking">✓ Confirm Booking</span>
                                    <span wire:loading wire:target="confirmBooking" class="animate-pulse">Booking...</span>
                                </button>
                            </div>
                        </form>
                    @endif

                    {{-- Booking summary block (cancel / transfer / details) --}}
                    @if ($summary && $action !== 'book')
                        @php $owner = $summary['booking']?->customer ?? $plot->customer; @endphp
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="bg-gray-950/60 border border-gray-800 rounded-2xl p-4">
                                <p class="text-[11px] text-gray-500 uppercase font-bold">Current Owner</p>
                                @if ($owner)
                                    <p class="font-black text-white mt-1">{{ $owner->name }}</p>
                                    @if ($owner->father_name) <p class="text-xs text-gray-400">S/O {{ $owner->father_name }}</p> @endif
                                    <p class="text-xs text-gray-400">{{ collect([$owner->cnic, $owner->phone])->filter()->implode(' · ') }}</p>
                                @elseif ($plot->status !== 'available')
                                    <p class="text-sm text-gray-500 mt-1">Customer record nahi (purani booking)</p>
                                @else
                                    <p class="text-sm text-gray-500 mt-1">— Available —</p>
                                @endif
                                @if ($summary['booking'])
                                    <p class="text-[11px] text-gray-500 mt-2">Booked: {{ $summary['booking']->booking_date?->format('d M Y') }}</p>
                                @endif
                            </div>

                            @if ($plot->status !== 'available')
                                <div class="bg-gray-950/60 border border-gray-800 rounded-2xl p-4 grid grid-cols-2 gap-3">
                                    <div><p class="text-[11px] text-gray-500 uppercase font-bold">Received</p><p class="font-black text-emerald-400">Rs. {{ number_format($summary['paid']) }}</p></div>
                                    <div><p class="text-[11px] text-gray-500 uppercase font-bold">Remaining</p><p class="font-black text-amber-400">Rs. {{ number_format($summary['remaining']) }}</p></div>
                                    <div><p class="text-[11px] text-gray-500 uppercase font-bold">Overdue</p><p class="font-black {{ $summary['overdue'] > 0 ? 'text-red-400' : 'text-gray-400' }}">Rs. {{ number_format($summary['overdue']) }}</p></div>
                                    <div><p class="text-[11px] text-gray-500 uppercase font-bold">Installments</p><p class="font-black text-white">{{ $summary['paid_count'] }}/{{ $summary['count'] }} paid</p></div>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- ================= CANCEL ================= --}}
                    @if ($action === 'cancel')
                        <form wire:submit="confirmCancel" class="space-y-4">
                            <div class="bg-rose-500/10 border border-rose-500/30 text-rose-300 px-4 py-3 rounded-xl text-xs space-y-1">
                                <p>• Tamam <b>unpaid</b> installments delete ho jayengi. Paid installments record ke liye mehfooz rahengi.</p>
                                <p>• Plot dobara <b>Available</b> ho jayega. Refund dashboard par outflow mein shamil hoga.</p>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="{{ $label }}">Refund Amount (Rs) — max {{ number_format($summary['paid'] ?? 0) }}</label>
                                    <input type="number" min="0" step="1" wire:model="refund_amount" class="{{ $input }}">
                                    @error('refund_amount') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="{{ $label }}">Refund Date</label>
                                    <input type="date" wire:model="refund_date" class="{{ $input }} [color-scheme:dark]">
                                    @error('refund_date') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="{{ $label }}">Reason *</label>
                                    <textarea wire:model="cancel_reason" rows="2" placeholder="e.g. Customer ne qistein ada nahi kin / khud cancel karwaya" class="{{ $input }}"></textarea>
                                    @error('cancel_reason') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="flex justify-end gap-3">
                                <button type="button" wire:click="close" class="bg-gray-800 hover:bg-gray-700 text-gray-300 border border-gray-700 font-bold py-2.5 px-5 rounded-xl text-sm">Back</button>
                                <button type="submit" wire:confirm="Booking cancel karna wapas nahi ho sakta. Continue?" wire:loading.attr="disabled" wire:target="confirmCancel" class="bg-rose-600/20 hover:bg-rose-600/30 text-rose-300 border border-rose-500/30 font-bold py-2.5 px-6 rounded-xl text-sm disabled:opacity-50">
                                    ⛔ Cancel Booking
                                </button>
                            </div>
                        </form>
                    @endif

                    {{-- ================= TRANSFER ================= --}}
                    @if ($action === 'transfer')
                        <form wire:submit="confirmTransfer" class="space-y-5">
                            @include('livewire.town-owner.partials.customer-picker', ['title' => 'New Owner (Transfer To)'])

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 border-t border-gray-800 pt-5">
                                <div>
                                    <label class="{{ $label }}">Transfer Date *</label>
                                    <input type="date" wire:model="transfer_date" class="{{ $input }} [color-scheme:dark]">
                                    @error('transfer_date') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="{{ $label }}">Transfer Fee (Rs)</label>
                                    <input type="number" min="0" step="1" wire:model="transfer_fee" class="{{ $input }}">
                                    @error('transfer_fee') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="{{ $label }}">Remarks</label>
                                    <input type="text" wire:model="transfer_remarks" class="{{ $input }}">
                                </div>
                            </div>
                            <p class="text-xs text-gray-500">Baqi installments naye owner ke naam ho jayengi. Payment plan wahi rahega.</p>
                            <div class="flex justify-end gap-3">
                                <button type="button" wire:click="close" class="bg-gray-800 hover:bg-gray-700 text-gray-300 border border-gray-700 font-bold py-2.5 px-5 rounded-xl text-sm">Back</button>
                                <button type="submit" wire:loading.attr="disabled" wire:target="confirmTransfer" class="bg-sky-600/20 hover:bg-sky-600/30 text-sky-300 border border-sky-500/30 font-bold py-2.5 px-6 rounded-xl text-sm disabled:opacity-50">
                                    🔁 Confirm Transfer
                                </button>
                            </div>
                        </form>
                    @endif

                    {{-- ================= DETAILS / HISTORY ================= --}}
                    @if ($action === 'details')
                        <div class="flex flex-wrap gap-2">
                            @if ($plot->status === 'available')
                                <button type="button" wire:click="switchAction('book')" class="bg-amber-600/20 hover:bg-amber-600/30 text-amber-300 border border-amber-500/30 px-4 py-2 rounded-xl text-xs font-bold">📝 Book this Plot</button>
                            @else
                                <a href="{{ route('town_owner.installments', $plot->id) }}" wire:navigate class="bg-indigo-600/20 hover:bg-indigo-600/30 text-indigo-300 border border-indigo-500/30 px-4 py-2 rounded-xl text-xs font-bold">💳 Installments</a>
                                <button type="button" wire:click="switchAction('transfer')" class="bg-sky-600/20 hover:bg-sky-600/30 text-sky-300 border border-sky-500/30 px-4 py-2 rounded-xl text-xs font-bold">🔁 Transfer</button>
                                <button type="button" wire:click="switchAction('cancel')" class="bg-rose-600/20 hover:bg-rose-600/30 text-rose-300 border border-rose-500/30 px-4 py-2 rounded-xl text-xs font-bold">⛔ Cancel Booking</button>
                            @endif
                        </div>

                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-300 mb-3">History</h4>
                            @php
                                $timeline = collect();
                                foreach ($bookings as $b) {
                                    $timeline->push(['date' => $b->booking_date, 'icon' => '📝', 'classes' => 'border-amber-500/30 text-amber-300',
                                        'title' => 'Booked by ' . ($b->customer?->name ?? 'Unknown (legacy)'),
                                        'body' => 'Price Rs. ' . number_format($b->final_price) . ($b->discount > 0 ? ' (discount Rs. ' . number_format($b->discount) . ')' : '') . ' · Down Rs. ' . number_format($b->down_payment) . ' · ' . $b->installments_count . ' installments']);
                                    if ($b->status === 'cancelled') {
                                        $timeline->push(['date' => $b->cancelled_at, 'icon' => '⛔', 'classes' => 'border-rose-500/30 text-rose-300',
                                            'title' => 'Booking cancelled' . ($b->customer ? ' (' . $b->customer->name . ')' : ''),
                                            'body' => trim(($b->refund_amount > 0 ? 'Refund Rs. ' . number_format($b->refund_amount) . ' on ' . $b->refund_date?->format('d M Y') . '. ' : 'No refund. ') . ($b->cancel_reason ?? ''))]);
                                    }
                                }
                                foreach ($transfers as $t) {
                                    $timeline->push(['date' => $t->transfer_date, 'icon' => '🔁', 'classes' => 'border-sky-500/30 text-sky-300',
                                        'title' => 'Transferred: ' . ($t->fromCustomer?->name ?? 'Unknown') . ' → ' . ($t->toCustomer?->name ?? 'Unknown'),
                                        'body' => 'Fee Rs. ' . number_format($t->transfer_fee) . ($t->remarks ? ' · ' . $t->remarks : '')]);
                                }
                                $timeline = $timeline->sortByDesc(fn ($e) => $e['date']?->timestamp ?? 0)->values();
                            @endphp

                            <ol class="space-y-3">
                                @forelse ($timeline as $event)
                                    <li class="flex gap-3">
                                        <span class="flex-shrink-0 h-8 w-8 flex items-center justify-center rounded-xl border bg-gray-950 {{ $event['classes'] }}">{{ $event['icon'] }}</span>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-gray-200">{{ $event['title'] }}</p>
                                            <p class="text-xs text-gray-500">{{ $event['date']?->format('d M Y') }} · {{ $event['body'] }}</p>
                                        </div>
                                    </li>
                                @empty
                                    <li class="text-sm text-gray-500">Is plot ki abhi koi booking history nahi.</li>
                                @endforelse
                            </ol>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
