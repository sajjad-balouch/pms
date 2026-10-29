<div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 bg-gray-950 text-gray-100 min-h-screen">
    @php
        $input = 'w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder-gray-600';
        $label = 'block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-1';
        $filters = ['all' => 'All', 'overdue' => '⚠️ Late payments', 'no_cnic' => '🪪 CNIC missing', 'no_plot' => 'No active plot'];
    @endphp

    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center bg-gray-900/80 backdrop-blur-md p-6 rounded-2xl border border-gray-800/80 shadow-2xl gap-4">
        <div>
            <h2 class="text-2xl font-black text-white tracking-tight flex items-center gap-2"><span>👥</span> Customers</h2>
            <p class="text-xs text-gray-400 mt-1 font-medium">Har kharidar ka profile, plots, baqaya aur documents (CNIC, photo, forms) ek jagah.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button type="button" wire:click="newCustomer" class="inline-flex items-center gap-2 bg-indigo-600/20 hover:bg-indigo-600/30 text-indigo-300 border border-indigo-500/30 px-4 py-2 rounded-xl text-xs font-bold">+ New Customer</button>
            <a href="{{ route('town_owner.dashboard') }}" wire:navigate class="inline-flex items-center gap-2 bg-gray-800 hover:bg-gray-700 text-gray-300 border border-gray-700/80 px-4 py-2 rounded-xl text-xs font-semibold">← Dashboard</a>
        </div>
    </div>

    @if (session('success'))
        <div wire:key="flash-s-{{ md5(session('success')) }}" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition
             class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-5 py-3.5 rounded-xl text-sm font-medium">✅ {{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div wire:key="flash-e-{{ md5(session('error')) }}" class="bg-rose-500/10 border border-rose-500/30 text-rose-400 px-5 py-3.5 rounded-xl text-sm font-medium">⛔ {{ session('error') }}</div>
    @endif

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-gray-900/60 p-5 rounded-2xl border border-gray-800/80 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-indigo-500"></div>
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Customers</p>
            <p class="text-3xl font-black text-white mt-1">{{ number_format($stats['total']) }}</p>
        </div>
        <div class="bg-gray-900/60 p-5 rounded-2xl border border-gray-800/80 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-emerald-500"></div>
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">With Active Plots</p>
            <p class="text-3xl font-black text-emerald-400 mt-1">{{ number_format($stats['with_plots']) }}</p>
        </div>
        <button type="button" wire:click="$set('filter', 'no_cnic')" class="text-left bg-gray-900/60 p-5 rounded-2xl border border-gray-800/80 hover:border-amber-500/40 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-amber-500"></div>
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">CNIC Copies Missing</p>
            <p class="text-3xl font-black text-amber-400 mt-1">{{ number_format($stats['missing_cnic']) }}</p>
            <p class="text-[11px] text-gray-500">Click karke list dekhein</p>
        </button>
    </div>

    <!-- List -->
    <div class="bg-gray-900/80 rounded-2xl border border-gray-800/80 shadow-2xl overflow-hidden">
        <div class="p-5 border-b border-gray-800/80 flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
            <div class="flex flex-wrap gap-2">
                @foreach ($filters as $key => $text)
                    <button type="button" wire:click="$set('filter', '{{ $key }}')"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold border {{ $filter === $key ? 'bg-indigo-600/30 text-indigo-200 border-indigo-500/50' : 'bg-gray-950 text-gray-400 border-gray-800 hover:text-white' }}">{{ $text }}</button>
                @endforeach
            </div>
            <div class="relative w-full md:w-72">
                <input type="search" wire:model.live.debounce.300ms="search" placeholder="Naam, CNIC, phone..."
                    class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl pl-9 pr-4 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder-gray-600">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">🔍</span>
            </div>
        </div>

        <div class="overflow-x-auto" wire:loading.class="opacity-60" wire:target="search,filter,gotoPage,nextPage,previousPage">
            <table class="min-w-full divide-y divide-gray-800/80 text-sm">
                <thead class="bg-gray-950/50 text-gray-400 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3 text-left font-bold">Customer</th>
                        <th class="px-5 py-3 text-left font-bold">CNIC / Phone</th>
                        <th class="px-5 py-3 text-left font-bold">Plots</th>
                        <th class="px-5 py-3 text-right font-bold">Paid</th>
                        <th class="px-5 py-3 text-right font-bold">Remaining</th>
                        <th class="px-5 py-3 text-left font-bold">Documents</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60 text-gray-300">
                    @forelse ($customers as $c)
                        <tr wire:key="cust-{{ $c->id }}" class="hover:bg-gray-800/40 cursor-pointer" wire:click="openProfile({{ $c->id }})">
                            <td class="px-5 py-3">
                                <p class="font-bold text-white">{{ $c->name }}</p>
                                @if ($c->father_name)<p class="text-xs text-gray-500">S/O {{ $c->father_name }}</p>@endif
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-xs">
                                <p class="text-gray-300">{{ $c->cnic ?: '—' }}</p>
                                <p class="text-gray-500">{{ $c->phone }}</p>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <span class="font-bold {{ $c->active_plots ? 'text-indigo-400' : 'text-gray-600' }}">{{ $c->active_plots }}</span>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-right text-emerald-400 font-semibold">Rs. {{ number_format($c->paid_total) }}</td>
                            <td class="px-5 py-3 whitespace-nowrap text-right">
                                <span class="font-semibold text-amber-400">Rs. {{ number_format($c->remaining_total) }}</span>
                                @if ($c->overdue_total > 0)
                                    <span class="block text-[11px] text-red-400">Late: Rs. {{ number_format($c->overdue_total) }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                @if ($c->cnic_docs_count >= 2)
                                    <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">🪪 CNIC ✓</span>
                                @else
                                    <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20">🪪 Missing</span>
                                @endif
                                <span class="text-[11px] text-gray-500 ml-1">{{ $c->documents_count }} files</span>
                            </td>
                            <td class="px-5 py-3 text-right text-gray-500">›</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            {{ trim($search) !== '' || $filter !== 'all' ? 'Koi customer nahi mila.' : 'Abhi koi customer nahi. Plot book karte hi customer yahan aa jayega.' }}
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($customers->hasPages())
            <div class="p-4 border-t border-gray-800/80 bg-gray-950/30">{{ $customers->links() }}</div>
        @endif
    </div>

    <!-- Profile / New Customer Modal -->
    @if ($profile || $profileId === 'new')
        <div class="fixed inset-0 z-50 flex items-start justify-center p-4 overflow-y-auto" x-data x-on:keydown.escape.window="$wire.closeProfile()">
            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" wire:click="closeProfile"></div>
            <div class="relative w-full max-w-3xl bg-gray-900 border border-gray-800 rounded-2xl shadow-2xl my-8">
                @php $cust = $profile['customer'] ?? null; @endphp

                <!-- Header -->
                <div class="p-6 border-b border-gray-800 flex items-start justify-between gap-4">
                    <div class="flex items-center gap-4 min-w-0">
                        <div class="h-16 w-16 flex-shrink-0 rounded-2xl overflow-hidden bg-gray-800 border border-gray-700 flex items-center justify-center text-2xl">
                            @if ($profile['photo'] ?? null)
                                <img src="{{ route('town_owner.customer.document', $profile['photo']->id) }}" alt="Customer photo" class="h-full w-full object-cover">
                            @else
                                👤
                            @endif
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-xl font-black text-white truncate">{{ $cust?->name ?? 'New Customer' }}</h3>
                            @if ($cust)
                                <p class="text-sm text-gray-400">{{ collect([$cust->father_name ? 'S/O ' . $cust->father_name : null, $cust->cnic])->filter()->implode(' · ') }}</p>
                                <p class="text-sm text-gray-400">
                                    <a href="tel:{{ $cust->phone }}" class="text-sky-400 hover:text-sky-300">{{ $cust->phone }}</a>
                                    @if ($profile['whatsapp'])
                                        · <a href="{{ $profile['whatsapp'] }}" target="_blank" rel="noopener" class="text-green-400 hover:text-green-300">WhatsApp</a>
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>
                    <button type="button" wire:click="closeProfile" class="h-8 w-8 flex-shrink-0 rounded-full bg-gray-800 hover:bg-gray-700 text-gray-300">✕</button>
                </div>

                <div class="p-6 space-y-6">
                    @if ($editing)
                        <!-- Edit / New form -->
                        <form wire:submit="saveCustomer" class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="{{ $label }}">Full Name *</label>
                                    <input type="text" wire:model="c_name" class="{{ $input }}">
                                    @error('c_name') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="{{ $label }}">Father / Husband Name</label>
                                    <input type="text" wire:model="c_father_name" class="{{ $input }}">
                                    @error('c_father_name') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="{{ $label }}">CNIC</label>
                                    <input type="text" wire:model="c_cnic" maxlength="15" placeholder="35202-1234567-1" class="{{ $input }}">
                                    @error('c_cnic') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="{{ $label }}">Phone (WhatsApp) *</label>
                                    <input type="tel" wire:model="c_phone" placeholder="0300-1234567" class="{{ $input }}">
                                    @error('c_phone') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="{{ $label }}">Address</label>
                                    <input type="text" wire:model="c_address" class="{{ $input }}">
                                    @error('c_address') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            @if ($cust)
                                <p class="text-xs text-gray-500">Naam ya phone badalne par baqi (unpaid) qiston, reminders aur receipts mein bhi naya naam/phone aa jayega.</p>
                            @endif
                            <div class="flex justify-end gap-3">
                                <button type="button" wire:click="cancelEdit" class="bg-gray-800 hover:bg-gray-700 text-gray-300 border border-gray-700 font-bold py-2 px-5 rounded-xl text-sm">Cancel</button>
                                <button type="submit" wire:loading.attr="disabled" wire:target="saveCustomer" class="bg-indigo-600/20 hover:bg-indigo-600/30 text-indigo-300 border border-indigo-500/30 font-bold py-2 px-6 rounded-xl text-sm disabled:opacity-50">Save</button>
                            </div>
                        </form>
                    @elseif ($cust)
                        <div class="flex flex-wrap gap-2">
                            <button type="button" wire:click="startEdit" class="bg-amber-600/20 hover:bg-amber-600/30 text-amber-300 border border-amber-500/30 px-4 py-2 rounded-xl text-xs font-bold">✏️ Edit Details</button>
                            @if ($profile['bookings']->isEmpty())
                                <button type="button" wire:click="deleteCustomer" wire:confirm="{{ $cust->name }} aur un ke documents delete karein?" class="bg-rose-600/10 hover:bg-rose-600/20 text-rose-300 border border-rose-500/20 px-4 py-2 rounded-xl text-xs font-bold">🗑️ Delete</button>
                            @endif
                        </div>
                        @if ($cust->address)
                            <p class="text-sm text-gray-400">📍 {{ $cust->address }}</p>
                        @endif
                    @endif

                    @if ($cust && ! $editing)
                        <!-- Plots -->
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-300 mb-3">Plots</h4>
                            <div class="space-y-3">
                                @forelse ($profile['bookings'] as $b)
                                    <div wire:key="bk-{{ $b->id }}" class="bg-gray-950/60 border {{ $b->status === 'active' ? 'border-gray-800' : 'border-gray-800/50 opacity-60' }} rounded-xl p-4">
                                        <div class="flex flex-wrap justify-between gap-3">
                                            <div>
                                                <p class="font-bold text-white">{{ $b->plot?->town?->name }} · Plot #{{ $b->plot?->plot_number }} <span class="text-xs text-gray-500">{{ $b->plot?->block_name ?? 'Main' }} · {{ $b->plot?->size }}</span></p>
                                                <p class="text-xs text-gray-500">{{ $b->allotmentNumber() }} · Booked {{ $b->booking_date?->format('d M Y') }}
                                                    @if ($b->status !== 'active') · <span class="text-rose-400 font-bold">{{ ucfirst($b->status) }}</span>@endif
                                                </p>
                                            </div>
                                            @if ($b->status === 'active')
                                                <div class="text-right text-xs">
                                                    <p class="text-emerald-400 font-bold">Paid Rs. {{ number_format($b->paid) }}</p>
                                                    <p class="text-amber-400">Baqi Rs. {{ number_format($b->remaining) }}</p>
                                                    @if ($b->overdue > 0)<p class="text-red-400">Late Rs. {{ number_format($b->overdue) }}</p>@endif
                                                </div>
                                            @endif
                                        </div>
                                        @if ($b->status === 'active' && $b->plot)
                                            <div class="flex flex-wrap gap-2 mt-3">
                                                <a href="{{ route('town_owner.installments', $b->plot_id) }}" wire:navigate class="bg-indigo-600/20 hover:bg-indigo-600/30 text-indigo-300 border border-indigo-500/30 px-3 py-1.5 rounded-xl text-xs font-bold">💳 Installments</a>
                                                <a href="{{ route('town_owner.allotment.letter', $b->id) }}" target="_blank" class="bg-amber-600/20 hover:bg-amber-600/30 text-amber-300 border border-amber-500/30 px-3 py-1.5 rounded-xl text-xs font-bold">📄 Allotment Letter</a>
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500">Is customer ki koi booking nahi.</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Documents -->
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-gray-300">Documents 🔒</h4>
                                @if ($profile['missing']->isNotEmpty())
                                    <span class="text-[11px] text-amber-400">Missing: {{ $profile['missing']->map(fn ($t) => $docTypes[$t])->implode(', ') }}</span>
                                @endif
                            </div>

                            @if ($profile['documents']->isNotEmpty())
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
                                    @foreach ($profile['documents'] as $doc)
                                        <div wire:key="doc-{{ $doc->id }}" class="group relative bg-gray-950/60 border border-gray-800 rounded-xl overflow-hidden">
                                            <a href="{{ route('town_owner.customer.document', $doc->id) }}" target="_blank" class="block aspect-[4/3] bg-gray-900 flex items-center justify-center">
                                                @if ($doc->isImage())
                                                    <img src="{{ route('town_owner.customer.document', $doc->id) }}" alt="{{ $doc->label() }}" loading="lazy" class="h-full w-full object-cover">
                                                @else
                                                    <span class="text-3xl">📄</span>
                                                @endif
                                            </a>
                                            <div class="px-2 py-1.5">
                                                <p class="text-[11px] font-bold text-gray-300 truncate">{{ $doc->label() }}</p>
                                                <p class="text-[10px] text-gray-500">{{ $doc->created_at->format('d M Y') }}</p>
                                            </div>
                                            <button type="button" wire:click="deleteDocument({{ $doc->id }})" wire:confirm="Ye document delete karein?" class="absolute top-1 right-1 h-6 w-6 rounded-full bg-black/70 hover:bg-rose-600 text-white text-[10px] opacity-0 group-hover:opacity-100 transition-opacity">✕</button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <form wire:submit="uploadDocument" class="bg-gray-950/60 border border-gray-800 rounded-xl p-4 grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                                <div>
                                    <label class="{{ $label }}">Type</label>
                                    <select wire:model.live="doc_type" class="{{ $input }}">
                                        @foreach ($docTypes as $k => $v)
                                            <option value="{{ $k }}">{{ $v }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if ($doc_type === 'other')
                                    <div>
                                        <label class="{{ $label }}">Title</label>
                                        <input type="text" wire:model="doc_title" placeholder="e.g. Transfer deed" class="{{ $input }}">
                                    </div>
                                @endif
                                <div class="{{ $doc_type === 'other' ? '' : 'sm:col-span-2' }}">
                                    <label class="{{ $label }}">File (photo / PDF, max 5MB)</label>
                                    <input type="file" wire:model="doc_file" wire:key="doc-input-{{ $uploadIteration }}" accept="image/jpeg,image/png,image/webp,application/pdf" capture="environment"
                                        class="w-full text-xs text-gray-400 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-500/10 file:text-indigo-400 hover:file:bg-indigo-500/20 cursor-pointer">
                                    @error('doc_file') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div class="sm:col-span-3 flex items-center justify-between gap-3">
                                    <span wire:loading wire:target="doc_file" class="text-xs text-indigo-400 animate-pulse">Uploading...</span>
                                    <span class="text-[11px] text-gray-500">CNIC / photo dobara upload karne par purani file ki jagah le leti hai.</span>
                                    <button type="submit" wire:loading.attr="disabled" wire:target="uploadDocument,doc_file" class="bg-indigo-600/20 hover:bg-indigo-600/30 text-indigo-300 border border-indigo-500/30 font-bold py-2 px-5 rounded-xl text-xs disabled:opacity-50">⬆ Upload</button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
