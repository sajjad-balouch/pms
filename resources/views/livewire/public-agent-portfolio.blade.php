<div class="min-h-screen bg-slate-950 text-slate-100 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto space-y-8">

        <!-- Top Profile Card (Visiting Card Look) -->
        <div class="bg-gradient-to-br from-slate-900 to-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl relative overflow-hidden">
            <div class="absolute -top-12 -right-12 w-48 h-48 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 relative z-10">
                <!-- Agency Logo / Avatar -->
                <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl overflow-hidden border-2 border-indigo-500/30 bg-slate-950 shrink-0 shadow-lg flex items-center justify-center">
                    @if($agent->agency_logo)
                        <img src="{{ asset('public/' . $agent->agency_logo) }}" alt="{{ $agent->agency_name }}" class="w-full h-full object-cover">
                    @else
                        <i class="fa-solid fa-building text-3xl text-indigo-400"></i>
                    @endif
                </div>

                <!-- Agency Meta -->
                <div class="flex-1 text-center sm:text-left space-y-2">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-black text-white">{{ $agent->agency_name ?: $agent->name }}</h1>
                            <p class="text-xs font-bold text-amber-400">{{ $agent->agency_tagline }}</p>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-bold rounded-full mx-auto sm:mx-0">
                            <i class="fa-solid fa-badge-check"></i> Verified Agent
                        </span>
                    </div>

                    <p class="text-xs text-slate-400 leading-relaxed max-w-xl">
                        {{ $agent->about_agency ?: 'Your trusted real estate advisor. We provide premium, legally verified plots, houses, and commercial properties.' }}
                    </p>

                    <!-- Contact & Location Icons -->
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-3 pt-2 text-xs text-slate-300">
                        @if($agent->city_name)
                            <span class="flex items-center gap-1.5 bg-slate-800/80 px-2.5 py-1 rounded-lg">
                                <i class="fa-solid fa-location-dot text-rose-400"></i> {{ $agent->city_name }}
                            </span>
                        @endif
                        @if($agent->office_address)
                            <span class="flex items-center gap-1.5 bg-slate-800/80 px-2.5 py-1 rounded-lg">
                                <i class="fa-solid fa-store text-indigo-400"></i> {{ $agent->office_address }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Action Buttons: Save Contact & WhatsApp -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-6 mt-6 border-t border-slate-800/80 relative z-10">
                <!-- 1. Download vCard -->
                <button type="button" wire:click="downloadVCard" class="py-2.5 px-4 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 font-bold text-xs rounded-xl transition flex items-center justify-center gap-2 cursor-pointer shadow-sm">
                    <i class="fa-solid fa-address-book text-amber-400"></i>
                    <span>Save Contact (vCard)</span>
                </button>

                <!-- 2. WhatsApp Direct -->
                @php
                    $cleanPhone = preg_replace('/[^0-9]/', '', $agent->phone);
                    if (str_starts_with($cleanPhone, '0')) $cleanPhone = '92' . substr($cleanPhone, 1);
                @endphp
                <a href="https://wa.me/{{ $cleanPhone }}?text={{ urlencode('Hello! I viewed your profile on AssanZameen and would like to inquire about your property listings.') }}" target="_blank"
                   class="py-2.5 px-4 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl transition flex items-center justify-center gap-2 shadow-md shadow-emerald-600/20 cursor-pointer">
                    <i class="fa-brands fa-whatsapp text-sm"></i>
                    <span>Direct WhatsApp</span>
                </a>

                <!-- 3. Direct Phone Call -->
                <a href="tel:{{ $agent->phone }}" class="py-2.5 px-4 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl transition flex items-center justify-center gap-2 shadow-md shadow-indigo-600/20 cursor-pointer">
                    <i class="fa-solid fa-phone text-xs"></i>
                    <span>Call Now ({{ $agent->phone }})</span>
                </a>
            </div>
        </div>

        <!-- Agency Active Listings Section -->
        <div class="space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h2 class="text-lg font-black text-white flex items-center gap-2">
                    <i class="fa-solid fa-house-chimney text-amber-400"></i>
                    <span>Agency Available Properties ({{ count($properties) }})</span>
                </h2>
                <span class="text-xs text-slate-400">100% Verified Listings</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse($properties as $property)
                    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl overflow-hidden shadow-xl hover:border-slate-700 transition flex flex-col justify-between">
                        <div>
                            <div class="h-40 bg-slate-950 relative overflow-hidden">
                                @if(!empty($property->images) && is_array($property->images) && count($property->images) > 0)
                                    <img src="{{ asset('public/' . $property->images[0]) }}" alt="{{ $property->title }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-700">
                                        <i class="fa-solid fa-image text-3xl"></i>
                                    </div>
                                @endif
                                <span class="absolute top-2.5 right-2.5 px-2.5 py-1 bg-slate-900/80 backdrop-blur-md text-[10px] font-bold text-white uppercase rounded-md border border-slate-700">
                                    {{ str_replace('_', ' ', $property->purpose) }}
                                </span>
                            </div>

                            <div class="p-4 space-y-2">
                                <div class="text-base font-bold text-white line-clamp-1">{{ $property->title }}</div>
                                <div class="text-xs text-slate-400 flex items-center gap-1.5">
                                    <i class="fa-solid fa-location-dot text-slate-500 text-[11px]"></i>
                                    <span class="truncate">{{ $property->location }}</span>
                                </div>
                                <div class="text-xs font-semibold text-slate-300">Area: <span class="text-amber-400">{{ $property->area_size }}</span></div>
                            </div>
                        </div>

                        <div class="p-4 pt-0 border-t border-slate-800/80 mt-2 flex items-center justify-between">
                            <span class="text-sm font-black text-emerald-400 font-mono">PKR {{ number_format($property->price) }}</span>
                            <a href="{{ url('/property/' . $property->id) }}" class="px-3 py-1.5 bg-indigo-600/20 hover:bg-indigo-600 text-indigo-400 hover:text-white border border-indigo-500/30 rounded-lg text-xs font-bold transition">
                                View Details
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full p-10 text-center text-slate-500 bg-slate-900/40 rounded-2xl border border-slate-800">
                        Currently, there are no active property listings available for this agency.
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>