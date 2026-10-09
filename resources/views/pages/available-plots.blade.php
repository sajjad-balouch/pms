<x-front-app-layout>
    <div class="bg-slate-950 min-h-screen text-slate-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-8">
            
            <!-- Header -->
            <div class="border-b border-slate-800 pb-8">
                <span class="px-3 py-1 bg-amber-500/10 text-amber-400 border border-amber-500/20 text-[10px] font-black uppercase tracking-widest rounded-full">
                    <i class="fa-solid fa-vector-square mr-1"></i> Inventory Marketplace
                </span>
                <h1 class="text-3xl sm:text-5xl font-extrabold text-white mt-3 tracking-tight">{{__("Available Plots")}}</h1>
                <p class="text-slate-400 text-sm mt-2">Filter and inspect verified residential & commercial property inventory.</p>
            </div>

            <!-- Plots Listings Table/Grid Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                
                <!-- Filters Sidebar -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 h-fit space-y-5">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-sliders text-amber-400"></i> Filter Inventory
                    </h3>
                    
                    <div class="space-y-3 text-xs">
                        <div>
                            <label class="text-slate-400 block mb-1">Type</label>
                            <select class="w-full bg-slate-950 border border-slate-800 rounded-xl p-2.5 text-slate-200">
                                <option>All Categories</option>
                                <option>Residential Plot</option>
                                <option>Commercial Plot</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-slate-400 block mb-1">Plot Size</label>
                            <select class="w-full bg-slate-950 border border-slate-800 rounded-xl p-2.5 text-slate-200">
                                <option>Any Size</option>
                                <option>5 Marla</option>
                                <option>10 Marla</option>
                                <option>1 Kanal</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Plots Inventory Cards -->
                <div class="lg:col-span-3 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 relative hover:border-amber-500/50 transition-all">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <span class="text-[10px] bg-emerald-500/10 text-emerald-400 px-2.5 py-0.5 rounded-full border border-emerald-500/20 font-bold">On Possession</span>
                                <h3 class="text-lg font-bold text-white mt-2">5 Marla Residential Plot</h3>
                                <p class="text-xs text-slate-400">Block C, DHA Phase 6</p>
                            </div>
                            <span class="text-amber-400 text-lg font-black">PKR 1.25 Cr</span>
                        </div>
                        <div class="border-t border-b border-slate-800/80 py-3 my-4 grid grid-cols-3 text-center text-xs text-slate-400">
                            <div><span class="block text-[10px] text-slate-500">Size</span> 5 Marla</div>
                            <div><span class="block text-[10px] text-slate-500">Category</span> Residential</div>
                            <div><span class="block text-[10px] text-slate-500">Facing</span> Park Facing</div>
                        </div>
                        <button class="w-full bg-amber-500 text-slate-950 font-bold text-xs py-2.5 rounded-xl hover:bg-amber-400 transition-colors">
                            Inquire Now
                        </button>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-front-app-layout>