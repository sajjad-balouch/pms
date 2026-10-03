<div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 bg-gray-950 text-gray-100 min-h-screen">

    <!-- Top Header & Back Link -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-gray-900/80 backdrop-blur-md p-6 rounded-2xl border border-gray-800/80 shadow-2xl gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-2xl">📍</span>
                <h2 class="text-2xl font-black text-white tracking-tight">Plot Inventory: {{ $town->name }}</h2>
            </div>
            <p class="text-xs text-gray-400 mt-1 font-medium">{{ $town->city }} — {{ $town->location }}</p>
        </div>
        <a href="{{ route('town_owner.towns') }}" class="inline-flex items-center gap-2 bg-gray-800 hover:bg-gray-700 text-gray-300 border border-gray-700/80 px-4 py-2 rounded-xl text-xs font-semibold transition-all duration-200 shadow-sm">
            <span>←</span> Back to Towns
        </a>
    </div>

    <!-- Success Flash Message -->
    @if (session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-5 py-3.5 rounded-xl text-sm font-medium flex items-center gap-3">
            <span>✅</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Add New Plot Form Card -->
    <div class="bg-gray-900/80 backdrop-blur-md p-6 rounded-2xl border border-gray-800/80 shadow-2xl space-y-6">
        <div class="border-b border-gray-800/80 pb-4">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <span>➕</span> Add Single Plot to Inventory
            </h3>
            <p class="text-xs text-gray-400 mt-0.5">Plot ki details aur payment plan yahan add karein.</p>
        </div>

        <form wire:submit.prevent="addPlot" class="grid grid-cols-1 md:grid-cols-4 gap-5">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Plot No. *</label>
                <input type="text" wire:model="plot_number" placeholder="e.g. 102" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder-gray-600 transition-colors" required />
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Block / Sector</label>
                <input type="text" wire:model="block_name" placeholder="e.g. Block A" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder-gray-600 transition-colors" />
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Type *</label>
                <select wire:model="type" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors">
                    <option value="residential">Residential</option>
                    <option value="commercial">Commercial</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Size *</label>
                <select wire:model="size" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors">
                    <option value="1 Marla">1 Marla</option>
                    <option value="2 Marla">2 Marla</option>
                    <option value="3 Marla">3 Marla</option>
                    <option value="4 Marla">4 Marla</option>
                    <option value="5 Marla">5 Marla</option>
                    <option value="6 Marla">6 Marla</option>
                    <option value="7 Marla">7 Marla</option>
                    <option value="8 Marla">8 Marla</option>
                    <option value="9 Marla">9 Marla</option>
                    <option value="10 Marla">10 Marla</option>
                    <option value="11 Marla">11 Marla</option>
                    <option value="12 Marla">12 Marla</option>
                    <option value="13 Marla">13 Marla</option>
                    <option value="14 Marla">14 Marla</option>
                    <option value="15 Marla">15 Marla</option>
                    <option value="16 Marla">16 Marla</option>
                    <option value="17 Marla">17 Marla</option>
                    <option value="18 Marla">18 Marla</option>
                    <option value="19 Marla">19 Marla</option>
                    <option value="1 Kanal">1 Kanal</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Total Price (Rs) *</label>
                <input type="number" step="0.01" wire:model="total_price" placeholder="2500000" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder-gray-600 transition-colors" required />
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Down Payment (Rs) *</label>
                <input type="number" step="0.01" wire:model="down_payment" placeholder="500000" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder-gray-600 transition-colors" required />
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Total Installments (Months)</label>
                <input type="number" wire:model="total_installments" placeholder="36" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder-gray-600 transition-colors" required />
            </div>

            <div class="flex items-end">
                <button type="submit" wire:loading.attr="disabled" class="w-full inline-flex items-center justify-center gap-2 bg-indigo-600/20 hover:bg-indigo-600/30 text-indigo-300 border border-indigo-500/30 font-bold py-2.5 px-4 rounded-xl text-sm shadow-sm hover:shadow-indigo-500/10 transition-all duration-200">
                    <span wire:loading.remove>+ Add Plot</span>
                    <span wire:loading class="animate-pulse">Adding...</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Plots Inventory List Card -->
    <div class="bg-gray-900/80 rounded-2xl border border-gray-800/80 shadow-2xl overflow-hidden">
        <div class="p-6 border-b border-gray-800/80">
            <h3 class="text-lg font-extrabold text-white">Plots List</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-800/80 text-sm">
                <thead class="bg-gray-950/50 text-gray-400 text-xs uppercase tracking-wider">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left font-bold">Plot #</th>
                        <th scope="col" class="px-6 py-4 text-left font-bold">Block</th>
                        <th scope="col" class="px-6 py-4 text-left font-bold">Type & Size</th>
                        <th scope="col" class="px-6 py-4 text-left font-bold">Total Price</th>
                        <th scope="col" class="px-6 py-4 text-left font-bold">Down Payment</th>
                        <th scope="col" class="px-6 py-4 text-left font-bold">Status</th>
                        <th scope="col" class="px-6 py-4 text-right font-bold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60 text-gray-300">
                    @forelse ($plots as $plot)
                        <tr class="hover:bg-gray-800/40 transition-colors">
                            <td class="px-6 py-4 font-black text-white whitespace-nowrap">#{{ $plot->plot_number }}</td>
                            <td class="px-6 py-4 text-gray-400 whitespace-nowrap">{{ $plot->block_name ?? 'Main' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="uppercase text-xs font-bold text-indigo-400">
                                    {{ $plot->type }}
                                </span>
                                <span class="text-gray-400 text-xs">({{ $plot->size }})</span>
                            </td>
                            <td class="px-6 py-4 font-bold text-white whitespace-nowrap">Rs. {{ number_format($plot->total_price) }}</td>
                            <td class="px-6 py-4 font-semibold text-emerald-400 whitespace-nowrap">Rs. {{ number_format($plot->down_payment) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($plot->status === 'available')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Available</span>
                                @elseif ($plot->status === 'booked')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">Booked</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">Sold Out</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap space-x-2">
                                @if ($plot->status === 'available')
                                    <button wire:click="updateStatus({{ $plot->id }}, 'booked')" class="inline-flex items-center gap-1 bg-amber-600/20 hover:bg-amber-600/30 text-amber-300 border border-amber-500/30 px-3 py-1.5 rounded-xl text-xs font-bold transition-all duration-200">
                                        Mark Booked
                                    </button>
                                @elseif ($plot->status === 'booked')
                                    <button wire:click="updateStatus({{ $plot->id }}, 'sold')" class="inline-flex items-center gap-1 bg-rose-600/20 hover:bg-rose-600/30 text-rose-300 border border-rose-500/30 px-3 py-1.5 rounded-xl text-xs font-bold transition-all duration-200">
                                        Mark Sold
                                    </button>
                                    <button wire:click="updateStatus({{ $plot->id }}, 'available')" class="inline-flex items-center gap-1 bg-gray-800 hover:bg-gray-700 text-gray-300 border border-gray-700 px-3 py-1.5 rounded-xl text-xs font-bold transition-all duration-200">
                                        Make Available
                                    </button>
                                    <a href="{{ route('town_owner.installments', $plot->id) }}" class="inline-flex items-center gap-1 bg-indigo-600/20 hover:bg-indigo-600/30 text-indigo-300 border border-indigo-500/30 px-3 py-1.5 rounded-xl text-xs font-bold transition-all duration-200">
                                        <span>💳</span> Installments
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                                <p class="text-base font-semibold">No plots added to this town yet.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($plots->hasPages())
            <div class="p-4 border-t border-gray-800/80 bg-gray-950/30">
                {{ $plots->links() }}
            </div>
        @endif
    </div>

</div>