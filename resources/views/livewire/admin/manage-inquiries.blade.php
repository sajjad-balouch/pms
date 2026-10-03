<div class="p-6 bg-slate-900 text-slate-100 min-h-screen">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-white">Contact Inquiries</h2>
        @if (session()->has('success'))
            <div class="bg-emerald-500/10 border border-emerald-500 text-emerald-400 px-4 py-2 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif
    </div>

    <!-- Filters & Search -->
    <div class="flex flex-col md:flex-row gap-4 mb-6">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by name, email, or subject..." class="bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-lg p-2.5 w-full md:w-1/3 focus:outline-none focus:border-indigo-500">

        <select wire:model.live="statusFilter" class="bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-lg p-2.5 w-full md:w-1/4 focus:outline-none focus:border-indigo-500">
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="in_progress">In Progress</option>
            <option value="resolved">Resolved</option>
            <option value="closed">Closed</option>
        </select>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto bg-slate-800 rounded-xl border border-slate-700">
        <table class="w-full text-sm text-left text-slate-300">
            <thead class="text-xs uppercase bg-slate-700/50 text-slate-400">
                <tr>
                    <th class="px-6 py-3">User</th>
                    <th class="px-6 py-3">Subject</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Date</th>
                    <th class="px-6 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700">
                @forelse ($inquiries as $inquiry)
                    <tr class="hover:bg-slate-700/30 transition">
                        <td class="px-6 py-4">
                            <div class="font-medium text-white">{{ $inquiry->name }}</div>
                            <div class="text-xs text-slate-400">{{ $inquiry->email }}</div>
                        </td>
                        <td class="px-6 py-4">{{ $inquiry->subject ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            @php
                                $badgeClasses = [
                                    'pending' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                    'in_progress' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                    'resolved' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                    'closed' => 'bg-slate-500/10 text-slate-400 border-slate-500/20',
                                ][$inquiry->status] ?? 'bg-slate-500/10 text-slate-400';
                            @endphp
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full border {{ $badgeClasses }}">
                                {{ ucfirst(str_replace('_', ' ', $inquiry->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-400">
                            {{ $inquiry->created_at->format('d M Y, h:i A') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button wire:click="viewInquiry({{ $inquiry->id }})" class="px-3 py-1 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-xs font-medium transition">
                                View / Edit
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                            No contact inquiries found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $inquiries->links() }}
    </div>

    <!-- View & Update Status Modal -->
    @if ($selectedInquiry)
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-slate-800 border border-slate-700 rounded-xl p-6 w-full max-w-lg shadow-xl text-slate-200">
                <div class="flex justify-between items-center mb-4 border-b border-slate-700 pb-3">
                    <h3 class="text-lg font-bold text-white">Inquiry Details</h3>
                    <button wire:click="$set('selectedInquiry', null)" class="text-slate-400 hover:text-white">&times;</button>
                </div>

                <div class="space-y-3 mb-6">
                    <div>
                        <span class="text-xs text-slate-400 block">From:</span>
                        <p class="font-medium text-white">{{ $selectedInquiry->name }} ({{ $selectedInquiry->email }})</p>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400 block">Subject:</span>
                        <p class="font-medium text-white">{{ $selectedInquiry->subject ?? 'No Subject' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400 block">Message:</span>
                        <div class="p-3 bg-slate-900/60 rounded-lg text-sm text-slate-300 mt-1 max-h-40 overflow-y-auto">
                            {{ $selectedInquiry->message }}
                        </div>
                    </div>
                    <div>
                        <label class="text-xs text-slate-400 block mb-1">Update Status:</label>
                        <select wire:model="newStatus" class="bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-lg p-2.5 w-full focus:outline-none focus:border-indigo-500">
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="resolved">Resolved</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button wire:click="$set('selectedInquiry', null)" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 rounded-lg text-sm text-slate-200">Cancel</button>
                    <button wire:click="updateStatus" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 rounded-lg text-sm font-medium text-white">Save Changes</button>
                </div>
            </div>
        </div>
    @endif
</div>