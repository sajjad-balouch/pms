<div class="bg-slate-900 min-h-screen text-slate-100 py-10 px-4 sm:px-6 lg:px-8">
    
    <div class="max-w-7xl mx-auto space-y-8">
        
        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-6 py-4 rounded-2xl flex items-center justify-between shadow-lg">
                <span><i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') }}</span>
            </div>
        @endif

        <!-- User Header Banner -->
        <div class="bg-gradient-to-r from-slate-800 to-slate-950 border border-slate-800 rounded-3xl p-6 sm:p-8 flex flex-wrap items-center justify-between gap-6 shadow-2xl relative overflow-hidden">
            <div class="flex items-center gap-5 z-10">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-amber-500/20 border border-amber-500/30 flex items-center justify-center text-amber-400 text-2xl font-bold uppercase shadow-inner">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-white">{{ $user->name }}</h2>
                    <p class="text-slate-400 text-xs sm:text-sm mt-1">{{ $user->email }}</p>
                </div>
            </div>

            <!-- Quick Wallet Card -->
            <div class="bg-slate-900/90 border border-slate-700/60 p-5 rounded-2xl flex items-center gap-6 shadow-lg z-10">
                <div>
                    <span class="text-xs text-slate-400 block font-semibold uppercase tracking-wider">Approved Wallet Balance</span>
                    <span class="text-2xl font-black text-amber-400">PKR {{ number_format($user->wallet_balance ?? 0) }}</span>
                </div>
                <button type="button" wire:click="openTopUpModal" class="bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold px-4 py-2.5 rounded-xl transition text-xs flex items-center gap-2 shadow-lg shadow-amber-500/20 cursor-pointer">
                    <i class="fa-solid fa-plus"></i> Deposit / Top Up
                </button>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex flex-wrap border-b border-slate-800 gap-6 text-sm font-semibold">
            <button type="button" wire:click="$set('activeTab', 'overview')" class="pb-4 transition border-b-2 {{ $activeTab === 'overview' ? 'border-amber-400 text-amber-400' : 'border-transparent text-slate-400 hover:text-white' }}">
                <i class="fa-solid fa-chart-pie mr-2"></i> Overview
            </button>
            <button type="button" wire:click="$set('activeTab', 'unlocked_contacts')" class="pb-4 transition border-b-2 {{ $activeTab === 'unlocked_contacts' ? 'border-amber-400 text-amber-400' : 'border-transparent text-slate-400 hover:text-white' }}">
                <i class="fa-solid fa-users mr-2"></i> Unlocked Contacts ({{ count($unlockedContacts) }})
            </button>
            <button type="button" wire:click="$set('activeTab', 'unlocked')" class="pb-4 transition border-b-2 {{ $activeTab === 'unlocked' ? 'border-amber-400 text-amber-400' : 'border-transparent text-slate-400 hover:text-white' }}">
                <i class="fa-solid fa-key mr-2"></i> Unlocked Properties ({{ count($unlockedProperties) }})
            </button>
            <button type="button" wire:click="$set('activeTab', 'transactions')" class="pb-4 transition border-b-2 {{ $activeTab === 'transactions' ? 'border-amber-400 text-amber-400' : 'border-transparent text-slate-400 hover:text-white' }}">
                <i class="fa-solid fa-receipt mr-2"></i> Wallet History
            </button>
        </div>

        <!-- TAB CONTENT: UNLOCKED CONTACTS -->
        @if($activeTab === 'overview' || $activeTab === 'unlocked_contacts')
            <div class="space-y-6">
                <h3 class="text-xl font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-address-book text-amber-400"></i> Active Unlocked Contacts (1 Month Validity)
                </h3>

                @if($unlockedContacts->isEmpty())
                    <div class="bg-slate-800/40 border border-slate-800 rounded-3xl p-10 text-center space-y-3">
                        <i class="fa-solid fa-user-slash text-4xl text-slate-600"></i>
                        <p class="text-slate-400 text-sm">You haven't unlocked any Agent or Town Owner contact details yet.</p>
                        <a href="/" class="inline-block bg-slate-800 hover:bg-slate-700 text-amber-400 font-bold px-5 py-2.5 rounded-xl text-xs transition">Explore Agents & Owners</a>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($unlockedContacts as $contact)
                            @php $agent = $contact->unlockedUser; @endphp
                            @if($agent)
                                <div class="bg-slate-800/80 border border-slate-700/80 rounded-2xl p-5 hover:border-amber-500/50 transition duration-300 shadow-xl space-y-4 relative">
                                    
                                    <!-- Header Info -->
                                    <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-12 h-12 rounded-xl bg-amber-500/20 border border-amber-500/30 flex items-center justify-center text-amber-400 font-bold text-lg">
                                                {{ substr($agent->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <h4 class="text-sm font-bold text-white truncate">{{ $agent->name }}</h4>
                                                <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-md {{ $agent->role === 'town_owner' ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : 'bg-blue-500/20 text-blue-400 border border-blue-500/30' }}">
                                                    {{ str_replace('_', ' ', $agent->role) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Contact Info -->
                                    <div class="space-y-2 text-xs">
                                        <div class="flex items-center justify-between text-slate-300 bg-slate-900/60 p-2.5 rounded-xl border border-slate-800">
                                            <span class="text-slate-400 flex items-center gap-2"><i class="fa-solid fa-phone text-amber-400"></i> Phone:</span>
                                            <a href="tel:{{ $agent->phone }}" class="font-bold text-amber-400 hover:underline">{{ $agent->phone ?? 'N/A' }}</a>
                                        </div>

                                        <div class="flex items-center justify-between text-slate-300 bg-slate-900/60 p-2.5 rounded-xl border border-slate-800">
                                            <span class="text-slate-400 flex items-center gap-2"><i class="fa-solid fa-envelope text-amber-400"></i> Email:</span>
                                            <a href="mailto:{{ $agent->email }}" class="font-semibold text-slate-200 hover:underline truncate max-w-[180px]">{{ $agent->email }}</a>
                                        </div>
                                    </div>

                                    <!-- Expiry Footer -->
                                    <div class="pt-2 flex items-center justify-between text-[11px] text-slate-400 border-t border-slate-700/60">
                                        <span>Expires in:</span>
                                        <span class="text-emerald-400 font-bold">
                                            {{ \Carbon\Carbon::parse($contact->expires_at)->diffForHumans() }}
                                        </span>
                                    </div>

                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        <!-- TAB CONTENT: UNLOCKED PROPERTIES -->
        @if($activeTab === 'overview' || $activeTab === 'unlocked')
            <div class="space-y-6">
                <h3 class="text-xl font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-unlock text-amber-400"></i> My Unlocked Properties
                </h3>

                @if($unlockedProperties->isEmpty())
                    <div class="bg-slate-800/40 border border-slate-800 rounded-3xl p-12 text-center space-y-3">
                        <i class="fa-solid fa-folder-open text-4xl text-slate-600"></i>
                        <p class="text-slate-400 text-sm">You haven't unlocked any property contact details yet.</p>
                        <a href="/" class="inline-block bg-slate-800 hover:bg-slate-700 text-amber-400 font-bold px-5 py-2.5 rounded-xl text-xs transition">Browse Marketplace</a>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($unlockedProperties as $prop)
                            <div class="bg-slate-800/80 border border-slate-700/80 rounded-2xl overflow-hidden hover:border-amber-500/50 transition duration-300 shadow-xl space-y-4 p-4">
                                <div class="relative h-44 rounded-xl overflow-hidden bg-slate-950">
                                    @if(!empty($prop->images) && count($prop->images) > 0)
                                        <img src="{{ asset($prop->images[0]) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-600">
                                            <i class="fa-solid fa-image text-3xl"></i>
                                        </div>
                                    @endif
                                    <span class="absolute top-2 left-2 bg-emerald-500 text-slate-950 font-black text-[10px] uppercase px-2.5 py-1 rounded-full shadow">
                                        Unlocked
                                    </span>
                                </div>

                                <div>
                                    <h4 class="text-lg font-bold text-white truncate">{{ $prop->title ?? ucfirst($prop->property_type) }}</h4>
                                    <p class="text-xs text-slate-400 mt-1 truncate"><i class="fa-solid fa-location-dot text-amber-500 mr-1"></i> {{ $prop->location ?? $prop->city }}</p>
                                    <div class="text-amber-400 font-extrabold text-lg mt-2">PKR {{ number_format($prop->price) }}</div>
                                </div>

                                <div class="border-t border-slate-700/60 pt-3 space-y-2">
                                    <span class="text-[11px] text-slate-400 block font-semibold">Agent Info:</span>
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-white font-bold">{{ $prop->agent->name ?? 'Agent' }}</span>
                                        <a href="tel:{{ $prop->agent->phone }}" class="text-amber-400 hover:underline font-bold"><i class="fa-solid fa-phone mr-1"></i> {{ $prop->agent->phone }}</a>
                                    </div>
                                </div>

                                <a href="/properties/{{ $prop->id }}" class="block w-full text-center bg-slate-900 hover:bg-slate-950 text-slate-200 font-semibold text-xs py-2.5 rounded-xl transition">
                                    View Full Listing
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        <!-- TAB CONTENT: TRANSACTIONS -->
        @if($activeTab === 'transactions')
            <div class="bg-slate-800/60 border border-slate-700/60 rounded-3xl p-6 space-y-4 shadow-xl">
                <h3 class="text-lg font-bold text-white">Wallet Transactions & Top-up Status</h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-300">
                        <thead class="bg-slate-900/80 text-slate-400 uppercase font-bold text-[10px] border-b border-slate-700">
                            <tr>
                                <th class="p-3">Type / Method</th>
                                <th class="p-3">Sender Account Details</th>
                                <th class="p-3">Amount</th>
                                <th class="p-3">Status</th>
                                <th class="p-3">Proof / SS</th>
                                <th class="p-3">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @forelse($transactions as $tx)
                                <tr>
                                    <td class="p-3">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $tx->type === 'deposit' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-rose-500/20 text-rose-400' }}">
                                            {{ $tx->type }}
                                        </span>
                                        @if($tx->payment_method)
                                            <span class="block text-[11px] text-slate-400 font-semibold mt-1">{{ $tx->payment_method }}</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-slate-300">
                                        @if($tx->sender_account_title)
                                            <div class="font-bold text-white">{{ $tx->sender_account_title }}</div>
                                            <div class="text-[11px] text-slate-400">{{ $tx->sender_account_number }}</div>
                                        @else
                                            <span class="text-slate-500">-</span>
                                        @endif
                                    </td>
                                    <td class="p-3 font-extrabold text-sm {{ $tx->type === 'deposit' ? 'text-emerald-400' : 'text-rose-400' }}">
                                        {{ $tx->type === 'deposit' ? '+' : '-' }} PKR {{ number_format($tx->amount) }}
                                    </td>
                                    <td class="p-3">
                                        @if($tx->status === 'approved')
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                                <i class="fa-solid fa-circle-check mr-1"></i> Approved
                                            </span>
                                        @elseif($tx->status === 'pending')
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">
                                                <i class="fa-solid fa-clock mr-1"></i> Pending Approval
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">
                                                <i class="fa-solid fa-circle-xmark mr-1"></i> Rejected
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-3">
                                        @if($tx->screenshot)
                                            <a href="{{ asset($tx->screenshot) }}" target="_blank" class="text-amber-400 hover:underline text-xs inline-flex items-center gap-1 font-semibold">
                                                <i class="fa-solid fa-image"></i> View SS
                                            </a>
                                        @else
                                            <span class="text-slate-500">-</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-slate-400 text-[11px]">{{ \Carbon\Carbon::parse($tx->created_at)->format('d M Y, h:i A') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-6 text-center text-slate-500">No transaction records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div>

    <!-- Deposit / Top Up Modal -->
    @if($showTopUpModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md overflow-y-auto">
            <div class="bg-slate-900 border border-slate-700/80 rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-6 shadow-2xl relative my-8">
                
                <button type="button" wire:click="$set('showTopUpModal', false)" class="absolute top-5 right-5 text-slate-400 hover:text-white transition">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>

                <div class="text-center space-y-2">
                    <div class="w-14 h-14 bg-amber-500/20 border border-amber-500/40 text-amber-400 rounded-2xl mx-auto flex items-center justify-center text-xl shadow-inner">
                        <i class="fa-solid fa-building-columns"></i>
                    </div>
                    <h3 class="text-xl font-extrabold text-white">Deposit Funds to Wallet</h3>
                    <p class="text-slate-400 text-xs">Send payment to one of the official accounts below and upload proof.</p>
                </div>

                <form wire:submit.prevent="depositWallet" class="space-y-5">
                    
                    <!-- 1. Select Payment Method -->
                    <div>
                        <label class="text-xs text-slate-400 font-bold block mb-2 uppercase tracking-wider">1. Select Payment Method</label>
                        <div class="grid grid-cols-1 gap-2">
                            @foreach($paymentMethods as $pm)
                                <label for="method-{{ $pm->id }}" 
                                       class="p-3 rounded-xl border {{ $selectedMethodId == $pm->id ? 'border-amber-400 bg-amber-500/10' : 'border-slate-800 bg-slate-800/50 hover:border-slate-700' }} cursor-pointer block transition">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <input type="radio" 
                                                   id="method-{{ $pm->id }}"
                                                   name="payment_method" 
                                                   wire:model.live="selectedMethodId" 
                                                   value="{{ $pm->id }}" 
                                                   class="text-amber-500 focus:ring-amber-400 cursor-pointer">
                                            <div>
                                                <span class="text-sm font-bold text-white block">{{ $pm->method_name }}</span>
                                                <span class="text-xs text-amber-400 font-semibold">{{ $pm->account_title }} - {{ $pm->account_number }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    @if($pm->instructions)
                                        <p class="text-[11px] text-slate-400 mt-2 pl-7 border-t border-slate-700/50 pt-2">{{ $pm->instructions }}</p>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                        @error('selectedMethodId') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- 2. Deposit Amount -->
                    <div>
                        <label class="text-xs text-slate-400 font-bold block mb-1 uppercase tracking-wider">2. Deposit Amount (PKR)</label>
                        <input type="number" wire:model="topUpAmount" class="w-full bg-slate-800/80 border border-slate-700 rounded-xl px-4 py-3 text-white text-sm focus:border-amber-400 focus:outline-none" placeholder="e.g. 1000">
                        @error('topUpAmount') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- 3. Sender Details -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-slate-400 font-bold block mb-1">Your Account Title</label>
                            <input type="text" wire:model="senderAccountTitle" class="w-full bg-slate-800/80 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-xs focus:border-amber-400 focus:outline-none" placeholder="e.g. Ali Raza">
                            @error('senderAccountTitle') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="text-xs text-slate-400 font-bold block mb-1">Your Account / Mobile No.</label>
                            <input type="text" wire:model="senderAccountNumber" class="w-full bg-slate-800/80 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-xs focus:border-amber-400 focus:outline-none" placeholder="03001234567">
                            @error('senderAccountNumber') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- 4. Upload Payment Screenshot -->
                    <div>
                        <label class="text-xs text-slate-400 font-bold block mb-1">Upload Payment Screenshot (SS)</label>
                        <input type="file" wire:model="screenshot" accept="image/*" class="w-full bg-slate-800/80 border border-slate-700 rounded-xl px-3 py-2 text-slate-300 text-xs file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-500 file:text-slate-950 hover:file:bg-amber-400 cursor-pointer">
                        @error('screenshot') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror

                        @if ($screenshot)
                            <div class="mt-2 text-xs text-emerald-400 flex items-center gap-1">
                                <i class="fa-solid fa-image"></i> Image selected ready for submission.
                            </div>
                        @endif
                    </div>

                    <!-- Submit Buttons -->
                    <div class="grid grid-cols-2 gap-3 pt-3 border-t border-slate-800">
                        <button type="button" wire:click="$set('showTopUpModal', false)" class="w-full bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold py-3 rounded-xl text-xs transition">
                            Cancel
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="w-full bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold py-3 rounded-xl text-xs transition shadow-lg shadow-amber-500/20">
                            <span wire:loading.remove>Submit Request</span>
                            <span wire:loading><i class="fa-solid fa-spinner fa-spin"></i> Uploading...</span>
                        </button>
                    </div>

                </form>

            </div>
        </div>
    @endif

</div>