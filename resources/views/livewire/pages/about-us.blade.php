<div class="bg-slate-950 min-h-screen text-slate-100 py-16 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto space-y-20">
        
        <!-- Hero Section -->
        <div class="text-center space-y-5 max-w-3xl mx-auto">
            <span class="px-3 py-1 bg-amber-500/10 text-amber-400 border border-amber-500/20 text-[10px] font-black uppercase tracking-widest rounded-full">
                <i class="fa-solid fa-compass-drafting mr-1"></i> {{__("Who We Are")}}
            </span>
            <h1 class="text-4xl sm:text-6xl font-black text-white tracking-tight leading-tight">
                {{__("Redefining Real Estate")}} <span class="text-amber-400">{{__("Connectivity")}}</span> {{__("& Transparency")}}
            </h1>
            <p class="text-slate-400 text-sm sm:text-base leading-relaxed">
                {{__("Assan Zameen bridges the gap between buyers, town owners, and real estate agents through a unified digital ecosystem. We simplify buying, selling, and renting properties while providing verified NOC records, real-time map locations, and direct contact access.")}}
            </p>
        </div>

        <!-- Featured Image & Mission Highlight Banner -->
        <div class="relative rounded-3xl overflow-hidden border border-slate-800 bg-slate-900/60 shadow-2xl">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
                <div class="p-8 sm:p-12 flex flex-col justify-center space-y-6">
                    <span class="text-amber-400 text-xs font-bold uppercase tracking-wider">{{__("Our Core Mission")}}</span>
                    <h2 class="text-2xl sm:text-4xl font-extrabold text-white leading-snug">
                        {{__("Connecting Buyers, Town Owners & Agents in One Seamless Ecosystem")}}
                    </h2>
                    <p class="text-slate-400 text-xs sm:text-sm leading-relaxed">
                        {{__("Navigating property markets often involves hidden details and difficult agent coordination. Our portal connects town owners directly with verified property agents and end-buyers. Whether you want to list a town plot, find rental units, or purchase verified property with precise location directions, Assan Zameen brings everything to your fingertips.")}}
                    </p>
                    <div class="pt-2 flex flex-wrap gap-4 text-xs font-semibold text-slate-300">
                        <span class="flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-emerald-400"></i> {{__("Easy Buying & Selling")}}</span>
                        <span class="flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-emerald-400"></i> {{__("Seamless Rental Listings")}}</span>
                        <span class="flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-emerald-400"></i> {{__("Exact Map Directions")}}</span>
                    </div>
                </div>
                <div class="relative min-h-[300px] lg:min-h-full">
                    <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?q=80&w=1000" alt="Real Estate Ecosystem" class="absolute inset-0 w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent lg:bg-gradient-to-r lg:from-slate-950 lg:via-transparent lg:to-transparent"></div>
                </div>
            </div>
        </div>

        <!-- Stat Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="bg-slate-900/80 border border-slate-800 p-6 rounded-3xl text-center space-y-1 hover:border-amber-500/30 transition-all">
                <h3 class="text-3xl font-extrabold text-amber-400">100%</h3>
                <p class="text-xs text-slate-400">Verified NOCs & Towns</p>
            </div>
            <div class="bg-slate-900/80 border border-slate-800 p-6 rounded-3xl text-center space-y-1 hover:border-amber-500/30 transition-all">
                <h3 class="text-3xl font-extrabold text-white">50+</h3>
                <p class="text-xs text-slate-400">Housing Schemes</p>
            </div>
            <div class="bg-slate-900/80 border border-slate-800 p-6 rounded-3xl text-center space-y-1 hover:border-amber-500/30 transition-all">
                <h3 class="text-3xl font-extrabold text-amber-400">10K+</h3>
                <p class="text-xs text-slate-400">Plots & Listings</p>
            </div>
            <div class="bg-slate-900/80 border border-slate-800 p-6 rounded-3xl text-center space-y-1 hover:border-amber-500/30 transition-all">
                <h3 class="text-3xl font-extrabold text-white">24/7</h3>
                <p class="text-xs text-slate-400">Map & Contact Access</p>
            </div>
        </div>

        <!-- How We Bridge the Gap (3 Feature Columns) -->
        <div class="space-y-10">
            <div class="text-center space-y-2 max-w-2xl mx-auto">
                <h2 class="text-2xl sm:text-3xl font-bold text-white">{{__("How Assan Zameen Works for You")}}</h2>
                <p class="text-slate-400 text-xs sm:text-sm">{{__("Empowering every participant in the real estate marketplace with modern tools.")}}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div class="bg-slate-900/80 border border-slate-800 rounded-3xl overflow-hidden hover:border-amber-500/40 transition-all flex flex-col justify-between">
                    <div class="h-44 overflow-hidden relative">
                        <img src="https://images.unsplash.com/photo-1582407947304-fd86f028f716?q=80&w=600" alt="Town Owners" class="w-full h-full object-cover">
                        <span class="absolute top-3 left-3 bg-slate-950/80 backdrop-blur-md text-amber-400 text-[10px] font-bold px-3 py-1 rounded-full border border-amber-500/20">
                            {{__("Town Owners")}}
                        </span>
                    </div>
                    <div class="p-6 space-y-3 flex-1">
                        <h3 class="text-lg font-bold text-white">{{__("Digital Town Registry")}}</h3>
                        <p class="text-slate-400 text-xs leading-relaxed">
                            {{__("Town owners can showcase housing schemes, list available plots, upload layout plans, and update location map links so buyers find them instantly.")}}
                        </p>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="bg-slate-900/80 border border-slate-800 rounded-3xl overflow-hidden hover:border-amber-500/40 transition-all flex flex-col justify-between">
                    <div class="h-44 overflow-hidden relative">
                        <img src="https://images.unsplash.com/photo-1570129477492-45c003edd2be?q=80&w=600" alt="Agents & Buyers" class="w-full h-full object-cover">
                        <span class="absolute top-3 left-3 bg-slate-950/80 backdrop-blur-md text-emerald-400 text-[10px] font-bold px-3 py-1 rounded-full border border-emerald-500/20">
                            {{__("Agents & Buyers")}}
                        </span>
                    </div>
                    <div class="p-6 space-y-3 flex-1">
                        <h3 class="text-lg font-bold text-white">{{__("Seamless Buying, Selling & Renting")}}</h3>
                        <p class="text-slate-400 text-xs leading-relaxed">
                            {{__("Property agents and buyers can filter through live inventory, unlock direct owner contact numbers, and conduct hassle-free trade deals.")}}
                        </p>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="bg-slate-900/80 border border-slate-800 rounded-3xl overflow-hidden hover:border-amber-500/40 transition-all flex flex-col justify-between">
                    <div class="h-44 overflow-hidden relative">
                        <img src="https://images.unsplash.com/photo-1524813686514-a57563d77965?q=80&w=600" alt="GPS Directions" class="w-full h-full object-cover">
                        <span class="absolute top-3 left-3 bg-slate-950/80 backdrop-blur-md text-amber-400 text-[10px] font-bold px-3 py-1 rounded-full border border-amber-500/20">
                            {{__("Location Guidance")}}
                        </span>
                    </div>
                    <div class="p-6 space-y-3 flex-1">
                        <h3 class="text-lg font-bold text-white">{{__("Precision Google Map Routes")}}</h3>
                        <p class="text-slate-400 text-xs leading-relaxed">
                            {{__("Every property listing comes equipped with exact Google Map links or auto-generated location queries so users can easily drive directly to the site.")}}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Callout -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-8 sm:p-12 text-center space-y-6 relative overflow-hidden">
            <div class="relative z-10 space-y-3 max-w-xl mx-auto">
                <h3 class="text-2xl sm:text-3xl font-extrabold text-white">{{__("Ready to Explore Verified Properties?")}}</h3>
                <p class="text-slate-400 text-xs sm:text-sm">{{__("Start searching through available inventory or register your town scheme today.")}}</p>
                <div class="pt-2 flex flex-wrap justify-center gap-4">
                    <a href="{{ route('housing-schemes') }}" class="bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold px-6 py-3 rounded-xl text-xs transition-all">
                        {{__("Browse Housing Schemes")}}
                    </a>
                    <a href="{{ route('available-plots') }}" class="bg-slate-800 hover:bg-slate-700 text-white font-bold px-6 py-3 rounded-xl text-xs border border-slate-700 transition-all">
                        {{__("View Available Plots")}}
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>