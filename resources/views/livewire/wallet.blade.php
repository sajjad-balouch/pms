@php
    $input = 'w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2.5 text-sm text-white outline-none focus:border-amber-400';
    $label = 'mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-400';
    $typeLabels = [
        'deposit' => __('Top-up'), 'message_fee' => __('SMS charge'), 'unlock_fee' => __('Contact unlock'),
        'refund' => __('Refund'), 'adjustment' => __('Admin adjustment'),
    ];
    $statusTone = ['approved' => 'bg-emerald-500/15 text-emerald-300', 'pending' => 'bg-amber-500/15 text-amber-300', 'rejected' => 'bg-rose-500/15 text-rose-300'];
@endphp
<div class="py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 text-slate-100">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-2xl font-black text-white"><i class="fa-solid fa-wallet text-amber-400"></i> {{ __('My Wallet') }}</h1>
            <p class="text-sm text-slate-400">{{ __('Top up your wallet; SMS and other charges are deducted from this balance.') }}</p>
        </div>
        <button type="button" wire:click="openForm" class="rounded-xl bg-amber-400 px-5 py-2.5 text-sm font-black text-slate-950 hover:bg-amber-300">+ {{ __('Top Up') }}</button>
    </div>

    @if (session('success'))
        <div wire:key="ok-{{ md5(session('success')) }}" class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">{{ session('success') }}</div>
    @endif

    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-amber-500/30 bg-amber-500/10 p-5">
            <p class="{{ $label }}">{{ __('Balance') }}</p>
            <p class="text-3xl font-black text-amber-300">Rs. {{ number_format((float) $account->wallet_balance, 2) }}</p>
        </div>
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
            <p class="{{ $label }}">{{ __('Waiting for approval') }}</p>
            <p class="text-2xl font-black text-white">Rs. {{ number_format((float) $pending->sum('amount')) }}</p>
            <p class="text-xs text-slate-400">{{ trans_choice(':count request|:count requests', $pending->count(), ['count' => $pending->count()]) }}</p>
        </div>
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
            <p class="{{ $label }}">{{ __('SMS this month') }}</p>
            <p class="text-2xl font-black text-white">{{ (int) ($smsMonth->n ?? 0) }} <span class="text-sm font-bold text-slate-400">· Rs. {{ number_format((float) ($smsMonth->total ?? 0), 2) }}</span></p>
            @if ($smsRate !== null)
                <p class="text-xs text-slate-400">{{ __('Rate: Rs. :rate per SMS', ['rate' => rtrim(rtrim(number_format($smsRate, 2), '0'), '.')]) }}</p>
            @endif
        </div>
    </div>

    @if ((float) $account->wallet_balance <= 0 && $smsRate !== null)
        <div class="rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">{{ __('Your balance is empty, so messages to customers will not be sent. Please top up.') }}</div>
    @endif

    @if ($showForm)
        <form wire:submit="requestTopUp" class="space-y-4 rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
            <div class="flex items-center justify-between">
                <h2 class="font-black text-white">{{ __('Top-up request') }}</h2>
                <button type="button" wire:click="$set('showForm', false)" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <ol class="list-decimal space-y-1 ps-5 text-xs text-slate-400">
                <li>{{ __('Send the money to one of the accounts below.') }}</li>
                <li>{{ __('Fill in the amount and your account details, and attach the payment screenshot.') }}</li>
                <li>{{ __('After the admin checks it, the amount is added to your wallet.') }}</li>
            </ol>

            <div class="space-y-2">
                <span class="{{ $label }}">{{ __('Payment method') }}</span>
                @forelse ($methods as $pm)
                    <label wire:key="pm-{{ $pm->id }}" class="block cursor-pointer rounded-xl border p-3 {{ (string) $methodId === (string) $pm->id ? 'border-amber-400 bg-amber-500/10' : 'border-slate-700 bg-slate-950' }}">
                        <div class="flex items-start gap-3">
                            <input type="radio" wire:model.live="methodId" value="{{ $pm->id }}" class="mt-1 text-amber-500">
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-white">{{ $pm->method_name }}</p>
                                <p class="text-xs font-semibold text-amber-300 select-all">{{ $pm->account_title }} — {{ $pm->account_number }}</p>
                                @if ($pm->instructions)<p class="mt-1 text-[11px] text-slate-400">{{ $pm->instructions }}</p>@endif
                            </div>
                        </div>
                    </label>
                @empty
                    <p class="rounded-xl border border-slate-700 bg-slate-950 p-3 text-sm text-slate-400">{{ __('No payment method has been added yet. Please contact the admin.') }}</p>
                @endforelse
                @error('methodId')<p class="text-xs text-rose-400">{{ $message }}</p>@enderror
            </div>

            <div class="grid gap-3 sm:grid-cols-3">
                <div>
                    <label class="{{ $label }}">{{ __('Amount (Rs.)') }}</label>
                    <input type="number" min="100" step="1" wire:model="amount" class="{{ $input }}">
                    @error('amount')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="{{ $label }}">{{ __('Sent from (account title)') }}</label>
                    <input wire:model="senderTitle" class="{{ $input }}" placeholder="Muhammad Ali">
                    @error('senderTitle')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="{{ $label }}">{{ __('Account / mobile number') }}</label>
                    <input wire:model="senderNumber" inputmode="tel" class="{{ $input }}" placeholder="03001234567">
                    @error('senderNumber')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="{{ $label }}">{{ __('Payment screenshot') }}</label>
                <input type="file" wire:model="screenshot" accept="image/*" class="block w-full text-sm text-slate-300 file:me-3 file:rounded-lg file:border-0 file:bg-slate-800 file:px-3 file:py-2 file:text-sm file:font-bold file:text-white">
                <div wire:loading wire:target="screenshot" class="mt-1 text-xs text-slate-400">{{ __('Uploading...') }}</div>
                @if ($screenshot && method_exists($screenshot, 'isPreviewable') && $screenshot->isPreviewable())
                    <img src="{{ $screenshot->temporaryUrl() }}" alt="" class="mt-2 h-32 rounded-lg border border-slate-700 object-contain">
                @endif
                @error('screenshot')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
            </div>

            <button type="submit" wire:loading.attr="disabled" class="w-full rounded-xl bg-amber-400 py-3 text-sm font-black text-slate-950 hover:bg-amber-300 disabled:opacity-50 sm:w-auto sm:px-10">{{ __('Send request') }}</button>
        </form>
    @endif

    <section class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/70">
        <h2 class="border-b border-slate-800 px-5 py-4 font-black text-white">{{ __('History') }}</h2>
        <div class="divide-y divide-slate-800">
            @forelse ($transactions as $tx)
                @php
                    $credit = in_array($tx->type, ['deposit', 'refund'], true) || ($tx->type === 'adjustment' && $tx->admin_note !== 'debit');
                    $counts = $tx->status === 'approved';
                @endphp
                <div wire:key="tx-{{ $tx->id }}" class="flex flex-wrap items-center justify-between gap-3 px-5 py-3 text-sm">
                    <div class="min-w-0">
                        <p class="font-bold text-white">
                            {{ $typeLabels[$tx->type] ?? ucfirst($tx->type) }}
                            <span class="ms-1 rounded-full px-2 py-0.5 text-[10px] font-bold {{ $statusTone[$tx->status] ?? 'bg-slate-700 text-slate-300' }}">{{ __(ucfirst($tx->status)) }}</span>
                        </p>
                        <p class="truncate text-xs text-slate-400">{{ $tx->description }} · {{ $tx->created_at->format('d M Y, h:i A') }}</p>
                        @if ($tx->status === 'rejected' && $tx->admin_note)
                            <p class="text-xs text-rose-300">{{ __('Reason') }}: {{ $tx->admin_note }}</p>
                        @endif
                    </div>
                    <p class="font-black {{ $tx->status === 'rejected' ? 'text-slate-500 line-through' : (! $counts ? 'text-amber-300' : ($credit ? 'text-emerald-400' : 'text-rose-400')) }}">
                        {{ $credit ? '+' : '−' }} Rs. {{ number_format((float) $tx->amount, 2) }}
                    </p>
                </div>
            @empty
                <p class="px-5 py-8 text-center text-sm text-slate-500">{{ __('No transactions yet.') }}</p>
            @endforelse
        </div>
        @if ($transactions->hasPages())
            <div class="border-t border-slate-800 px-5 py-3">{{ $transactions->links() }}</div>
        @endif
    </section>
</div>
