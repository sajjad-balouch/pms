<div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 bg-gray-950 text-gray-100 min-h-screen">
    @php
        $input = 'w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-rose-500 focus:ring-1 focus:ring-rose-500 placeholder-gray-600 transition-colors';
        $label = 'block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1';
    @endphp

    <!-- Top Navigation Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center bg-gray-900/80 backdrop-blur-md p-6 rounded-2xl border border-gray-800/80 shadow-2xl gap-4">
        <div>
            <h2 class="text-2xl font-black text-white tracking-tight flex items-center gap-2">
                <span>🏢</span> Town Expenses{{ $town ? ': ' . $town->name : '' }}
            </h2>
            <p class="text-xs text-gray-400 mt-1 font-medium" lang="ur" dir="rtl">اخراجات اور بلز کی تفصیلات</p>
        </div>
        <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
            @if ($towns->count() > 1)
                <select wire:model.live="townId" class="bg-gray-950 border border-gray-700 text-gray-200 rounded-xl px-3 py-2 text-sm focus:border-rose-500 focus:ring-rose-500">
                    @foreach ($towns as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>
            @endif
            <a href="{{ route('town_owner.dashboard') }}" wire:navigate class="inline-flex items-center gap-2 bg-gray-800 hover:bg-gray-700 text-gray-300 border border-gray-700/80 px-4 py-2 rounded-xl text-xs font-semibold transition-all duration-200">
                <span>←</span> Dashboard
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session('success'))
        <div wire:key="flash-success-{{ md5(session('success')) }}" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition
             class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-5 py-3.5 rounded-xl text-sm font-medium flex items-center justify-between">
            <div class="flex items-center gap-3"><span>✅</span><span>{{ session('success') }}</span></div>
            <button type="button" @click="show = false" class="text-emerald-400/60 hover:text-emerald-400">&times;</button>
        </div>
    @endif

    @if (! $town)
        <div class="bg-gray-900/80 p-10 rounded-2xl border border-gray-800/80 text-center">
            <p class="text-lg font-bold text-white">Pehle koi housing scheme banayein</p>
            <p class="text-sm text-gray-400 mt-1">Akhrajaat kisi scheme ke saath darj hote hain.</p>
            <a href="{{ route('town_owner.towns') }}" wire:navigate class="inline-block mt-4 text-indigo-400 font-bold hover:underline">+ Add New Scheme</a>
        </div>
    @else
        <!-- Summary Cards -->
        @php $change = $lastMonth > 0 ? round(($thisMonth - $lastMonth) / $lastMonth * 100) : null; @endphp
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-gray-900/60 p-5 rounded-2xl border border-gray-800/80 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1 h-full bg-rose-500"></div>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400">This Month</p>
                <p class="text-2xl font-black text-rose-400 mt-1">Rs. {{ number_format($thisMonth) }}</p>
                @if (! is_null($change))
                    <p class="text-[11px] mt-1 {{ $change > 0 ? 'text-rose-400' : 'text-emerald-400' }}">
                        {{ $change > 0 ? '▲' : '▼' }} {{ abs($change) }}% vs last month
                    </p>
                @endif
            </div>
            <div class="bg-gray-900/60 p-5 rounded-2xl border border-gray-800/80 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1 h-full bg-orange-500"></div>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Last Month</p>
                <p class="text-2xl font-black text-orange-400 mt-1">Rs. {{ number_format($lastMonth) }}</p>
            </div>
            <div class="bg-gray-900/60 p-5 rounded-2xl border border-gray-800/80 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1 h-full bg-indigo-500"></div>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400">{{ $periodLabel }}{{ $categoryFilter ? ' · ' . $categoryFilter : '' }}</p>
                <p class="text-2xl font-black text-indigo-400 mt-1">Rs. {{ number_format($filteredTotal) }}</p>
                <p class="text-[11px] text-gray-500 mt-1">{{ number_format($filteredCount) }} entries</p>
            </div>
            <div class="bg-gray-900/60 p-5 rounded-2xl border border-gray-800/80 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1 h-full bg-gray-500"></div>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400">All Time Total</p>
                <p class="text-2xl font-black text-white mt-1">Rs. {{ number_format($allTime) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="space-y-8">
                <!-- Expense Entry Form Card -->
                <div x-on:expense-form-opened.window="$el.scrollIntoView({ behavior: 'smooth', block: 'start' })"
                     class="bg-gray-900/80 backdrop-blur-md p-6 rounded-2xl border {{ $editingExpenseId ? 'border-amber-500/40' : 'border-gray-800/80' }} shadow-2xl space-y-5 h-fit scroll-mt-6">
                    <div class="border-b border-gray-800/80 pb-4 flex items-start justify-between gap-3">
                        <div>
                            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                <span>{{ $editingExpenseId ? '✏️' : '💸' }}</span> {{ $editingExpenseId ? 'Edit Expense' : 'Add New Expense' }}
                            </h3>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $editingExpenseId ? 'Entry ki tafseel badlein.' : 'Naya akhrajat darj karein.' }}</p>
                        </div>
                        @if ($editingExpenseId || $title)
                            <button type="button" wire:click="cancelEdit" class="text-xs text-rose-400 hover:text-rose-300 font-semibold bg-rose-500/10 border border-rose-500/20 px-3 py-1.5 rounded-xl">✕ {{ $editingExpenseId ? 'Cancel' : 'Clear' }}</button>
                        @endif
                    </div>

                    <form wire:submit="saveExpense" class="space-y-4">
                        <div>
                            <label class="{{ $label }}">Expense Title *</label>
                            <input type="text" wire:model="title" placeholder="e.g. Electric Bill, Office Tea" class="{{ $input }}" required />
                            @error('title') <span class="text-xs text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="{{ $label }}">Category *</label>
                                <select wire:model="category" class="{{ $input }}">
                                    @foreach ($categories as $key => $text)
                                        <option value="{{ $key }}">{{ $text }}</option>
                                    @endforeach
                                </select>
                                @error('category') <span class="text-xs text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="{{ $label }}">Amount (Rs.) *</label>
                                <input type="number" min="1" step="1" wire:model="amount" placeholder="5000" class="{{ $input }}" required />
                                @error('amount') <span class="text-xs text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="{{ $label }}">Expense Date *</label>
                                <input type="date" max="{{ now()->toDateString() }}" wire:model="expense_date" class="{{ $input }} [color-scheme:dark]" required />
                                @error('expense_date') <span class="text-xs text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="{{ $label }}">Paid To</label>
                                <input type="text" wire:model="paid_to" placeholder="Vendor / Person" class="{{ $input }}" />
                                @error('paid_to') <span class="text-xs text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="{{ $label }}">Details</label>
                            <textarea wire:model="details" rows="2" placeholder="Optional — bill number, quantity waghera" class="{{ $input }}"></textarea>
                            @error('details') <span class="text-xs text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>

                        <!-- Receipt / Bill -->
                        <div class="space-y-2">
                            <label class="{{ $label }}">Bill / Receipt (Photo or PDF)</label>

                            @if ($editingExpense?->receipt_path && ! $receipt)
                                <div class="flex items-center justify-between bg-gray-950/60 border border-gray-800 rounded-xl px-3 py-2 text-xs">
                                    <a href="{{ $receiptUrl($editingExpense->receipt_path) }}" target="_blank" class="text-sky-400 hover:text-sky-300 font-semibold">📎 Current receipt dekhein</a>
                                    <button type="button" wire:click="removeReceipt" wire:confirm="Receipt hata dein?" class="text-rose-400 hover:text-rose-300 font-bold">Remove</button>
                                </div>
                            @endif

                            @if ($receipt && ! $errors->has('receipt') && $receipt->isPreviewable())
                                <div class="relative w-32 rounded-lg overflow-hidden border border-rose-500/40">
                                    <img src="{{ $receipt->temporaryUrl() }}" alt="Receipt preview" class="w-full h-24 object-cover">
                                    <button type="button" wire:click="$set('receipt', null)" class="absolute top-1 right-1 h-5 w-5 bg-black/70 hover:bg-rose-600 text-white rounded-full text-[10px]">✕</button>
                                </div>
                            @elseif ($receipt && ! $errors->has('receipt'))
                                <p class="text-xs text-gray-400">📄 {{ $receipt->getClientOriginalName() }}</p>
                            @endif

                            <input type="file" wire:model="receipt" wire:key="receipt-input-{{ $uploadIteration }}" accept="image/jpeg,image/png,image/webp,application/pdf"
                                class="w-full text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-rose-500/10 file:text-rose-400 hover:file:bg-rose-500/20 cursor-pointer">
                            <span wire:loading wire:target="receipt" class="text-xs text-rose-400 animate-pulse">Uploading...</span>
                            @error('receipt') <span class="text-xs text-rose-400 block font-medium">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit" wire:loading.attr="disabled" wire:target="saveExpense,receipt"
                            class="w-full inline-flex items-center justify-center gap-2 {{ $editingExpenseId ? 'bg-amber-600/20 hover:bg-amber-600/30 text-amber-300 border-amber-500/30' : 'bg-rose-600/20 hover:bg-rose-600/30 text-rose-300 border-rose-500/30' }} border font-bold py-3 px-4 rounded-xl text-sm transition-all duration-200 disabled:opacity-50">
                            <span wire:loading.remove wire:target="saveExpense">{{ $editingExpenseId ? 'Update Expense' : '+ Add Expense Record' }}</span>
                            <span wire:loading wire:target="saveExpense" class="animate-pulse">Saving Record...</span>
                        </button>
                    </form>
                </div>

                <!-- Category Breakdown -->
                <div class="bg-gray-900/80 p-6 rounded-2xl border border-gray-800/80 shadow-2xl">
                    <h3 class="font-extrabold text-white flex items-center gap-2"><span>🧾</span> By Category <span class="text-xs font-normal text-gray-500">({{ $periodLabel }})</span></h3>
                    @php $breakdownMax = max(1, collect($breakdown)->max('total')); @endphp
                    <div class="mt-4 space-y-3">
                        @forelse ($breakdown as $row)
                            <button type="button" wire:click="$set('categoryFilter', '{{ $categoryFilter === $row['key'] ? '' : $row['key'] }}')" class="w-full text-left group">
                                <div class="flex justify-between text-sm">
                                    <span class="font-semibold truncate pr-2 {{ $categoryFilter === $row['key'] ? 'text-rose-300' : 'text-gray-300 group-hover:text-white' }}">{{ $row['label'] }}</span>
                                    <span class="text-gray-400 whitespace-nowrap">Rs. {{ number_format($row['total']) }}</span>
                                </div>
                                <div class="mt-1 h-1.5 w-full bg-gray-800 rounded-full overflow-hidden">
                                    <div class="h-full bg-rose-500 rounded-full" style="width: {{ round($row['total'] / $breakdownMax * 100) }}%"></div>
                                </div>
                                <span class="text-[11px] text-gray-500">{{ $row['entries'] }} entries · {{ $filteredTotal > 0 ? round($row['total'] / $filteredTotal * 100) : 0 }}%</span>
                            </button>
                        @empty
                            <p class="text-sm text-gray-500 py-4 text-center">Is period mein koi kharcha nahi.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Expenses History Table Card -->
            <div class="lg:col-span-2 bg-gray-900/80 rounded-2xl border border-gray-800/80 shadow-2xl overflow-hidden h-fit">
                <div class="p-6 border-b border-gray-800/80 space-y-4">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                        <h3 class="text-lg font-extrabold text-white flex items-center gap-2"><span>📜</span> Expenses History</h3>
                        <button type="button" wire:click="exportCsv" wire:loading.attr="disabled" wire:target="exportCsv"
                            class="inline-flex items-center gap-2 bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-300 border border-emerald-500/30 px-4 py-2 rounded-xl text-xs font-bold disabled:opacity-50">⬇️ Export CSV</button>
                    </div>

                    <!-- Filters -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <select wire:model.live="period" class="bg-gray-950 border border-gray-800 text-gray-200 rounded-xl px-3 py-2 text-sm">
                            <option value="this_month">This Month</option>
                            <option value="last_month">Last Month</option>
                            <option value="this_year">This Year</option>
                            <option value="all">All Time</option>
                            <option value="custom">Custom Range</option>
                        </select>
                        <select wire:model.live="categoryFilter" class="bg-gray-950 border border-gray-800 text-gray-200 rounded-xl px-3 py-2 text-sm">
                            <option value="">All Categories</option>
                            @foreach ($categories as $key => $text)
                                <option value="{{ $key }}">{{ $text }}</option>
                            @endforeach
                        </select>
                        <div class="relative sm:col-span-2 flex gap-2">
                            <div class="relative flex-1">
                                <input type="search" wire:model.live.debounce.300ms="search" placeholder="Title, paid to, details..."
                                    class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl pl-9 pr-4 py-2 text-sm focus:border-rose-500 focus:ring-1 focus:ring-rose-500 placeholder-gray-600">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">🔍</span>
                            </div>
                            @if ($hasFilters)
                                <button type="button" wire:click="clearFilters" title="Reset filters" class="px-3 bg-gray-800 hover:bg-gray-700 text-gray-300 border border-gray-700 rounded-xl text-sm">✕</button>
                            @endif
                        </div>
                        @if ($period === 'custom')
                            <input type="date" wire:model.live="from" class="bg-gray-950 border border-gray-800 text-gray-200 rounded-xl px-3 py-2 text-sm [color-scheme:dark]">
                            <input type="date" wire:model.live="to" class="bg-gray-950 border border-gray-800 text-gray-200 rounded-xl px-3 py-2 text-sm [color-scheme:dark]">
                        @endif
                    </div>
                </div>

                <div class="overflow-x-auto" wire:loading.class="opacity-60" wire:target="period,from,to,categoryFilter,search,clearFilters,gotoPage,nextPage,previousPage">
                    <table class="min-w-full divide-y divide-gray-800/80 text-sm">
                        <thead class="bg-gray-950/50 text-gray-400 text-xs uppercase tracking-wider">
                            <tr>
                                <th scope="col" class="px-5 py-4 text-left font-bold">Date</th>
                                <th scope="col" class="px-5 py-4 text-left font-bold">Title</th>
                                <th scope="col" class="px-5 py-4 text-left font-bold">Category</th>
                                <th scope="col" class="px-5 py-4 text-right font-bold">Amount</th>
                                <th scope="col" class="px-5 py-4 text-right font-bold">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800/60 text-gray-300">
                            @forelse ($expenses as $exp)
                                <tr wire:key="exp-{{ $exp->id }}" class="hover:bg-gray-800/40 transition-colors {{ $editingExpenseId == $exp->id ? 'bg-amber-500/10' : '' }}">
                                    <td class="px-5 py-4 text-gray-400 whitespace-nowrap">{{ \Carbon\Carbon::parse($exp->expense_date)->format('d M, Y') }}</td>
                                    <td class="px-5 py-4 font-bold text-white">
                                        {{ $exp->title }}
                                        @if ($exp->paid_to)
                                            <span class="block text-xs font-normal text-gray-500 mt-0.5">Paid to: {{ $exp->paid_to }}</span>
                                        @endif
                                        @if ($exp->details)
                                            <span class="block text-xs font-normal text-gray-500 mt-0.5 max-w-xs truncate" title="{{ $exp->details }}">{{ $exp->details }}</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-800 text-gray-300 border border-gray-700/80">{{ $categoryLabel($exp->category) }}</span>
                                    </td>
                                    <td class="px-5 py-4 font-black text-rose-400 whitespace-nowrap text-right">Rs. {{ number_format($exp->amount) }}</td>
                                    <td class="px-5 py-4 text-right whitespace-nowrap space-x-1">
                                        @if ($exp->receipt_path)
                                            <a href="{{ $receiptUrl($exp->receipt_path) }}" target="_blank" title="Receipt dekhein" class="inline-flex items-center bg-sky-600/10 hover:bg-sky-600/20 text-sky-300 border border-sky-500/20 px-2.5 py-1.5 rounded-xl text-xs font-bold">📎</a>
                                        @endif
                                        <button type="button" wire:click="duplicateExpense({{ $exp->id }})" title="Dobara darj karein (har mahine ka bill)" class="inline-flex items-center bg-gray-800 hover:bg-gray-700 text-gray-300 border border-gray-700 px-2.5 py-1.5 rounded-xl text-xs font-bold">⧉</button>
                                        <button type="button" wire:click="editExpense({{ $exp->id }})" title="Edit" class="inline-flex items-center bg-amber-600/10 hover:bg-amber-600/20 text-amber-300 border border-amber-500/20 px-2.5 py-1.5 rounded-xl text-xs font-bold">✏️</button>
                                        <button type="button" wire:click="deleteExpense({{ $exp->id }})" wire:confirm="کیا آپ اس انٹری کو ڈیلیٹ کرنا چاہتے ہیں؟" title="Delete"
                                            class="inline-flex items-center bg-rose-600/10 hover:bg-rose-600/20 text-rose-400 border border-rose-500/20 px-2.5 py-1.5 rounded-xl text-xs font-bold">🗑️</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                        @if ($hasFilters)
                                            <p class="text-base font-semibold">In filters ke mutabiq koi kharcha nahi mila.</p>
                                            <button type="button" wire:click="clearFilters" class="text-rose-400 font-bold hover:underline mt-1">Reset filters</button>
                                        @else
                                            <p class="text-base font-semibold">Is mahine abhi tak koi akhrajat darj nahi hue.</p>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if ($expenses->count())
                            <tfoot class="bg-gray-950/50">
                                <tr>
                                    <td colspan="3" class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Total ({{ $periodLabel }})</td>
                                    <td class="px-5 py-3 text-right font-black text-rose-400 whitespace-nowrap">Rs. {{ number_format($filteredTotal) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>

                @if ($expenses->hasPages())
                    <div class="p-4 border-t border-gray-800/80 bg-gray-950/30">
                        {{ $expenses->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif

</div>
