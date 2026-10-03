<div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 bg-gray-950 text-gray-100 min-h-screen">

    <!-- Top Navigation Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-gray-900/80 backdrop-blur-md p-6 rounded-2xl border border-gray-800/80 shadow-2xl gap-4">
        <div>
            <h2 class="text-2xl font-black text-white tracking-tight flex items-center gap-2">
                <span>👷</span> Employees & Salary Management — {{ $town->name }}
            </h2>
            <p class="text-xs text-gray-400 mt-1 font-medium">Mulazmeen ke kowaif aur tankhwahon ka shumar</p>
        </div>
    </div>

    <!-- Alert / Session Messages -->
    @if (session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-5 py-3.5 rounded-xl text-sm font-medium flex items-center gap-3 shadow-sm">
            <span>✅</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Add Employee Form Card -->
        <div class="bg-gray-900/80 backdrop-blur-md p-6 rounded-2xl border border-gray-800/80 shadow-2xl space-y-5 h-fit">
            <div class="border-b border-gray-800/80 pb-4">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <span>➕</span> Add New Employee
                </h3>
                <p class="text-xs text-gray-400 mt-0.5">Naye mulazim ke kowaif darj karein.</p>
            </div>

            <form wire:submit.prevent="addEmployee" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Name *</label>
                    <input type="text" wire:model="name" placeholder="e.g. Muhammad Ahmad" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder-gray-600 transition-colors" required />
                    @error('name') <span class="text-xs text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Phone</label>
                    <input type="text" wire:model="phone" placeholder="e.g. 03001234567" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder-gray-600 transition-colors" />
                    @error('phone') <span class="text-xs text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Designation *</label>
                    <input type="text" wire:model="designation" placeholder="e.g. Guard, Manager" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder-gray-600 transition-colors" required />
                    @error('designation') <span class="text-xs text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Monthly Fixed Salary *</label>
                    <input type="number" wire:model="monthly_salary" placeholder="35000" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder-gray-600 transition-colors" required />
                    @error('monthly_salary') <span class="text-xs text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <button type="submit" wire:loading.attr="disabled" class="w-full inline-flex items-center justify-center gap-2 bg-indigo-600/20 hover:bg-indigo-600/30 text-indigo-300 border border-indigo-500/30 font-bold py-3 px-4 rounded-xl text-sm shadow-sm transition-all duration-200">
                    <span wire:loading.remove>Save Employee</span>
                    <span wire:loading class="animate-pulse">Saving...</span>
                </button>
            </form>
        </div>

        <!-- Record Salary or Advance Payment Card -->
        <div class="bg-gray-900/80 backdrop-blur-md p-6 rounded-2xl border border-gray-800/80 shadow-2xl space-y-5 h-fit">
            <div class="border-b border-gray-800/80 pb-4">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <span>💵</span> Pay Salary / Advance
                </h3>
                <p class="text-xs text-gray-400 mt-0.5">Salary ya advance payment ki adayeghi ka record banayein.</p>
            </div>

            <form wire:submit.prevent="recordPayment" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Select Employee *</label>
                    <select wire:model="selected_employee_id" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-colors" required>
                        <option value="">Select Employee</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->designation }}) - Fixed: Rs. {{ number_format($emp->monthly_salary) }}</option>
                        @endforeach
                    </select>
                    @error('selected_employee_id') <span class="text-xs text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Payment Type *</label>
                    <select wire:model="payment_type" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-colors">
                        <option value="salary">Monthly Salary</option>
                        <option value="advance">Advance Payment</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Amount *</label>
                    <input type="number" wire:model="amount" placeholder="25000" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 placeholder-gray-600 transition-colors" required />
                    @error('amount') <span class="text-xs text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                @if($payment_type === 'salary')
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Salary Month</label>
                        <input type="text" wire:model="salary_month" placeholder="e.g. September 2026" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 placeholder-gray-600 transition-colors" />
                    </div>
                @endif

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Payment Date *</label>
                    <input type="date" wire:model="payment_date" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-colors" required />
                    @error('payment_date') <span class="text-xs text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Remarks</label>
                    <input type="text" wire:model="remarks" placeholder="Optional notes" class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 placeholder-gray-600 transition-colors" />
                </div>

                <button type="submit" wire:loading.attr="disabled" class="w-full inline-flex items-center justify-center gap-2 bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-300 border border-emerald-500/30 font-bold py-3 px-4 rounded-xl text-sm shadow-sm transition-all duration-200">
                    <span wire:loading.remove>Record Payment</span>
                    <span wire:loading class="animate-pulse">Processing...</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Employees List Table Card -->
    <div class="bg-gray-900/80 rounded-2xl border border-gray-800/80 shadow-2xl overflow-hidden">
        <div class="p-6 border-b border-gray-800/80">
            <h3 class="text-lg font-extrabold text-white flex items-center gap-2">
                <span>📋</span> Employees List & Advance Balance
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-800/80 text-sm">
                <thead class="bg-gray-950/50 text-gray-400 text-xs uppercase tracking-wider">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left font-bold">Name</th>
                        <th scope="col" class="px-6 py-4 text-left font-bold">Designation</th>
                        <th scope="col" class="px-6 py-4 text-left font-bold">Monthly Fixed</th>
                        <th scope="col" class="px-6 py-4 text-left font-bold">Total Advance Taken</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60 text-gray-300">
                    @forelse($employees as $emp)
                        <tr class="hover:bg-gray-800/40 transition-colors">
                            <td class="px-6 py-4 font-bold text-white whitespace-nowrap">
                                {{ $emp->name }}
                                @if($emp->phone)
                                    <span class="block text-xs font-normal text-gray-500 mt-0.5">{{ $emp->phone }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-800 text-gray-300 border border-gray-700/80">
                                    {{ $emp->designation }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-black text-emerald-400 whitespace-nowrap">
                                Rs. {{ number_format($emp->monthly_salary) }}
                            </td>
                            <td class="px-6 py-4 font-black text-amber-400 whitespace-nowrap">
                                Rs. {{ number_format($emp->totalAdvance()) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                <p class="text-base font-semibold">Abhi tak koi mulazim shamil nahi kiya gaya.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>