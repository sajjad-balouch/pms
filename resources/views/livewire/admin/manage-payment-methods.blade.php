<div class="p-6 bg-slate-900 min-h-screen text-slate-100">
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Header -->
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">Payment Methods Management</h1>
                <p class="text-slate-400 text-xs mt-1">Manage official deposit accounts (JazzCash, EasyPaisa, Bank details) for user top-ups.</p>
            </div>
            
            <button type="button" wire:click="openCreateModal" class="bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold px-4 py-2.5 rounded-xl text-xs transition flex items-center gap-2 cursor-pointer shadow-lg shadow-amber-500/20">
                <i class="fa-solid fa-plus"></i> Add Payment Method
            </button>
        </div>

        <!-- Alerts -->
        @if (session()->has('success'))
            <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 p-4 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif

        <!-- Payment Methods Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($methods as $pm)
                <div class="bg-slate-800/40 border {{ $pm->is_active ? 'border-slate-700/80' : 'border-rose-500/30 bg-rose-950/10' }} rounded-3xl p-6 space-y-4 shadow-xl relative flex flex-col justify-between">
                    
                    <div>
                        <div class="flex items-center justify-between gap-2 border-b border-slate-700/60 pb-3">
                            <h3 class="text-lg font-bold text-white">{{ $pm->method_name }}</h3>
                            
                            <button wire:click="toggleStatus({{ $pm->id }})" class="cursor-pointer">
                                @if($pm->is_active)
                                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-bold text-[10px]">Active</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full bg-rose-500/10 text-rose-400 border border-rose-500/20 font-bold text-[10px]">Inactive</span>
                                @endif
                            </button>
                        </div>

                        <div class="mt-4 space-y-2 text-xs">
                            <div>
                                <span class="text-slate-400 block font-semibold">Account Title:</span>
                                <span class="text-amber-400 font-bold text-sm">{{ $pm->account_title }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block font-semibold">Account / IBAN Number:</span>
                                <span class="text-white font-mono bg-slate-900 px-2.5 py-1 rounded-lg border border-slate-800 inline-block mt-0.5">{{ $pm->account_number }}</span>
                            </div>

                            @if($pm->instructions)
                                <div class="pt-2 border-t border-slate-800">
                                    <span class="text-slate-400 block font-semibold mb-0.5">Instructions:</span>
                                    <p class="text-slate-300 text-[11px] leading-relaxed">{{ $pm->instructions }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Card Actions -->
                    <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-800">
                        <button wire:click="openEditModal({{ $pm->id }})" class="bg-amber-500/10 hover:bg-amber-500 text-amber-400 hover:text-slate-950 font-bold px-3 py-1.5 rounded-lg text-[11px] transition">
                            Edit
                        </button>
                        <button wire:click="deleteMethod({{ $pm->id }})" wire:confirm="Are you sure you want to delete this payment method?" class="bg-rose-600/20 hover:bg-rose-600 text-rose-400 hover:text-white font-bold px-3 py-1.5 rounded-lg text-[11px] transition">
                            Delete
                        </button>
                    </div>

                </div>
            @empty
                <div class="col-span-full bg-slate-800/40 border border-slate-800 rounded-3xl p-12 text-center text-slate-500">
                    No payment methods added yet. Click "Add Payment Method" to create one.
                </div>
            @endforelse
        </div>

    </div>

    <!-- Create / Edit Modal -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 bg-black/80 flex items-center justify-center p-4">
            <div class="bg-slate-900 border border-slate-700 p-6 sm:p-8 rounded-3xl max-w-lg w-full space-y-5 relative shadow-2xl">
                
                <button type="button" wire:click="$set('showModal', false)" class="absolute top-5 right-5 text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>

                <div>
                    <h3 class="text-xl font-extrabold text-white">
                        {{ $isEditMode ? 'Edit Payment Method' : 'Add New Payment Method' }}
                    </h3>
                    <p class="text-slate-400 text-xs mt-1">Configure account details visible to users during top-up.</p>
                </div>

                <form wire:submit.prevent="saveMethod" class="space-y-4">
                    
                    <div>
                        <label class="text-xs text-slate-400 font-bold block mb-1">Method Name</label>
                        <input type="text" wire:model="method_name" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-amber-400" placeholder="e.g. JazzCash / EasyPaisa / Meezan Bank">
                        @error('method_name') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-slate-400 font-bold block mb-1">Account Title</label>
                            <input type="text" wire:model="account_title" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-amber-400" placeholder="e.g. Muhammad Ali">
                            @error('account_title') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="text-xs text-slate-400 font-bold block mb-1">Account / Mobile / IBAN No.</label>
                            <input type="text" wire:model="account_number" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-amber-400" placeholder="03001234567 or IBAN">
                            @error('account_number') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="text-xs text-slate-400 font-bold block mb-1">Instructions for User (Optional)</label>
                        <textarea wire:model="instructions" rows="3" class="w-full bg-slate-800 border border-slate-700 rounded-xl p-3 text-xs text-white focus:outline-none focus:border-amber-400" placeholder="e.g. Transfer funds and attach receipt screenshot with TID..."></textarea>
                        @error('instructions') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="is_active" class="text-amber-500 focus:ring-amber-400 rounded">
                            <span class="text-xs font-bold text-white">Active (Visible to users)</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                        <button type="button" wire:click="$set('showModal', false)" class="bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold px-5 py-2.5 rounded-xl text-xs transition">
                            Cancel
                        </button>
                        <button type="submit" class="bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold px-5 py-2.5 rounded-xl text-xs transition shadow-lg shadow-amber-500/20">
                            {{ $isEditMode ? 'Update Method' : 'Save Method' }}
                        </button>
                    </div>

                </form>

            </div>
        </div>
    @endif
</div>