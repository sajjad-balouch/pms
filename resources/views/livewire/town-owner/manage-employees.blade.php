<div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 bg-gray-950 text-gray-100 min-h-screen">
    @php
        $input = 'w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder-gray-600 transition-colors';
        $label = 'block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1';
    @endphp

    <!-- Top Navigation Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center bg-gray-900/80 backdrop-blur-md p-6 rounded-2xl border border-gray-800/80 shadow-2xl gap-4">
        <div>
            <h2 class="text-2xl font-black text-white tracking-tight flex items-center gap-2">
                <span>👷</span> Employees & Salary Management{{ $town ? ' — ' . $town->name : '' }}
            </h2>
            <p class="text-xs text-gray-400 mt-1 font-medium">Mulazmeen ke kawaif, tankhwah aur advance ka hisaab</p>
        </div>
        <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
            @if ($towns->count() > 1)
                <select wire:model.live="townId" class="bg-gray-950 border border-gray-700 text-gray-200 rounded-xl px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @foreach ($towns as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>
            @endif
            <a href="{{ route('town_owner.dashboard') }}" wire:navigate class="inline-flex items-center gap-2 bg-gray-800 hover:bg-gray-700 text-gray-300 border border-gray-700/80 px-4 py-2 rounded-xl text-xs font-semibold transition-all duration-200">
                <span>←</span> Dashboard
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session('success'))
        <div wire:key="flash-success-{{ md5(session('success')) }}" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)" x-transition
             class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-5 py-3.5 rounded-xl text-sm font-medium flex items-center justify-between">
            <div class="flex items-center gap-3"><span>✅</span><span>{{ session('success') }}</span></div>
            <button type="button" @click="show = false" class="text-emerald-400/60 hover:text-emerald-400">&times;</button>
        </div>
    @endif

    @if (session('error'))
        <div wire:key="flash-error-{{ md5(session('error')) }}" x-data="{ show: true }" x-show="show" x-transition
             class="bg-rose-500/10 border border-rose-500/30 text-rose-400 px-5 py-3.5 rounded-xl text-sm font-medium flex items-center justify-between">
            <div class="flex items-center gap-3"><span>⛔</span><span>{{ session('error') }}</span></div>
            <button type="button" @click="show = false" class="text-rose-400/60 hover:text-rose-400">&times;</button>
        </div>
    @endif

    @if (! $town)
        <div class="bg-gray-900/80 p-10 rounded-2xl border border-gray-800/80 text-center">
            <p class="text-lg font-bold text-white">Pehle koi housing scheme banayein</p>
            <p class="text-sm text-gray-400 mt-1">Mulazmeen kisi scheme ke saath jode jate hain.</p>
            <a href="{{ route('town_owner.towns') }}" wire:navigate class="inline-block mt-4 text-indigo-400 font-bold hover:underline">+ Add New Scheme</a>
        </div>
    @else
        <!-- Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-gray-900/60 p-5 rounded-2xl border border-gray-800/80 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1 h-full bg-indigo-500"></div>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Active Employees</p>
                <p class="text-3xl font-black text-white mt-1">{{ $stats['active'] }}</p>
                @if ($stats['inactive'])
                    <p class="text-[11px] text-gray-500 mt-1">{{ $stats['inactive'] }} inactive</p>
                @endif
            </div>
            <div class="bg-gray-900/60 p-5 rounded-2xl border border-gray-800/80 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1 h-full bg-sky-500"></div>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Monthly Payroll</p>
                <p class="text-2xl font-black text-sky-400 mt-1">Rs. {{ number_format($stats['payroll']) }}</p>
            </div>
            <div class="bg-gray-900/60 p-5 rounded-2xl border border-gray-800/80 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1 h-full bg-emerald-500"></div>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400">{{ $monthLabel($month) }}</p>
                <p class="text-2xl font-black text-emerald-400 mt-1">Rs. {{ number_format($stats['paid']) }}</p>
                <p class="text-[11px] {{ $stats['pending'] > 0 ? 'text-rose-400' : 'text-gray-500' }} mt-1">Pending: Rs. {{ number_format($stats['pending']) }}</p>
            </div>
            <div class="bg-gray-900/60 p-5 rounded-2xl border border-gray-800/80 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1 h-full bg-amber-500"></div>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Advance Outstanding</p>
                <p class="text-2xl font-black text-amber-400 mt-1">Rs. {{ number_format($stats['advances']) }}</p>
                <p class="text-[11px] text-gray-500 mt-1">Salary se adjust hona baqi</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Add / Edit Employee Form Card -->
            <div x-on:employee-form-opened.window="$el.scrollIntoView({ behavior: 'smooth', block: 'start' })"
                 class="bg-gray-900/80 backdrop-blur-md p-6 rounded-2xl border {{ $editingEmployeeId ? 'border-amber-500/40' : 'border-gray-800/80' }} shadow-2xl space-y-5 h-fit scroll-mt-6">
                <div class="border-b border-gray-800/80 pb-4 flex items-start justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <span>{{ $editingEmployeeId ? '✏️' : '➕' }}</span> {{ $editingEmployeeId ? 'Edit Employee' : 'Add New Employee' }}
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $editingEmployeeId ? 'Salary badalne se purani payments par asar nahi parta.' : 'Naye mulazim ke kawaif darj karein.' }}</p>
                    </div>
                    @if ($editingEmployeeId)
                        <button type="button" wire:click="cancelEdit" class="text-xs text-rose-400 hover:text-rose-300 font-semibold bg-rose-500/10 border border-rose-500/20 px-3 py-1.5 rounded-xl">✕ Cancel</button>
                    @endif
                </div>

                <form wire:submit="saveEmployee" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="{{ $label }}">Name *</label>
                            <input type="text" wire:model="name" placeholder="e.g. Muhammad Ahmad" class="{{ $input }}" required />
                            @error('name') <span class="text-xs text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="{{ $label }}">Phone</label>
                            <input type="tel" wire:model="phone" placeholder="0300-1234567" class="{{ $input }}" />
                            @error('phone') <span class="text-xs text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="{{ $label }}">Designation *</label>
                            <input type="text" wire:model="designation" list="designation-options" placeholder="e.g. Guard, Manager" class="{{ $input }}" required />
                            <datalist id="designation-options">
                                @foreach (['Manager', 'Accountant', 'Sales Officer', 'Recovery Officer', 'Site Supervisor', 'Guard', 'Driver', 'Gardener', 'Sweeper', 'Electrician', 'Plumber'] as $d)
                                    <option value="{{ $d }}"></option>
                                @endforeach
                            </datalist>
                            @error('designation') <span class="text-xs text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="{{ $label }}">Monthly Fixed Salary *</label>
                            <input type="number" min="0" step="1" wire:model="monthly_salary" placeholder="35000" class="{{ $input }}" required />
                            @error('monthly_salary') <span class="text-xs text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <button type="submit" wire:loading.attr="disabled" wire:target="saveEmployee" class="w-full inline-flex items-center justify-center gap-2 {{ $editingEmployeeId ? 'bg-amber-600/20 hover:bg-amber-600/30 text-amber-300 border-amber-500/30' : 'bg-indigo-600/20 hover:bg-indigo-600/30 text-indigo-300 border-indigo-500/30' }} border font-bold py-3 px-4 rounded-xl text-sm transition-all duration-200 disabled:opacity-50">
                        <span wire:loading.remove wire:target="saveEmployee">{{ $editingEmployeeId ? 'Update Employee' : 'Save Employee' }}</span>
                        <span wire:loading wire:target="saveEmployee" class="animate-pulse">Saving...</span>
                    </button>
                </form>
            </div>

            <!-- Record Salary or Advance Payment Card -->
            <div x-on:payment-form-opened.window="$el.scrollIntoView({ behavior: 'smooth', block: 'start' })"
                 class="bg-gray-900/80 backdrop-blur-md p-6 rounded-2xl border border-gray-800/80 shadow-2xl space-y-5 h-fit scroll-mt-6">
                <div class="border-b border-gray-800/80 pb-4">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2"><span>💵</span> Pay Salary / Advance</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Salary ya advance ki adayegi ka record banayein. Baqi salary khud bhar jati hai.</p>
                </div>

                <form wire:submit="recordPayment" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="{{ $label }}">Select Employee *</label>
                            <select wire:model.live="selected_employee_id" class="{{ $input }}" required>
                                <option value="">Select Employee</option>
                                @foreach ($activeEmployees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->designation }}) — Rs. {{ number_format($emp->monthly_salary) }}</option>
                                @endforeach
                            </select>
                            @error('selected_employee_id') <span class="text-xs text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="{{ $label }}">Payment Type *</label>
                            <select wire:model.live="payment_type" class="{{ $input }}">
                                <option value="salary">Monthly Salary</option>
                                <option value="advance">Advance Payment</option>
                            </select>
                        </div>

                        @if ($payment_type === 'salary')
                            <div>
                                <label class="{{ $label }}">Salary Month *</label>
                                <input type="month" wire:model.live="salary_month" class="{{ $input }} [color-scheme:dark]" />
                                @error('salary_month') <span class="text-xs text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        @if ($selectedInfo)
                            <div class="sm:col-span-2 grid grid-cols-3 gap-2 text-center bg-gray-950/60 border border-gray-800 rounded-xl p-3 text-xs">
                                <div>
                                    <p class="text-gray-500 font-bold uppercase">Fixed</p>
                                    <p class="font-black text-white">Rs. {{ number_format($selectedInfo['employee']->monthly_salary) }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 font-bold uppercase">Already Paid</p>
                                    <p class="font-black text-emerald-400">{{ is_null($selectedInfo['paid_for_month']) ? '—' : 'Rs. ' . number_format($selectedInfo['paid_for_month']) }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 font-bold uppercase">Advance Bal.</p>
                                    <p class="font-black text-amber-400">Rs. {{ number_format($selectedInfo['advance_balance']) }}</p>
                                </div>
                            </div>
                            @if ($payment_type === 'salary' && $selectedInfo['paid_for_month'] >= (float) $selectedInfo['employee']->monthly_salary && $selectedInfo['paid_for_month'] > 0)
                                <p class="sm:col-span-2 text-xs text-amber-400">⚠️ Is mahine ki poori salary pehle hi di ja chuki hai. Bonus/overtime ho to remarks mein likhein.</p>
                            @endif
                        @endif

                        @if ($payment_type === 'salary' && $selectedInfo && $selectedInfo['advance_balance'] > 0)
                            <div>
                                <label class="{{ $label }}">Advance se Katoti (Rs)</label>
                                <input type="number" min="0" step="1" max="{{ $selectedInfo['advance_balance'] }}" wire:model.live.debounce.400ms="advance_deduction" class="{{ $input }}" />
                                @error('advance_deduction') <span class="text-xs text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        <div>
                            <label class="{{ $label }}">{{ $payment_type === 'salary' ? 'Cash Paid (Rs) *' : 'Advance Amount (Rs) *' }}</label>
                            <input type="number" min="0" step="1" wire:model="amount" placeholder="25000" class="{{ $input }}" required />
                            @error('amount') <span class="text-xs text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="{{ $label }}">Payment Date *</label>
                            <input type="date" wire:model="payment_date" class="{{ $input }} [color-scheme:dark]" required />
                            @error('payment_date') <span class="text-xs text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="{{ $label }}">Remarks</label>
                            <input type="text" wire:model="remarks" maxlength="255" placeholder="Optional notes" class="{{ $input }}" />
                            @error('remarks') <span class="text-xs text-rose-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <button type="submit" wire:loading.attr="disabled" wire:target="recordPayment" class="w-full inline-flex items-center justify-center gap-2 bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-300 border border-emerald-500/30 font-bold py-3 px-4 rounded-xl text-sm transition-all duration-200 disabled:opacity-50">
                        <span wire:loading.remove wire:target="recordPayment">Record Payment</span>
                        <span wire:loading wire:target="recordPayment" class="animate-pulse">Processing...</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Payroll / Employees Table -->
        <div class="bg-gray-900/80 rounded-2xl border border-gray-800/80 shadow-2xl overflow-hidden">
            <div class="p-6 border-b border-gray-800/80 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <h3 class="text-lg font-extrabold text-white flex items-center gap-2"><span>📋</span> Payroll — {{ $monthLabel($month) }}</h3>
                <div class="flex flex-wrap gap-3 w-full md:w-auto">
                    <input type="month" wire:model.live="payrollMonth" class="bg-gray-950 border border-gray-800 text-gray-200 rounded-xl px-3 py-2 text-sm [color-scheme:dark]">
                    <div class="relative flex-1 md:w-64">
                        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Naam, designation, phone..."
                            class="w-full bg-gray-950 border border-gray-800 text-white rounded-xl pl-9 pr-4 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder-gray-600">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">🔍</span>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-800/80 text-sm">
                    <thead class="bg-gray-950/50 text-gray-400 text-xs uppercase tracking-wider">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left font-bold">Name</th>
                            <th scope="col" class="px-6 py-4 text-left font-bold">Designation</th>
                            <th scope="col" class="px-6 py-4 text-left font-bold">Monthly Fixed</th>
                            <th scope="col" class="px-6 py-4 text-left font-bold">This Month</th>
                            <th scope="col" class="px-6 py-4 text-left font-bold">Advance Balance</th>
                            <th scope="col" class="px-6 py-4 text-right font-bold">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/60 text-gray-300">
                        @forelse ($employees as $emp)
                            @php
                                $isActive = $emp->status === 'active';
                                $paidPct = $emp->monthly_salary > 0 ? min(100, round($emp->month_paid / $emp->monthly_salary * 100)) : 0;
                                [$badgeText, $badgeClass] = match (true) {
                                    ! $isActive => ['Inactive', 'bg-gray-800 text-gray-400 border-gray-700'],
                                    $emp->month_pending <= 0 => ['Paid', 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20'],
                                    $emp->month_paid > 0 => ['Partial', 'bg-amber-500/10 text-amber-400 border-amber-500/20'],
                                    default => ['Pending', 'bg-rose-500/10 text-rose-400 border-rose-500/20'],
                                };
                            @endphp
                            <tr wire:key="emp-{{ $emp->id }}" class="hover:bg-gray-800/40 transition-colors {{ $isActive ? '' : 'opacity-60' }} {{ $editingEmployeeId == $emp->id ? 'bg-amber-500/10' : '' }}">
                                <td class="px-6 py-4 font-bold text-white whitespace-nowrap">
                                    {{ $emp->name }}
                                    @if ($emp->phone)
                                        <a href="tel:{{ $emp->phone }}" class="block text-xs font-normal text-gray-500 hover:text-gray-300 mt-0.5">{{ $emp->phone }}</a>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-800 text-gray-300 border border-gray-700/80">{{ $emp->designation }}</span>
                                </td>
                                <td class="px-6 py-4 font-black text-emerald-400 whitespace-nowrap">Rs. {{ number_format($emp->monthly_salary) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap min-w-[160px]">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold border {{ $badgeClass }}">{{ $badgeText }}</span>
                                        <span class="text-xs text-gray-400">Rs. {{ number_format($emp->month_paid) }}</span>
                                    </div>
                                    @if ($isActive)
                                        <div class="mt-1.5 h-1 w-full bg-gray-800 rounded-full overflow-hidden">
                                            <div class="h-full bg-emerald-500" style="width: {{ $paidPct }}%"></div>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-black whitespace-nowrap {{ $emp->advance_balance > 0 ? 'text-amber-400' : 'text-gray-500' }}">
                                    Rs. {{ number_format($emp->advance_balance) }}
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap space-x-1">
                                    @if ($isActive && $emp->month_pending > 0)
                                        <button type="button" wire:click="payEmployee({{ $emp->id }})" class="inline-flex items-center gap-1 bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-300 border border-emerald-500/30 px-3 py-1.5 rounded-xl text-xs font-bold">💵 Pay</button>
                                    @endif
                                    <button type="button" wire:click="showHistory({{ $emp->id }})" title="Payment history" class="inline-flex items-center bg-sky-600/10 hover:bg-sky-600/20 text-sky-300 border border-sky-500/20 px-2.5 py-1.5 rounded-xl text-xs font-bold">📜</button>
                                    <button type="button" wire:click="editEmployee({{ $emp->id }})" title="Edit" class="inline-flex items-center bg-amber-600/10 hover:bg-amber-600/20 text-amber-300 border border-amber-500/20 px-2.5 py-1.5 rounded-xl text-xs font-bold">✏️</button>
                                    <button type="button" wire:click="toggleStatus({{ $emp->id }})"
                                        wire:confirm="{{ $isActive ? $emp->name . ' ko Inactive (naukri khatam) karein?' : $emp->name . ' ko dobara Active karein?' }}"
                                        title="{{ $isActive ? 'Deactivate' : 'Activate' }}"
                                        class="inline-flex items-center bg-gray-800 hover:bg-gray-700 text-gray-300 border border-gray-700 px-2.5 py-1.5 rounded-xl text-xs font-bold">{{ $isActive ? '⏸' : '▶' }}</button>
                                    @if (! $emp->advances_total && ! $emp->month_paid && ! $emp->deductions_total)
                                        <button type="button" wire:click="deleteEmployee({{ $emp->id }})" wire:confirm="{{ $emp->name }} ko delete karein?" title="Delete" class="inline-flex items-center bg-rose-600/10 hover:bg-rose-600/20 text-rose-300 border border-rose-500/20 px-2.5 py-1.5 rounded-xl text-xs font-bold">🗑️</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <p class="text-base font-semibold">{{ trim($search) !== '' ? 'Koi mulazim nahi mila.' : 'Abhi tak koi mulazim shamil nahi kiya gaya.' }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Payments -->
        <div class="bg-gray-900/80 rounded-2xl border border-gray-800/80 shadow-2xl overflow-hidden">
            <div class="p-6 border-b border-gray-800/80">
                <h3 class="text-lg font-extrabold text-white flex items-center gap-2"><span>🧾</span> Recent Payments</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-800/80 text-sm">
                    <thead class="bg-gray-950/50 text-gray-400 text-xs uppercase tracking-wider">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left font-bold">Date</th>
                            <th scope="col" class="px-6 py-3 text-left font-bold">Employee</th>
                            <th scope="col" class="px-6 py-3 text-left font-bold">Type</th>
                            <th scope="col" class="px-6 py-3 text-right font-bold">Cash</th>
                            <th scope="col" class="px-6 py-3 text-left font-bold">Remarks</th>
                            <th scope="col" class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/60 text-gray-300">
                        @forelse ($recentPayments as $p)
                            <tr wire:key="pay-{{ $p->id }}" class="hover:bg-gray-800/40">
                                <td class="px-6 py-3 whitespace-nowrap text-gray-400">{{ \Carbon\Carbon::parse($p->payment_date)->format('d M Y') }}</td>
                                <td class="px-6 py-3 whitespace-nowrap font-semibold text-white">{{ $p->employee?->name }}</td>
                                <td class="px-6 py-3 whitespace-nowrap">
                                    @if ($p->type === 'advance')
                                        <span class="text-xs font-bold text-amber-400">Advance</span>
                                    @else
                                        <span class="text-xs font-bold text-emerald-400">Salary</span>
                                        <span class="text-xs text-gray-500">{{ $monthLabel($p->salary_month) }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-right font-bold">
                                    Rs. {{ number_format($p->amount) }}
                                    @if ($p->advance_deduction > 0)
                                        <span class="block text-[11px] font-normal text-amber-400">+ Rs. {{ number_format($p->advance_deduction) }} advance adjust</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-xs text-gray-500 max-w-[220px] truncate">{{ $p->remarks }}</td>
                                <td class="px-6 py-3 text-right">
                                    <button type="button" wire:click="deletePayment({{ $p->id }})" wire:confirm="Ye payment entry (Rs. {{ number_format($p->amount) }}) delete karein?" title="Delete entry" class="text-rose-400/70 hover:text-rose-400 text-xs">🗑️</button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">Abhi koi payment record nahi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Employee History Modal -->
    @if ($history)
        @php
            $e = $history['employee'];
            $totalSalary = $history['payments']->where('type', 'salary')->sum(fn ($p) => $p->amount + $p->advance_deduction);
            $totalAdvance = $history['payments']->where('type', 'advance')->sum('amount');
        @endphp
        <div class="fixed inset-0 z-50 flex items-start sm:items-center justify-center p-4 overflow-y-auto" x-data x-on:keydown.escape.window="$wire.closeHistory()">
            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" wire:click="closeHistory"></div>
            <div class="relative w-full max-w-2xl bg-gray-900 border border-gray-800 rounded-2xl shadow-2xl my-8">
                <div class="flex items-start justify-between gap-4 p-6 border-b border-gray-800">
                    <div>
                        <h3 class="text-lg font-black text-white">📜 {{ $e->name }}</h3>
                        <p class="text-sm text-gray-400">{{ $e->designation }} · Rs. {{ number_format($e->monthly_salary) }}/month</p>
                    </div>
                    <button type="button" wire:click="closeHistory" class="h-8 w-8 rounded-full bg-gray-800 hover:bg-gray-700 text-gray-300">✕</button>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div class="bg-gray-950/60 border border-gray-800 rounded-xl p-3"><p class="text-[11px] text-gray-500 uppercase font-bold">Total Salary</p><p class="font-black text-emerald-400">Rs. {{ number_format($totalSalary) }}</p></div>
                        <div class="bg-gray-950/60 border border-gray-800 rounded-xl p-3"><p class="text-[11px] text-gray-500 uppercase font-bold">Total Advance</p><p class="font-black text-amber-400">Rs. {{ number_format($totalAdvance) }}</p></div>
                        <div class="bg-gray-950/60 border border-gray-800 rounded-xl p-3"><p class="text-[11px] text-gray-500 uppercase font-bold">Advance Balance</p><p class="font-black text-rose-400">Rs. {{ number_format($e->advance_balance) }}</p></div>
                    </div>
                    <div class="max-h-96 overflow-y-auto border border-gray-800 rounded-xl">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-950/50 text-gray-400 text-xs uppercase sticky top-0">
                                <tr>
                                    <th class="px-4 py-2 text-left">Date</th>
                                    <th class="px-4 py-2 text-left">Type</th>
                                    <th class="px-4 py-2 text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-800/60 text-gray-300">
                                @forelse ($history['payments'] as $p)
                                    <tr>
                                        <td class="px-4 py-2 text-gray-400 whitespace-nowrap">{{ \Carbon\Carbon::parse($p->payment_date)->format('d M Y') }}</td>
                                        <td class="px-4 py-2">
                                            {{ $p->type === 'advance' ? 'Advance' : 'Salary — ' . $monthLabel($p->salary_month) }}
                                            @if ($p->remarks) <span class="block text-[11px] text-gray-500">{{ $p->remarks }}</span> @endif
                                        </td>
                                        <td class="px-4 py-2 text-right whitespace-nowrap">
                                            <span class="font-bold {{ $p->type === 'advance' ? 'text-amber-400' : 'text-emerald-400' }}">Rs. {{ number_format($p->amount) }}</span>
                                            @if ($p->advance_deduction > 0)
                                                <span class="block text-[11px] text-amber-400">+ {{ number_format($p->advance_deduction) }} adjust</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="px-4 py-6 text-center text-gray-500">Koi payment record nahi.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
