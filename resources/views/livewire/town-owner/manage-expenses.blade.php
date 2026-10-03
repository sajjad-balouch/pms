<div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 bg-gray-950 text-gray-100 min-h-screen">

    <!-- Top Navigation Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-gray-900/80 backdrop-blur-md p-6 rounded-2xl border border-gray-800/80 shadow-2xl gap-4">
        <div>
            <h2 class="text-2xl font-black text-white tracking-tight flex items-center gap-2">
                <span>🏢</span> Town Expenses: {{ $town->name }}
            </h2>
            <p class="text-xs text-gray-400 mt-1 font-medium">اخراجات اور بلز کی تفصیلات</p>
        </div>
        <div class="text-left sm:text-right bg-gray-950/60 sm:bg-transparent p-3 sm:p-0 rounded-xl border border-gray-800/60 sm:border-none w-full sm:w-auto">
            <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Expenses</span>
            <p class="text-2xl font-black text-rose-400 tracking-tight">Rs. {{ number_format($totalExpenseSum) }}</p>
        </div>
    </div>

    <!-- Alert / Session Messages -->
    @if (session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-5 py-3.5 rounded-xl text-sm font-medium flex items-center gap-3 shadow-sm">
            <span>✅</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Expense Entry Form Card -->
        <div class="bg-gray-900/80 backdrop-blur-md p-6 rounded-2xl border border-gray-800/80 shadow-2xl space-y-5 h-fit">
            <div class="border-b border-gray-800/80 pb-4">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <span>💸</span> Add New Expense
                </h3>
                <p class="text-xs text-gray-400 mt-0.5">Naya akhrajat darj karein.</p>
            </div>

            <form wire:submit.prevent="addExpense" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Expense Title *</label>
                    <input type="text" wire:model="title" placeholder="e.g. Electric Bill, Office Tea" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-rose-500 focus:ring-1 focus:ring-rose-500 placeholder-gray-600 transition-colors" required />
                    @error('title') <span class="text-xs text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Category</label>
                    <select wire:model="category" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-rose-500 focus:ring-1 focus:ring-rose-500 transition-colors">
                        <option value="Utilities">Utilities (Bijli/Gas/Water)</option>
                        <option value="Maintenance">Development & Repair</option>
                        <option value="Legal">Legal & Govt Fees</option>
                        <option value="Office">Office Expenses</option>
                        <option value="General">Other General</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Amount (Rs.) *</label>
                    <input type="number" wire:model="amount" placeholder="5000" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-rose-500 focus:ring-1 focus:ring-rose-500 placeholder-gray-600 transition-colors" required />
                    @error('amount') <span class="text-xs text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Expense Date *</label>
                    <input type="date" wire:model="expense_date" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-rose-500 focus:ring-1 focus:ring-rose-500 transition-colors" required />
                    @error('expense_date') <span class="text-xs text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Paid To</label>
                    <input type="text" wire:model="paid_to" placeholder="e.g. Vendor name, Person" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-rose-500 focus:ring-1 focus:ring-rose-500 placeholder-gray-600 transition-colors" />
                </div>

                <button type="submit" wire:loading.attr="disabled" class="w-full inline-flex items-center justify-center gap-2 bg-rose-600/20 hover:bg-rose-600/30 text-rose-300 border border-rose-500/30 font-bold py-3 px-4 rounded-xl text-sm shadow-sm transition-all duration-200">
                    <span wire:loading.remove>+ Add Expense Record</span>
                    <span wire:loading class="animate-pulse">Saving Record...</span>
                </button>
            </form>
        </div>

        <!-- Expenses History Table Card -->
        <div class="lg:col-span-2 bg-gray-900/80 rounded-2xl border border-gray-800/80 shadow-2xl overflow-hidden">
            <div class="p-6 border-b border-gray-800/80">
                <h3 class="text-lg font-extrabold text-white flex items-center gap-2">
                    <span>📜</span> Expenses History
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-800/80 text-sm">
                    <thead class="bg-gray-950/50 text-gray-400 text-xs uppercase tracking-wider">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left font-bold">Title</th>
                            <th scope="col" class="px-6 py-4 text-left font-bold">Category</th>
                            <th scope="col" class="px-6 py-4 text-left font-bold">Amount</th>
                            <th scope="col" class="px-6 py-4 text-left font-bold">Date</th>
                            <th scope="col" class="px-6 py-4 text-right font-bold">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/60 text-gray-300">
                        @forelse($expenses as $exp)
                            <tr class="hover:bg-gray-800/40 transition-colors">
                                <td class="px-6 py-4 font-bold text-white whitespace-nowrap">
                                    {{ $exp->title }}
                                    @if($exp->paid_to)
                                        <span class="block text-xs font-normal text-gray-500 mt-0.5">Paid to: {{ $exp->paid_to }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-800 text-gray-300 border border-gray-700/80">
                                        {{ $exp->category }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-black text-rose-400 whitespace-nowrap">
                                    Rs. {{ number_format($exp->amount) }}
                                </td>
                                <td class="px-6 py-4 text-gray-400 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($exp->expense_date)->format('d M, Y') }}
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <button wire:click="deleteExpense({{ $exp->id }})" 
                                            wire:confirm="کیا آپ اس انٹری کو ڈیلیٹ کرنا چاہتے ہیں؟" 
                                            class="inline-flex items-center gap-1 bg-rose-600/10 hover:bg-rose-600/20 text-rose-400 border border-rose-500/20 px-3 py-1.5 rounded-xl text-xs font-bold transition-all duration-200">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <p class="text-base font-semibold">Abhi tak koi akhrajat darj nahi hue.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>