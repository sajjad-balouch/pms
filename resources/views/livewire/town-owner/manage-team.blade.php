<div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 bg-gray-950 text-gray-100 min-h-screen">
    @php
        $input = 'w-full bg-gray-950 border border-gray-800 text-white rounded-xl px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder-gray-600';
        $label = 'block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-1';
    @endphp

    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center bg-gray-900/80 backdrop-blur-md p-6 rounded-2xl border border-gray-800/80 shadow-2xl gap-4">
        <div>
            <h2 class="text-2xl font-black text-white tracking-tight flex items-center gap-2"><span>🧑‍💼</span> My Team</h2>
            <p class="text-xs text-gray-400 mt-1 font-medium">Recovery officer, accountant, sales staff — har ek ka apna login, sirf wohi sections jo aap dein. Har wusooli aur follow-up par un ka naam darj hota hai.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button type="button" wire:click="newMember" class="inline-flex items-center gap-2 bg-indigo-600/20 hover:bg-indigo-600/30 text-indigo-300 border border-indigo-500/30 px-4 py-2 rounded-xl text-xs font-bold">+ Add Team Member</button>
            <a href="{{ route('town_owner.dashboard') }}" wire:navigate class="inline-flex items-center gap-2 bg-gray-800 hover:bg-gray-700 text-gray-300 border border-gray-700/80 px-4 py-2 rounded-xl text-xs font-semibold">← Dashboard</a>
        </div>
    </div>

    @if (session('success'))
        <div wire:key="flash-{{ md5(session('success')) }}" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)" x-transition
             class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-5 py-3.5 rounded-xl text-sm font-medium">✅ {{ session('success') }}</div>
    @endif

    <!-- Add / Edit form -->
    @if ($editingId)
        <form wire:submit="saveMember" class="bg-gray-900/80 p-6 rounded-2xl border border-indigo-500/30 shadow-2xl space-y-5">
            <div class="flex justify-between items-start gap-3">
                <h3 class="text-lg font-bold text-white">{{ $editingId === 'new' ? '➕ New Team Member' : '✏️ Edit Team Member' }}</h3>
                <button type="button" wire:click="closeForm" class="text-xs text-rose-400 hover:text-rose-300 font-semibold bg-rose-500/10 border border-rose-500/20 px-3 py-1.5 rounded-xl">✕ Cancel</button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="{{ $label }}">Name *</label>
                    <input type="text" wire:model="name" class="{{ $input }}">
                    @error('name') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="{{ $label }}">Login Email *</label>
                    <input type="email" wire:model="email" autocomplete="off" class="{{ $input }}">
                    @error('email') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="{{ $label }}">Phone</label>
                    <input type="tel" wire:model="phone" placeholder="0300-1234567" class="{{ $input }}">
                    @error('phone') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="{{ $label }}">{{ $editingId === 'new' ? 'Password *' : 'New Password (optional)' }}</label>
                    <input type="password" wire:model="password" autocomplete="new-password" placeholder="{{ $editingId === 'new' ? 'Kam az kam 8' : 'Khali chhorein = wahi rahe' }}" class="{{ $input }}">
                    @error('password') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="{{ $label }} mb-0">Quick role:</span>
                    @foreach ($presets as $key => $preset)
                        <button type="button" wire:click="applyPreset('{{ $key }}')" class="px-3 py-1 rounded-lg text-xs font-bold bg-gray-800 hover:bg-gray-700 text-gray-300 border border-gray-700">{{ $preset['label'] }}</button>
                    @endforeach
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                    @foreach ($sections as $key => $text)
                        <label class="flex items-center gap-2 bg-gray-950 border border-gray-800 rounded-xl px-3 py-2 text-sm text-gray-300 cursor-pointer has-[:checked]:border-indigo-500/60 has-[:checked]:bg-indigo-500/10">
                            <input type="checkbox" wire:model="permissions" value="{{ $key }}" class="rounded bg-gray-900 border-gray-700 text-indigo-500 focus:ring-indigo-500">
                            {{ $text }}
                        </label>
                    @endforeach
                </div>
                @error('permissions') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                <p class="text-[11px] text-gray-500 mt-2">"Dashboard & Profit" dene se munafa, kharche aur salaries nazar aate hain — sirf bharose wale shakhs ko dein. Team manage karna sirf owner ka kaam hai.</p>
            </div>

            <div class="flex justify-end">
                <button type="submit" wire:loading.attr="disabled" wire:target="saveMember" class="bg-indigo-600/20 hover:bg-indigo-600/30 text-indigo-300 border border-indigo-500/30 font-bold py-2.5 px-6 rounded-xl text-sm disabled:opacity-50">Save</button>
            </div>
        </form>
    @endif

    <!-- Members -->
    <div class="bg-gray-900/80 rounded-2xl border border-gray-800/80 shadow-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-800/80 text-sm">
                <thead class="bg-gray-950/50 text-gray-400 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3 text-left font-bold">Member</th>
                        <th class="px-5 py-3 text-left font-bold">Access</th>
                        <th class="px-5 py-3 text-right font-bold">Is mahine wusooli</th>
                        <th class="px-5 py-3 text-right font-bold">Follow-ups</th>
                        <th class="px-5 py-3 text-right font-bold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60 text-gray-300">
                    @forelse ($members as $m)
                        @php $work = $collected->get($m->id); @endphp
                        <tr wire:key="member-{{ $m->id }}" class="hover:bg-gray-800/40 {{ $m->is_approved ? '' : 'opacity-50' }}">
                            <td class="px-5 py-3">
                                <p class="font-bold text-white">{{ $m->name }}
                                    @unless ($m->is_approved) <span class="text-[10px] font-bold text-rose-400 ml-1">LOGIN BAND</span> @endunless
                                </p>
                                <p class="text-xs text-gray-500">{{ $m->email }}{{ $m->phone ? ' · ' . $m->phone : '' }}</p>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex flex-wrap gap-1 max-w-md">
                                    @foreach ((array) $m->permissions as $p)
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-gray-800 text-gray-300 border border-gray-700">{{ $sections[$p] ?? $p }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-5 py-3 text-right whitespace-nowrap">
                                <span class="font-bold text-emerald-400">Rs. {{ number_format($work->total ?? 0) }}</span>
                                <span class="block text-[11px] text-gray-500">{{ $work->entries ?? 0 }} entries</span>
                            </td>
                            <td class="px-5 py-3 text-right font-bold text-indigo-400">{{ $followups[$m->id] ?? 0 }}</td>
                            <td class="px-5 py-3 text-right whitespace-nowrap space-x-1">
                                <button type="button" wire:click="editMember({{ $m->id }})" class="inline-flex items-center bg-amber-600/10 hover:bg-amber-600/20 text-amber-300 border border-amber-500/20 px-2.5 py-1.5 rounded-xl text-xs font-bold">✏️ Edit</button>
                                <button type="button" wire:click="toggleActive({{ $m->id }})" wire:confirm="{{ $m->is_approved ? $m->name . ' ka login band karein?' : $m->name . ' ka login dobara kholein?' }}"
                                    class="inline-flex items-center bg-gray-800 hover:bg-gray-700 text-gray-300 border border-gray-700 px-2.5 py-1.5 rounded-xl text-xs font-bold">{{ $m->is_approved ? '⏸ Disable' : '▶ Enable' }}</button>
                                <button type="button" wire:click="removeMember({{ $m->id }})" wire:confirm="{{ $m->name }} ko team se nikal dein? Un ki pichhli entries record mein rahengi." class="inline-flex items-center bg-rose-600/10 hover:bg-rose-600/20 text-rose-300 border border-rose-500/20 px-2.5 py-1.5 rounded-xl text-xs font-bold">🗑️</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <p class="text-base font-semibold">Abhi team mein koi nahi</p>
                            <p class="text-sm mt-1">"+ Add Team Member" se recovery officer ya accountant ka login banayein.</p>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
