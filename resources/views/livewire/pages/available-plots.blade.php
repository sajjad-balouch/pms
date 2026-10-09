<div class="bg-slate-950 min-h-screen text-slate-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-8">
        
        <!-- Header -->
        <div class="border-b border-slate-800 pb-8">
            <span class="px-3 py-1 bg-amber-500/10 text-amber-400 border border-amber-500/20 text-[10px] font-black uppercase tracking-widest rounded-full">
                <i class="fa-solid fa-vector-square mr-1"></i> {{__("Inventory Marketplace")}}
            </span>
            <h1 class="text-3xl sm:text-5xl font-extrabold text-white mt-3 tracking-tight">{{__("Available Plots")}}</h1>
            <p class="text-slate-400 text-sm mt-2">{{__("Filter and inspect verified residential & commercial property inventory.")}}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            
            <!-- Filters Sidebar -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 h-fit space-y-5">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-sliders text-amber-400"></i> {{__("Filter Inventory")}}
                </h3>
                
                <div class="space-y-4 text-xs">
                    <div>
                        <label class="text-slate-400 block mb-1">{{__("Search Plot No.")}}</label>
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="e.g. B-104" class="w-full bg-slate-950 border border-slate-800 rounded-xl p-2.5 text-slate-200 focus:border-amber-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="text-slate-400 block mb-1">{{__("Category Type")}}</label>
                        <select wire:model.live="type" class="w-full bg-slate-950 border border-slate-800 rounded-xl p-2.5 text-slate-200 focus:border-amber-500 focus:outline-none">
                            <option value="all">{{__("All Categories")}}</option>
                            <option value="residential">{{__("Residential Plot")}}</option>
                            <option value="commercial">{{__("Commercial Plot")}}</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-slate-400 block mb-1">{{__("Plot Size")}}</label>
                        <select wire:model.live="size" class="w-full bg-slate-950 border border-slate-800 rounded-xl p-2.5 text-slate-200 focus:border-amber-500 focus:outline-none">
                            <option value="all">{{__("Any Size")}}</option>
                            <option value="5">{{__("5 Marla")}}</option>
                            <option value="10">{{__("10 Marla")}}</option>
                            <option value="1">{{__("1 Kanal")}}</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Dynamic Plots Grid -->
            <div class="lg:col-span-3 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse($plots as $plot)
                        @php
                            $townMapUrl = !empty(trim($plot->town->google_map_url ?? ''))
                                ? $plot->town->google_map_url
                                : 'https://www.google.com/maps/search/?api=1&query=' . urlencode(($plot->town->location ?? '') . ', ' . ($plot->town->city ?? ''));
                        @endphp

                        <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 relative hover:border-amber-500/50 transition-all flex flex-col justify-between space-y-4">
                            <div>
                                <!-- Top Badges & Header -->
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <span class="text-[10px] bg-emerald-500/10 text-emerald-400 px-2.5 py-0.5 rounded-full border border-emerald-500/20 font-bold uppercase">
                                            {{ $plot->status ?? '__("Available")' }}
                                        </span>
                                        <h3 class="text-xl font-extrabold text-white mt-2">{{__("Plot")}} #{{ $plot->plot_number }}</h3>
                                        
                                        <!-- 🏢 TOWN DETAIL WITH GOOGLE MAP LINK -->
                                        <div class="text-xs text-amber-400 font-semibold mt-1 flex items-center gap-1.5 flex-wrap">
                                            <i class="fa-solid fa-city"></i>
                                            <span>{{ $plot->town->name ?? 'N/A' }}</span>
                                            
                                            @if(!empty($plot->town->location))
                                                <span class="text-slate-400 font-normal">({{ $plot->town->location }})</span>
                                            @endif

                                            <!-- Google Map Marker Button/Link -->
                                            <a href="{{ $townMapUrl }}" target="_blank" rel="noopener noreferrer" title="View Town on Google Maps" class="inline-flex items-center gap-1 text-[10px] bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded-md transition-colors ml-1">
                                                <i class="fa-solid fa-location-dot"></i> {{__("Map")}}
                                            </a>
                                        </div>
                                    </div>

                                    <span class="text-amber-400 text-lg font-black">
                                        PKR {{ number_format($plot->total_price) }}
                                    </span>
                                </div>

                                <!-- Plot Meta Details -->
                                <div class="border-t border-b border-slate-800/80 py-3 my-4 grid grid-cols-3 text-center text-xs text-slate-400">
                                    <div><span class="block text-[10px] text-slate-500">Size</span> {{ $plot->size }}</div>
                                    <div><span class="block text-[10px] text-slate-500">Type</span> {{ ucfirst($plot->type) }}</div>
                                    <div><span class="block text-[10px] text-slate-500">Block</span> {{ $plot->block_name ?? 'N/A' }}</div>
                                </div>
                            </div>

                            <!-- 👤 TOWN OWNER / CONTACT DETAIL -->
                            <div class="pt-2 border-t border-slate-800/60 flex items-center justify-between text-xs text-slate-400">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-slate-800 flex items-center justify-center text-amber-400 font-bold text-[10px]">
                                        {{ strtoupper(substr($plot->town->user->name ?? 'A', 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="block text-[10px] text-slate-500">Town Owner</span>
                                        <strong class="text-slate-200 text-xs">{{ $plot->town->user->name ?? 'Administrator' }}</strong>
                                    </div>
                                </div>

                                @php
                                    $ownerId = $plot->town->user_id ?? null;
                                    $isUnlocked = (isset($unlockedPlotIds) && in_array($plot->id, $unlockedPlotIds)) || 
                                                  ($ownerId && isset($unlockedUserIds) && in_array($ownerId, $unlockedUserIds));
                                @endphp

                                @if($isUnlocked)
                                    <div class="text-right bg-amber-500/10 border border-amber-500/20 rounded-xl px-3 py-1.5 space-y-0.5">
                                        <span class="inline-block text-[9px] font-black uppercase text-amber-400 tracking-wider">Unlocked Contact</span>
                                        <div class="text-xs text-emerald-400 font-bold flex items-center gap-1 justify-end">
                                            <i class="fa-solid fa-phone text-[10px]"></i>
                                            <a href="tel:{{ $plot->town->user->phone ?? '' }}" class="hover:underline">{{ $plot->town->user->phone ?? 'N/A' }}</a>
                                        </div>
                                    </div>
                                @else
                                    <button wire:click="inquirePlot({{ $plot->id }})" class="bg-amber-500 text-slate-950 font-bold text-xs px-4 py-2 rounded-xl hover:bg-amber-400 transition-colors">
                                        Inquire
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center text-slate-500 py-12">
                            No available plots found.
                        </div>
                    @endforelse
                </div>

                <div>
                    {{ $plots->links() }}
                </div>
            </div>

        </div>

    </div>

    <!-- 🔒 UNLOCK CONTACT MODAL -->
    @if($showModal && $selectedPlot)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 max-w-md w-full space-y-5 relative shadow-2xl">
                
                <!-- Close Button -->
                <button wire:click="closeModal" class="absolute top-4 right-4 text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>

                <div class="text-center">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-400 border border-amber-500/20 flex items-center justify-center mx-auto mb-3 text-xl">
                        <i class="fa-solid fa-lock-open"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white">Unlock Contact Details</h3>
                    <p class="text-xs text-slate-400 mt-1">Plot #{{ $selectedPlot->plot_number }} ({{ $selectedPlot->town->name ?? 'N/A' }})</p>
                </div>

                <!-- Alert Messages -->
                @if(session()->has('modal_error'))
                    <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 p-3 rounded-xl text-xs text-center">
                        {{ session('modal_error') }}
                    </div>
                @endif

                @if(session()->has('modal_success'))
                    <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-3 rounded-xl text-xs text-center">
                        {{ session('modal_success') }}
                    </div>
                @endif

                <!-- Content Area -->
                @if($unlockedContactData)
                    <!-- UNLOCKED CONTACT INFO -->
                    <div class="bg-slate-950 border border-slate-800 rounded-2xl p-4 space-y-3 text-xs">
                        <div class="flex justify-between items-center border-b border-slate-800 pb-2">
                            <span class="text-slate-500">Owner / Agent Name:</span>
                            <strong class="text-white">{{ $unlockedContactData['name'] }}</strong>
                        </div>
                        <div class="flex justify-between items-center border-b border-slate-800 pb-2">
                            <span class="text-slate-500">Phone Number:</span>
                            <strong class="text-amber-400 font-mono text-sm">{{ $unlockedContactData['phone'] }}</strong>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500">Email:</span>
                            <strong class="text-slate-200">{{ $unlockedContactData['email'] }}</strong>
                        </div>
                    </div>

                    <a href="tel:{{ $unlockedContactData['phone'] }}" class="w-full block text-center bg-emerald-500 text-slate-950 font-bold py-3 rounded-xl hover:bg-emerald-400 transition-all text-xs">
                        <i class="fa-solid fa-phone mr-1"></i> Call Now
                    </a>
                @else
                    <!-- UNLOCK CONFIRMATION & FEE DETAIL -->
                    <div class="bg-slate-950 border border-slate-800 rounded-2xl p-4 space-y-3 text-xs">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Unlock Fee:</span>
                            <span class="text-amber-400 font-bold">PKR {{ number_format($unlockFee) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Your Wallet Balance:</span>
                            <span class="text-white font-bold">PKR {{ number_format(Auth::user()->wallet_balance ?? 0) }}</span>
                        </div>
                    </div>

                    <button wire:click="processUnlock" class="w-full bg-amber-500 text-slate-950 font-bold py-3 rounded-xl hover:bg-amber-400 transition-all text-xs">
                        Pay PKR {{ number_format($unlockFee) }} & Unlock Contact
                    </button>
                @endif

            </div>
        </div>
    @endif
</div>