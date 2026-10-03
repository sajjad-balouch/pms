<footer class="bg-slate-950 border-t border-slate-800 text-slate-400 text-sm pt-16 pb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 pb-12 border-b border-slate-800">
            
            <!-- Brand Overview -->
            <div class="lg:col-span-2 space-y-4">
                <a href="/" class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-500 flex items-center justify-center text-slate-950 font-black text-lg">AZ</div>
                    <span class="text-xl font-bold tracking-tight text-white">Assan<span class="text-amber-400">Zameen</span></span>
                </a>
                <p class="text-slate-400 text-xs leading-relaxed max-w-sm">
                    Assan Zameen real estate platform providing verified land ownership records, transparent plot transactions, and end-to-end legal support for buyers and investors.
                </p>
                <div class="flex items-center gap-3 pt-2">
                    <a href="https://www.facebook.com/share/1bWhGHS5Vy/" class="w-9 h-9 rounded-full bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400 hover:text-amber-400 hover:border-amber-400 transition-colors"><i class="fa-brands fa-facebook-f text-xs"></i></a>
                    <a href="#" class="w-9 h-9 rounded-full bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400 hover:text-amber-400 hover:border-amber-400 transition-colors"><i class="fa-brands fa-twitter text-xs"></i></a>
                    <a href="#" class="w-9 h-9 rounded-full bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400 hover:text-amber-400 hover:border-amber-400 transition-colors"><i class="fa-brands fa-instagram text-xs"></i></a>
                </div>
            </div>

            <!-- Dynamic Housing Societies (Towns) -->
            <div>
                <h4 class="text-white font-semibold text-xs uppercase tracking-wider mb-4">Housing Societies</h4>
                <ul class="space-y-2.5 text-xs">
                    @php
                        // Fetching latest active housing schemes / towns from database
                        $footerTowns = \App\Models\Town::latest()->take(5)->get();
                    @endphp

                    @forelse($footerTowns as $fTown)
                        <li>
                            <a href="{{ route('housing-schemes', ['search' => $fTown->name]) }}" class="hover:text-amber-400 transition-colors flex items-center gap-1.5">
                                <i class="fa-solid fa-angle-right text-[10px] text-amber-500/70"></i>
                                <span>{{ $fTown->name }}</span>
                            </a>
                        </li>
                    @empty
                        <li class="text-slate-500 italic">No societies listed</li>
                    @endforelse
                </ul>
            </div>

            <!-- Plot Categories -->
            <div>
                <h4 class="text-white font-semibold text-xs uppercase tracking-wider mb-4">Categories</h4>
                <ul class="space-y-2.5 text-xs">
                    <li><a href="{{ route('available-plots', ['size' => '5_marla']) }}" class="hover:text-amber-400 transition-colors">5 Marla Plots</a></li>
                    <li><a href="{{ route('available-plots', ['size' => '10_marla']) }}" class="hover:text-amber-400 transition-colors">10 Marla Plots</a></li>
                    <li><a href="{{ route('available-plots', ['size' => '1_kanal']) }}" class="hover:text-amber-400 transition-colors">1 Kanal Residential</a></li>
                    <li><a href="{{ route('available-plots', ['type' => 'commercial']) }}" class="hover:text-amber-400 transition-colors">Commercial Boulevard</a></li>
                </ul>
            </div>

            <!-- Newsletter -->
            <div>
                <h4 class="text-white font-semibold text-xs uppercase tracking-wider mb-4">Subscribe Updates</h4>
                <p class="text-xs text-slate-400 mb-3">Get notified on new plot releases and market insights.</p>
                <form class="space-y-2" @submit.prevent>
                    <input type="email" placeholder="Your email address" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-amber-500">
                    <button class="w-full bg-slate-800 hover:bg-slate-700 text-amber-400 font-semibold text-xs py-2 rounded-xl transition-colors">Subscribe</button>
                </form>
            </div>
        </div>

        <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs">
            <p>&copy; {{ date('Y') }} Assan Zameen Inc. All rights reserved.</p>
            <div class="flex gap-6">
                @php
                    $footerPages = \App\Models\Page::where('is_active', true)->get();
                @endphp
                @foreach($footerPages as $fPage)
                    <a href="{{ route('dynamic.page', $fPage->slug) }}" class="hover:text-amber-400 transition-colors">
                        {{ $fPage->title }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</footer>