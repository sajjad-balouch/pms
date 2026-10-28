{{-- Used inside plot-booking-manager (book + transfer). Expects the component's customer properties. --}}
<div class="space-y-3">
    <div class="flex items-center justify-between gap-3">
        <h4 class="text-xs font-bold uppercase tracking-wider text-gray-300">{{ $title ?? 'Customer' }}</h4>
        <div class="inline-flex bg-gray-950 border border-gray-800 rounded-xl p-1 text-[11px] font-bold">
            <button type="button" wire:click="setCustomerMode('new')" class="px-2.5 py-1 rounded-lg {{ $customerMode === 'new' ? 'bg-indigo-600/30 text-indigo-300' : 'text-gray-400 hover:text-white' }}">+ New</button>
            <button type="button" wire:click="setCustomerMode('existing')" class="px-2.5 py-1 rounded-lg {{ $customerMode === 'existing' ? 'bg-indigo-600/30 text-indigo-300' : 'text-gray-400 hover:text-white' }}">Existing</button>
        </div>
    </div>

    @if ($customerMode === 'existing')
        @if ($selectedCustomer)
            <div class="flex items-center justify-between bg-indigo-500/10 border border-indigo-500/30 rounded-xl px-4 py-3">
                <div>
                    <p class="font-bold text-white">{{ $selectedCustomer->name }}</p>
                    <p class="text-xs text-gray-400">{{ collect([$selectedCustomer->cnic, $selectedCustomer->phone])->filter()->implode(' · ') }}</p>
                </div>
                <button type="button" wire:click="$set('selectedCustomerId', null)" class="text-xs text-rose-400 hover:text-rose-300 font-bold">Change</button>
            </div>
        @else
            <input type="search" wire:model.live.debounce.300ms="customerSearch" placeholder="Naam, CNIC ya phone se dhoondhein..."
                class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder-gray-600">
            <div class="max-h-48 overflow-y-auto divide-y divide-gray-800/80 border border-gray-800 rounded-xl">
                @forelse ($customerResults as $c)
                    <button type="button" wire:key="cust-{{ $c->id }}" wire:click="selectCustomer({{ $c->id }})"
                        class="w-full text-left px-4 py-2.5 hover:bg-gray-800 transition-colors">
                        <span class="block text-sm font-semibold text-white">{{ $c->name }}</span>
                        <span class="block text-xs text-gray-500">{{ collect([$c->cnic, $c->phone])->filter()->implode(' · ') }}</span>
                    </button>
                @empty
                    <p class="px-4 py-3 text-xs text-gray-500">Koi customer nahi mila. "+ New" se naya banayein.</p>
                @endforelse
            </div>
        @endif
        @error('selectedCustomerId') <span class="text-rose-400 text-xs block">{{ $message }}</span> @enderror
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-1">Full Name *</label>
                <input type="text" wire:model="c_name" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                @error('c_name') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-1">Father / Husband Name</label>
                <input type="text" wire:model="c_father_name" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                @error('c_father_name') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-1">CNIC</label>
                <input type="text" wire:model="c_cnic" placeholder="35202-1234567-1" maxlength="15" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder-gray-600">
                @error('c_cnic') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-1">Phone (WhatsApp) *</label>
                <input type="tel" wire:model="c_phone" placeholder="0300-1234567" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder-gray-600">
                @error('c_phone') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div class="sm:col-span-2">
                <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-1">Address</label>
                <input type="text" wire:model="c_address" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                @error('c_address') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>
    @endif
</div>
