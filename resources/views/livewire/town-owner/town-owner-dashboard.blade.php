<div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 bg-gray-950 text-gray-100 min-h-screen">

    <!-- Top Header & Navigation Links -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center bg-gray-900/80 backdrop-blur-md p-6 rounded-2xl border border-gray-800/80 shadow-2xl gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <h2 class="text-2xl font-black text-white tracking-tight">Town Owner Dashboard</h2>
            </div>
            <p class="text-sm text-gray-400 mt-1">خوش آمدید، <span class="font-bold text-indigo-400">{{ auth()->user()->name }}</span></p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
            <!-- Navigation Links -->
            <a href="{{ route('town_owner.towns') }}" class="inline-flex items-center gap-2 bg-indigo-600/20 hover:bg-indigo-600/30 text-indigo-300 border border-indigo-500/30 px-4 py-2.5 rounded-xl font-semibold text-sm shadow-sm hover:shadow-indigo-500/10 transition-all duration-200">
                <span>🏘️</span>
                <span>Schemes</span>
            </a>

            <a href="{{ route('town_owner.employees') }}" class="inline-flex items-center gap-2 bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-300 border border-emerald-500/30 px-4 py-2.5 rounded-xl font-semibold text-sm shadow-sm hover:shadow-emerald-500/10 transition-all duration-200">
                <span>👷</span>
                <span>Employees</span>
            </a>

            <a href="{{ route('town_owner.expenses') }}" class="inline-flex items-center gap-2 bg-rose-600/20 hover:bg-rose-600/30 text-rose-300 border border-rose-500/30 px-4 py-2.5 rounded-xl font-semibold text-sm shadow-sm hover:shadow-rose-500/10 transition-all duration-200">
                <span>💸</span>
                <span>Expenses</span>
            </a>

            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 bg-gray-800 hover:bg-red-500/20 hover:text-red-400 hover:border-red-500/40 text-gray-400 border border-gray-700 px-4 py-2.5 rounded-xl font-medium text-sm transition-all duration-200">
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Quick Inventory Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-gray-900/60 p-5 rounded-2xl border border-gray-800/80 shadow-lg hover:border-purple-500/50 transition-all duration-200 relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-1 h-full bg-purple-500"></div>
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Housing Towns</p>
            <p class="text-3xl font-black text-white mt-2 group-hover:text-purple-400 transition-colors">{{ $towns->count() }}</p>
        </div>

        <div class="bg-gray-900/60 p-5 rounded-2xl border border-gray-800/80 shadow-lg hover:border-indigo-500/50 transition-all duration-200 relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-1 h-full bg-indigo-500"></div>
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Plots Inventory</p>
            <p class="text-3xl font-black text-white mt-2 group-hover:text-indigo-400 transition-colors">{{ number_format($totalPlots) }}</p>
        </div>

        <div class="bg-gray-900/60 p-5 rounded-2xl border border-gray-800/80 shadow-lg hover:border-emerald-500/50 transition-all duration-200 relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-1 h-full bg-emerald-500"></div>
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Available Plots</p>
            <p class="text-3xl font-black text-emerald-400 mt-2">{{ number_format($availablePlots) }}</p>
        </div>

        <div class="bg-gray-900/60 p-5 rounded-2xl border border-gray-800/80 shadow-lg hover:border-amber-500/50 transition-all duration-200 relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-1 h-full bg-amber-500"></div>
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Booked / Sold Plots</p>
            <p class="text-3xl font-black text-amber-400 mt-2">{{ number_format($bookedPlots) }}</p>
        </div>
    </div>

    <!-- Financial Income & Collection Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="bg-gray-900/80 p-6 rounded-2xl border border-gray-800/80 shadow-xl relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-emerald-500"></div>
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Recovery / Cash Collected</p>
            <p class="text-3xl font-black text-emerald-400 mt-2">Rs. {{ number_format($totalCollected) }}</p>
        </div>

        <div class="bg-gray-900/80 p-6 rounded-2xl border border-gray-800/80 shadow-xl relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-blue-500"></div>
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Expected Collection (This Month)</p>
            <p class="text-3xl font-black text-blue-400 mt-2">Rs. {{ number_format($dueThisMonth) }}</p>
        </div>
    </div>

    <!-- Expenses, Salary & Net Profit Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-gray-900/60 p-5 rounded-2xl border border-gray-800/80 shadow-lg relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-rose-500"></div>
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Town Expenses</p>
            <p class="text-2xl font-black text-rose-400 mt-2">Rs. {{ number_format($totalExpenses) }}</p>
        </div>

        <div class="bg-gray-900/60 p-5 rounded-2xl border border-gray-800/80 shadow-lg relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-orange-500"></div>
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Salaries & Advances Paid</p>
            <p class="text-2xl font-black text-orange-400 mt-2">Rs. {{ number_format($totalSalariesPaid) }}</p>
        </div>

        <div class="bg-gray-900/60 p-5 rounded-2xl border border-gray-800/80 shadow-lg relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-red-600"></div>
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Outflow (Expenses + Salary)</p>
            <p class="text-2xl font-black text-red-400 mt-2">Rs. {{ number_format($totalOutflow) }}</p>
        </div>

        <div class="bg-gray-900/60 p-5 rounded-2xl border border-gray-800/80 shadow-lg relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full {{ $netProfit >= 0 ? 'bg-teal-500' : 'bg-red-500' }}"></div>
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Net Profit / Balance</p>
            <p class="text-2xl font-black {{ $netProfit >= 0 ? 'text-teal-400' : 'text-red-400' }} mt-2">
                Rs. {{ number_format($netProfit) }}
            </p>
        </div>
    </div>

    <!-- Town Schemes Summary Table -->
    <div class="bg-gray-900/80 rounded-2xl border border-gray-800/80 shadow-2xl overflow-hidden">
        <div class="p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-gray-800/80">
            <h3 class="text-lg font-extrabold text-white flex items-center gap-2">
                <span>🏠</span> My Housing Projects
            </h3>
            <a href="{{ route('town_owner.towns') }}" class="inline-flex items-center text-sm font-semibold text-indigo-400 hover:text-indigo-300 transition-colors">
                + Add New Scheme
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-800/80 text-sm">
                <thead class="bg-gray-950/50 text-gray-400 text-xs uppercase tracking-wider">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left font-bold">Town Name</th>
                        <th scope="col" class="px-6 py-4 text-left font-bold">City / Location</th>
                        <th scope="col" class="px-6 py-4 text-left font-bold">Total Area</th>
                        <th scope="col" class="px-6 py-4 text-left font-bold">Total Plots</th>
                        <th scope="col" class="px-6 py-4 text-left font-bold">NOC Status</th>
                        <th scope="col" class="px-6 py-4 text-right font-bold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60 text-gray-300">
                    @forelse ($towns as $town)
                        <tr class="hover:bg-gray-800/40 transition-colors">
                            <td class="px-6 py-4 font-bold text-white whitespace-nowrap">{{ $town->name }}</td>
                            <td class="px-6 py-4 text-gray-400 whitespace-nowrap">{{ $town->city }} - {{ $town->location }}</td>
                            <td class="px-6 py-4 text-gray-400 whitespace-nowrap">{{ $town->total_area ?? 'N/A' }}</td>
                            <td class="px-6 py-4 font-bold text-indigo-400 whitespace-nowrap">{{ $town->plots_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                    {{ $town->noc_number ?? 'Approved' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <a href="{{ route('town_owner.plots', $town->id) }}" class="inline-flex items-center gap-1.5 bg-purple-600/20 hover:bg-purple-600/30 text-purple-300 border border-purple-500/30 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all duration-200">
                                    <span>📍</span> Manage Plots
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                <p class="text-base font-semibold">کوئی ہاؤسنگ سکیم نہیں ملی۔</p>
                                <a href="{{ route('town_owner.towns') }}" class="text-indigo-400 font-bold hover:underline mt-1 inline-block">
                                    یہاں کلک کر کے نئی سکیم شامل کریں
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>