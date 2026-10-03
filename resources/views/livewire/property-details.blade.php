<div class="bg-slate-900 min-h-screen text-slate-100 py-12 px-4 sm:px-6 lg:px-8">
    
    <!-- Flash Messages -->
    @if (session()->has('error'))
        <div class="max-w-7xl mx-auto mb-6 bg-rose-500/10 border border-rose-500/30 text-rose-400 px-6 py-4 rounded-2xl flex items-center justify-between">
            <span><i class="fa-solid fa-circle-exclamation mr-2"></i>{{ session('error') }}</span>
        </div>
    @endif
    @if (session()->has('success'))
        <div class="max-w-7xl mx-auto mb-6 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-6 py-4 rounded-2xl flex items-center justify-between">
            <span><i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') }}</span>
        </div>
    @endif

    <div class="max-w-7xl mx-auto space-y-8">
        
        <!-- Top Bar -->
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-800 pb-6">
            <div>
                <a href="{{url('/')}}" class="text-amber-400 hover:text-amber-300 text-xs font-semibold uppercase tracking-wider inline-flex items-center gap-2 mb-2">
                    <i class="fa-solid fa-arrow-left"></i> Back to Marketplace
                </a>
                <h1 class="text-3xl font-extrabold text-white">
                    {{ $property->title ?? ucfirst($property->property_type) }}
                </h1>
                <p class="text-slate-400 text-sm mt-1">
                    <i class="fa-solid fa-location-dot text-amber-500 mr-1"></i>
                    {{ $property->location ?? $property->city }} {{ $property->town ? '('.$property->town->name.')' : '' }}
                </p>
            </div>
            <div class="text-right">
                <span class="text-xs font-semibold text-slate-400 uppercase block mb-1">Demand Price</span>
                <span class="text-3xl font-black text-amber-400">PKR {{ number_format($property->price ?? 0) }}</span>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Dynamic Image Gallery -->
                <div class="relative h-[380px] sm:h-[450px] rounded-3xl overflow-hidden border border-slate-800 bg-slate-950 shadow-2xl">
                    @if(!empty($property->images) && count($property->images) > 0)
                        <img src="{{ asset('public/'.$property->images[0]) }}" alt="{{ $property->title }}" class="w-full h-full object-cover">
                    @else
                        <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?q=80&w=1073&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="{{ $property->title }}" class="w-full h-full object-cover">
                    @endif

                    <div class="absolute top-4 left-4 flex gap-2">
                        <span class="px-4 py-1.5 rounded-full text-xs font-extrabold uppercase tracking-wider shadow-lg bg-amber-500 text-slate-950">
                            {{ str_replace('_', ' ', $property->purpose) }}
                        </span>
                    </div>
                </div>

                <!-- Attributes Grid -->
                <div class="bg-slate-800/60 border border-slate-700/60 rounded-3xl p-6 grid grid-cols-2 sm:grid-cols-4 gap-6">
                    <div class="space-y-1">
                        <span class="text-xs text-slate-400 block">Property Type</span>
                        <span class="text-base font-bold text-white capitalize">{{ $property->property_type }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs text-slate-400 block">Area Size</span>
                        <span class="text-base font-bold text-amber-400">{{ $property->area_size }} Marla</span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs text-slate-400 block">City</span>
                        <span class="text-base font-bold text-white">{{ $property->city }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs text-slate-400 block">Purpose</span>
                        <span class="text-base font-bold text-emerald-400 capitalize">{{ str_replace('_', ' ', $property->purpose) }}</span>
                    </div>
                </div>

                <!-- Description -->
                <div class="bg-slate-800/60 border border-slate-700/60 rounded-3xl p-6 space-y-3">
                    <h3 class="text-xl font-bold text-white">Description</h3>
                    <p class="text-slate-300 text-sm leading-relaxed">
                        {{ $property->description ?? 'No detailed description provided for this listing.' }}
                    </p>
                </div>
            </div>

            <!-- Agent Details Sidebar (Locked/Unlocked) -->
            <div class="space-y-6">
                <div class="bg-slate-800/80 border border-slate-700/80 rounded-3xl p-6 space-y-6 shadow-2xl relative">
                    
                    <span class="text-amber-400 text-xs font-bold uppercase tracking-widest block">Agent Contact Info</span>

                    @if($isUnlocked)
                        <!-- Unlocked Contact State -->
                        <div class="flex items-center gap-4 border-b border-slate-700/60 pb-6">
                            <div class="w-14 h-14 rounded-2xl bg-amber-500/20 border border-amber-500/30 flex items-center justify-center text-amber-400 text-xl font-bold">
                                {{ substr($property->agent->name ?? 'A', 0, 1) }}
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-white">{{ $property->agent->name ?? 'Property Agent' }}</h4>
                                <span class="text-xs text-slate-400 capitalize block">{{ $property->agent->role ?? 'Agent' }}</span>
                            </div>
                        </div>

                        <div class="space-y-3">
                            @if(!empty($property->agent->phone))
                                <a href="tel:{{ $property->agent->phone }}" class="w-full bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold py-3.5 px-4 rounded-2xl transition flex items-center justify-center gap-2 shadow-lg shadow-amber-500/20">
                                    <i class="fa-solid fa-phone"></i> Call {{ $property->agent->phone }}
                                </a>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $property->agent->phone) }}?text=Hi, I unlocked your property #{{ $property->id }}" target="_blank" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3.5 px-4 rounded-2xl transition flex items-center justify-center gap-2 shadow-lg shadow-emerald-600/20">
                                    <i class="fa-brands fa-whatsapp text-lg"></i> WhatsApp Direct
                                </a>
                            @endif
                        </div>
                    @else
                        <!-- Locked Contact State -->
                        <div class="relative rounded-2xl border border-slate-700/60 bg-slate-900/80 p-6 text-center space-y-4 overflow-hidden">
                            <div class="filter blur-sm select-none opacity-40 space-y-2 pointer-events-none">
                                <div class="w-12 h-12 rounded-full bg-slate-700 mx-auto"></div>
                                <div class="h-4 bg-slate-700 rounded w-2/3 mx-auto"></div>
                                <div class="h-3 bg-slate-700 rounded w-1/2 mx-auto"></div>
                                <div class="h-10 bg-slate-700 rounded-xl w-full mt-4"></div>
                            </div>

                            <div class="absolute inset-0 flex flex-col items-center justify-center bg-slate-950/80 rounded-2xl p-4 z-10">
                                <div class="w-12 h-12 rounded-full bg-amber-500/20 border border-amber-500/40 flex items-center justify-center text-amber-400 text-xl mb-2">
                                    <i class="fa-solid fa-lock"></i>
                                </div>
                                <h4 class="text-white font-bold text-sm mb-1">Contact Details Hidden</h4>
                                <p class="text-xs text-slate-400 mb-4">Pay a small verification fee to view contact details.</p>
                                
                                <button type="button" wire:click="openUnlockModal" class="w-full bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-extrabold py-3 px-4 rounded-xl transition shadow-lg shadow-amber-500/20 flex items-center justify-center gap-2 text-sm cursor-pointer relative z-20">
                                    <i class="fa-solid fa-key"></i> Unlock Direct Contact (PKR {{ number_format($unlockFee) }})
                                </button>
                            </div>
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>

    <!-- Custom Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
           
            <div class="bg-slate-900 border border-slate-700/80 rounded-3xl max-w-md w-full p-6 sm:p-8 space-y-6 shadow-2xl relative">
                 @if (session()->has('error'))
        <div class="max-w-7xl mx-auto mb-6 bg-rose-500/10 border border-rose-500/30 text-rose-400 px-6 py-4 rounded-2xl flex items-center justify-between">
            <span><i class="fa-solid fa-circle-exclamation mr-2"></i>{{ session('error') }}</span>
        </div>
    @endif
                <button type="button" wire:click="$set('showModal', false)" class="absolute top-5 right-5 text-slate-400 hover:text-white transition">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>

                <div class="text-center space-y-3">
                    <div class="w-16 h-16 bg-amber-500/20 border border-amber-500/40 text-amber-400 rounded-2xl mx-auto flex items-center justify-center text-2xl shadow-inner">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <h3 class="text-xl font-extrabold text-white">Unlock Contact Details</h3>
                    <p class="text-slate-400 text-xs sm:text-sm leading-relaxed">
                        To connect directly with the verified agent, a fee of 
                        <strong class="text-amber-400">PKR {{ number_format($unlockFee) }}</strong> applies.
                    </p>
                </div>

                <div class="bg-slate-800/80 border border-slate-700/60 rounded-2xl p-4 space-y-2">
                    <div class="flex justify-between text-xs text-slate-400">
                        <span>Property ID:</span>
                        <span class="font-bold text-slate-200">#{{ $property->id }}</span>
                    </div>
                    <div class="flex justify-between text-xs text-slate-400">
                        <span>Unlock Fee:</span>
                        <span class="font-bold text-amber-400">PKR {{ number_format($unlockFee) }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-2">
                    <button type="button" wire:click="$set('showModal', false)" class="w-full bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold py-3 rounded-xl text-sm transition">
                        Cancel
                    </button>
                    <button type="button" wire:click="processUnlock" class="w-full bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold py-3 rounded-xl text-sm transition shadow-lg shadow-amber-500/20">
                        Yes, Pay & Unlock
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>