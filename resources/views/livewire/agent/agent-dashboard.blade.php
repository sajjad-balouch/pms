<div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

    <!-- Hero / Header Banner -->
    <div class="relative overflow-hidden bg-gradient-to-r from-indigo-900/90 via-slate-900 to-indigo-950 rounded-2xl p-6 sm:p-8 text-white shadow-lg border border-indigo-500/20">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/20 border border-indigo-400/30 text-indigo-300 text-xs font-medium">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    ریئل اسٹیٹ پورٹل
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">Agent Portal & Leads Dashboard</h2>
                <p class="text-slate-300 text-sm font-normal">
                    خوش آمدید، <span class="font-semibold text-white">{{ auth()->user()->name }}</span>! اپنی سرگرمیوں کا جائزہ لیں۔
                </p>
            </div>
            
            <a href="{{ route('agent.leads') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold px-5 py-2.5 rounded-xl text-sm shadow-lg shadow-indigo-950/50 transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                <span>Manage All Leads</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Stat Card 1 -->
        <div class="relative bg-slate-800/90 border border-slate-700/80 p-6 rounded-2xl shadow-md overflow-hidden group hover:border-slate-600 transition-all">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-indigo-500"></div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Leads Assigned</p>
                    <p class="text-3xl font-black text-white mt-2">{{ $totalLeads }}</p>
                </div>
                <div class="p-3 bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
        </div>

        <!-- Stat Card 2 -->
        <div class="relative bg-slate-800/90 border border-slate-700/80 p-6 rounded-2xl shadow-md overflow-hidden group hover:border-slate-600 transition-all">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-amber-500"></div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Active Inquiries</p>
                    <p class="text-3xl font-black text-amber-400 mt-2">{{ $activeLeads }}</p>
                </div>
                <div class="p-3 bg-amber-500/10 border border-amber-500/20 text-amber-400 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                </div>
            </div>
        </div>

        <!-- Stat Card 3 -->
        <div class="relative bg-slate-800/90 border border-slate-700/80 p-6 rounded-2xl shadow-md overflow-hidden group hover:border-slate-600 transition-all">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-emerald-500"></div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Closed Deals</p>
                    <p class="text-3xl font-black text-emerald-400 mt-2">{{ $closedDeals }}</p>
                </div>
                <div class="p-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Leads Table Card -->
    <div class="bg-slate-800/90 rounded-2xl shadow-md border border-slate-700/80 overflow-hidden">
        <div class="flex justify-between items-center px-6 py-5 border-b border-slate-700/80">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-rose-400">📞</span> Recent Client Leads
            </h3>
            <a href="{{ route('agent.leads') }}" class="text-xs font-bold text-indigo-400 hover:text-indigo-300 transition-colors">
                View All &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-900/60 text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-700/80">
                        <th class="px-6 py-4">Client Name</th>
                        <th class="px-6 py-4">Phone</th>
                        <th class="px-6 py-4">Housing Scheme</th>
                        <th class="px-6 py-4">Budget</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/60 text-sm">
                    @forelse ($recentLeads as $lead)
                        <tr class="hover:bg-slate-700/30 transition-colors">
                            <td class="px-6 py-4 font-semibold text-white">
                                {{ $lead->client_name }}
                            </td>
                            <td class="px-6 py-4 text-slate-300">
                                {{ $lead->client_phone }}
                            </td>
                            <td class="px-6 py-4 text-slate-300 font-medium">
                                {{ $lead->town?->name ?? 'General Inquiry' }}
                            </td>
                            <td class="px-6 py-4 font-bold text-emerald-400">
                                {{ $lead->budget ? 'Rs. ' . number_format($lead->budget) : 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold capitalize
                                    {{ $lead->status === 'closed_won' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : '' }}
                                    {{ $lead->status === 'new' ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : '' }}
                                    {{ $lead->status === 'negotiation' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : '' }}
                                    {{ in_array($lead->status, ['contacted', 'site_visit']) ? 'bg-purple-500/10 text-purple-400 border border-purple-500/20' : '' }}
                                    {{ $lead->status === 'closed_lost' ? 'bg-rose-500/10 text-rose-400 border border-rose-500/20' : '' }}">
                                    {{ str_replace('_', ' ', $lead->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-slate-400 text-xs">
                                {{ $lead->created_at->format('d M Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                                کوئی لیڈ نہیں ملی۔
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>