@php
    $input = 'w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-white outline-none focus:border-emerald-500';
    $label = 'mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-400';
    $statusTone = ['sent' => 'bg-emerald-500/15 text-emerald-300', 'failed' => 'bg-red-500/15 text-red-300', 'limited' => 'bg-amber-500/15 text-amber-300', 'link' => 'bg-slate-700 text-slate-200',
        'queued' => 'bg-sky-500/15 text-sky-300', 'sending' => 'bg-indigo-500/15 text-indigo-300', 'no_balance' => 'bg-amber-500/15 text-amber-300', 'cancelled' => 'bg-slate-700 text-slate-300'];
@endphp
<div class="space-y-6 text-slate-100">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-black text-white"><i class="fa-brands fa-whatsapp text-emerald-400"></i> Messaging (WhatsApp / SMS)</h1>
            <p class="text-sm text-slate-400">Town owners ke customers ko jaane wale paighamat. Naya provider lagana ho to sirf yahan setting badlein, code nahi.</p>
        </div>
        <span class="rounded-full px-3 py-1 text-xs font-bold {{ $isAutomatic ? 'bg-emerald-500/15 text-emerald-300' : 'bg-slate-800 text-slate-300' }}">
            {{ $isAutomatic ? 'Khudkaar bhejna ON' : 'Sirf WhatsApp link' }}
        </span>
    </div>

    @if (session('success'))
        <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">{{ session('success') }}</div>
    @endif

    <form wire:submit="save" class="space-y-6">
        {{-- 1. Tareeqa --}}
        <section class="space-y-4 rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
            <h2 class="font-black text-white">1. Bhejne ka tareeqa</h2>
            <div class="grid gap-2 md:grid-cols-2 xl:grid-cols-4">
                @foreach ($drivers as $key => $name)
                    <label class="flex cursor-pointer items-start gap-2 rounded-xl border p-3 text-sm {{ $driver === $key ? 'border-emerald-500 bg-emerald-500/10' : 'border-slate-700 bg-slate-950' }}">
                        <input type="radio" wire:model.live="driver" value="{{ $key }}" class="mt-1 text-emerald-500">
                        <span>{{ $name }}</span>
                    </label>
                @endforeach
            </div>

            @if ($driver === 'link')
                <p class="text-sm text-slate-400">Owner ke button par WhatsApp tayyar paigham ke saath khulta hai aur owner khud "Send" dabata hai. Koi kharcha nahi.</p>
            @endif

            @if ($driver === 'whatsapp_cloud')
                <div class="grid gap-3 md:grid-cols-2">
                    <div><label class="{{ $label }}">Phone number ID</label><input wire:model="config.phone_number_id" class="{{ $input }}" placeholder="1234567890">@error('config.phone_number_id')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror</div>
                    <div><label class="{{ $label }}">API version</label><input wire:model="config.api_version" class="{{ $input }}" placeholder="v21.0"></div>
                    <div><label class="{{ $label }}">Template language (default)</label><input wire:model="config.language" class="{{ $input }}" placeholder="en / ur"></div>
                </div>
                <p class="text-xs text-amber-300">Meta ke qanoon ke mutabiq customer ko pehla paigham sirf <b>manzoor shuda template</b> se jata hai. Neeche har paigham ke saath Meta template ka naam likhein; us ke variables ki tarteeb wahi rakhein jo wahan likhi hai.</p>
            @endif

            @if ($driver === 'http')
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs text-slate-400">Namoona bharein:</span>
                    @foreach ($presets as $key => $p)
                        <button type="button" wire:click="applyPreset('{{ $key }}')" class="rounded-lg bg-slate-800 px-3 py-1.5 text-xs font-bold text-slate-200 hover:bg-slate-700">{{ $p['label'] }}</button>
                    @endforeach
                </div>
                <div class="grid gap-3 md:grid-cols-2">
                    <div class="md:col-span-2"><label class="{{ $label }}">Gateway URL</label><input wire:model="config.url" dir="ltr" class="{{ $input }} font-mono" placeholder="https://...">@error('config.url')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror</div>
                    <div><label class="{{ $label }}">Method</label><select wire:model="config.method" class="{{ $input }}"><option>POST</option><option>GET</option></select></div>
                    <div><label class="{{ $label }}">Body ki qism (POST)</label><select wire:model="config.body_type" class="{{ $input }}"><option value="json">JSON</option><option value="form">Form</option></select></div>
                    <div class="md:col-span-2"><label class="{{ $label }}">Payload (JSON)</label><textarea wire:model="config.payload" dir="ltr" rows="2" class="{{ $input }} font-mono" placeholder='{"to":"{to}","message":"{message}"}'></textarea>@error('config.payload')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror</div>
                    <div class="md:col-span-2"><label class="{{ $label }}">Headers (har line: Naam: qeemat)</label><textarea wire:model="config.headers" dir="ltr" rows="2" class="{{ $input }} font-mono" placeholder="Authorization: Bearer {token}"></textarea></div>
                    <div><label class="{{ $label }}">Sender / masking</label><input wire:model="config.sender" class="{{ $input }}" placeholder="AssanZameen"></div>
                    <div><label class="{{ $label }}">Number ki shakal</label><select wire:model="config.number_format" class="{{ $input }}"><option value="92">923001234567</option><option value="+92">+923001234567</option><option value="0">03001234567</option></select></div>
                    <div class="md:col-span-2"><label class="{{ $label }}">Kamyabi ki pehchan (jawab mein yeh likha ho; khaali = koi bhi 2xx)</label><input wire:model="config.success_contains" dir="ltr" class="{{ $input }} font-mono"></div>
                </div>
                <p class="text-xs text-slate-400">URL, payload aur headers mein yeh likhein: <code class="text-emerald-300">{to}</code> <code class="text-emerald-300">{message}</code> <code class="text-emerald-300">{token}</code> <code class="text-emerald-300">{sender}</code></p>
            @endif

            @if ($driver === 'gateway')
                <div class="grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="{{ $label }}">Users se charge: Rs. fi SMS</label>
                        <input type="number" step="0.01" min="0" wire:model="config.sms_rate" class="{{ $input }}">
                        @error('config.sms_rate')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                        <p class="mt-1 text-[11px] text-slate-500">Ek SMS = 160 angrezi huroof (Urdu: 70). Lamba paigham 2-3 SMS ginta hai. Admin ke paighamat muft.</p>
                    </div>
                    <div>
                        <label class="{{ $label }}">Har SMS ke darmiyan waqfa (milli-second)</label>
                        <input type="number" min="0" wire:model="config.sms_delay_ms" class="{{ $input }}">
                        @error('config.sms_delay_ms')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                        <p class="mt-1 text-[11px] text-slate-500">3000 = 3 second. Jaldi jaldi SMS se telecom SIM block kar sakti hai.</p>
                    </div>
                    <div>
                        <label class="{{ $label }}">Har user ki rozana had (0 = koi had nahi)</label>
                        <input type="number" min="0" wire:model="ownerDailyLimit" class="{{ $input }}">
                    </div>
                </div>
                @if ($devices->where('is_active', true)->isEmpty())
                    <p class="rounded-lg border border-amber-500/30 bg-amber-500/10 px-3 py-2 text-xs text-amber-200">Abhi koi phone chalu nahi. Users ke SMS queue mein jama ho rahe hain aur phone jorte hi chale jayenge. Neeche "Gateway phones" mein phone jorein.</p>
                @endif
                <p class="text-xs text-slate-400">Paise user ke <b>wallet</b> se katte hain (wahi wallet jo top-up se bharta hai). SMS na jaye to paise khud wapas. Phones neeche "Gateway phones" mein jorein.</p>
            @endif

            @if (! in_array($driver, ['link', 'gateway'], true))
                <div class="grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="{{ $label }}">API token / key</label>
                        <input type="password" wire:model="token" autocomplete="new-password" class="{{ $input }}" placeholder="{{ $hasToken ? '•••••• (mehfooz hai; badalna ho to naya likhein)' : 'Token likhein' }}">
                        @error('token')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="{{ $label }}">Har owner ki rozana had (0 = koi had nahi)</label>
                        <input type="number" min="0" wire:model="ownerDailyLimit" class="{{ $input }}">
                    </div>
                </div>
            @endif
        </section>

        {{-- 2. Paighamat --}}
        <section class="space-y-4 rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
            <div>
                <h2 class="font-black text-white">2. Paighamat ke alfaaz</h2>
                <p class="text-xs text-slate-400">{curly} wale alfaaz bhejte waqt asal qeemat se badal jate hain. Na badlein to default chalta hai.</p>
            </div>
            @foreach ($definitions as $key => $def)
                <div wire:key="tpl-{{ $key }}" class="space-y-2 rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <p class="text-sm font-bold text-white">{{ $def['label'] }} <span class="font-mono text-[11px] text-slate-500">{{ $key }}</span></p>
                        <button type="button" wire:click="resetTemplate('{{ $key }}')" class="text-xs text-slate-400 hover:text-white">Default wapas</button>
                    </div>
                    <textarea wire:model="templates.{{ $key }}.body" rows="2" class="{{ $input }}"></textarea>
                    @error("templates.{$key}.body")<p class="text-xs text-red-400">{{ $message }}</p>@enderror
                    <p class="text-[11px] text-slate-500">Variables: @foreach ($def['vars'] as $n => $v)<code class="text-emerald-300">{{ '{' . $v . '}' }}</code>@if ($driver === 'whatsapp_cloud')<span class="text-slate-600">=@{{ {{ $n + 1 }} }}</span>@endif @endforeach</p>
                    @if ($driver === 'whatsapp_cloud')
                        <div class="grid gap-2 md:grid-cols-2">
                            <input wire:model="templates.{{ $key }}.meta_template" dir="ltr" class="{{ $input }} font-mono" placeholder="Meta template ka naam (maslan qist_reminder)">
                            <input wire:model="templates.{{ $key }}.meta_language" dir="ltr" class="{{ $input }} font-mono" placeholder="Language (khaali = default)">
                        </div>
                        @error("templates.{$key}.meta_template")<p class="text-xs text-red-400">Sirf chhote angrezi huroof, hindse aur _</p>@enderror
                    @endif
                </div>
            @endforeach
        </section>

        <button type="submit" class="w-full rounded-xl bg-emerald-600 py-3 font-black text-white hover:bg-emerald-500 md:w-auto md:px-10">Mehfooz karein</button>
    </form>

    {{-- Gateway phones --}}
    <section class="space-y-4 rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div>
                <h2 class="font-black text-white"><i class="fa-solid fa-mobile-screen text-emerald-400"></i> Gateway phones (apna SMS gateway)</h2>
                <p class="text-xs text-slate-400">Har Android phone mein "PMS SMS Gateway" app dalein, us mein yeh URL aur phone ka token likhein.</p>
            </div>
            <div class="flex flex-wrap gap-2 text-xs">
                <span class="rounded-full bg-sky-500/15 px-3 py-1 font-bold text-sky-300">Queue: {{ $queued }}</span>
                <span class="rounded-full bg-emerald-500/15 px-3 py-1 font-bold text-emerald-300">Kamai aaj: Rs. {{ number_format($earnedToday, 2) }}</span>
                <span class="rounded-full bg-emerald-500/15 px-3 py-1 font-bold text-emerald-300">Is mahine: Rs. {{ number_format($earnedMonth, 2) }}</span>
            </div>
        </div>

        <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3 text-sm">
            <span class="{{ $label }}">Server URL (app mein)</span>
            <code dir="ltr" class="select-all break-all text-emerald-300">{{ $gatewayUrl }}</code>
            @if (str_contains($gatewayUrl, 'localhost') || str_contains($gatewayUrl, '127.0.0.1'))
                <p class="mt-1 text-xs text-amber-300">Phone "localhost" nahi khol sakta. Live domain use karein, ya isi WiFi par computer ka IP (maslan http://192.168.1.5/pms/api/gateway).</p>
            @endif
        </div>

        @if ($newDeviceToken)
            <div class="rounded-xl border border-emerald-500/40 bg-emerald-500/10 p-3 text-sm text-emerald-100">
                <p class="font-bold">Naye phone ka token — abhi copy kar lein, yeh dobara nahi dikhega:</p>
                <code dir="ltr" class="mt-1 block select-all break-all font-mono text-white">{{ $newDeviceToken }}</code>
            </div>
        @endif

        <div class="space-y-2">
            @forelse ($devices as $d)
                <div wire:key="dev-{{ $d->id }}" class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-800 bg-slate-950/60 p-3 text-sm">
                    <div>
                        <p class="font-bold text-white">
                            <span class="me-1 inline-block h-2 w-2 rounded-full {{ $d->isOnline() ? 'bg-emerald-400' : 'bg-slate-600' }}"></span>
                            {{ $d->name }} @if ($d->sim_number)<span class="font-mono text-xs text-slate-400">{{ $d->sim_number }}</span>@endif
                            @unless ($d->is_active)<span class="ms-1 rounded bg-slate-700 px-1.5 text-[10px] text-slate-300">BAND</span>@endunless
                        </p>
                        <p class="text-xs text-slate-400">
                            {{ $d->last_seen_at ? 'Aakhri dafa: ' . $d->last_seen_at->diffForHumans() : 'Abhi tak connect nahi hua' }}
                            @if ($d->last_status) · battery {{ $d->last_status['battery'] ?? '—' }}% · signal {{ $d->last_status['signal'] ?? '—' }} @endif
                            · aaj {{ $d->sentToday() }} / {{ $d->daily_limit ?: '∞' }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="number" min="0" value="{{ $d->daily_limit }}" wire:change="setDeviceLimit({{ $d->id }}, $event.target.value)" title="Rozana had" class="w-24 rounded-lg border border-slate-700 bg-slate-950 px-2 py-1 text-xs text-white">
                        <button type="button" wire:click="toggleDevice({{ $d->id }})" class="rounded-lg bg-slate-800 px-3 py-1.5 text-xs font-bold text-slate-200 hover:bg-slate-700">{{ $d->is_active ? 'Band karein' : 'Chalu karein' }}</button>
                        <button type="button" wire:click="deleteDevice({{ $d->id }})" wire:confirm="Yeh phone hata dein? Is ka token kaam karna chhor dega." class="rounded-lg bg-red-500/15 px-3 py-1.5 text-xs font-bold text-red-300 hover:bg-red-500/25">Hatayein</button>
                    </div>
                </div>
            @empty
                <p class="py-2 text-sm text-slate-500">Abhi koi phone nahi joda gaya.</p>
            @endforelse
        </div>

        <form wire:submit="addDevice" class="grid gap-2 md:grid-cols-[1fr_180px_120px_auto]">
            <input wire:model="deviceName" class="{{ $input }}" placeholder="Phone ka naam (maslan Office Samsung)">
            <input wire:model="deviceSim" inputmode="tel" class="{{ $input }}" placeholder="SIM number (ikhtiyari)">
            <input type="number" min="0" wire:model="deviceLimit" class="{{ $input }}" title="Rozana had">
            <button type="submit" class="rounded-xl bg-emerald-600 px-5 py-2 text-sm font-bold text-white hover:bg-emerald-500">+ Phone jorein</button>
        </form>
        @error('deviceName')<p class="text-xs text-red-400">{{ $message }}</p>@enderror

        @if ($queued > 0)
            <button type="button" wire:click="cancelQueued" wire:confirm="Queue ke tamam paighamat rok dein? Users ko paise wapas mil jayenge." class="text-xs text-red-300 underline">Queue ke paighamat rok dein (paise wapas)</button>
        @endif
    </section>

    {{-- 3. Test --}}
    <section class="space-y-3 rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
        <h2 class="font-black text-white">3. Test paigham</h2>
        <p class="text-xs text-slate-400">Pehle setting mehfooz karein, phir apne number par test bhejein.</p>
        <form wire:submit="sendTest" class="grid gap-2 md:grid-cols-[200px_1fr_auto]">
            <input wire:model="testPhone" inputmode="tel" class="{{ $input }}" placeholder="03001234567">
            <input wire:model="testMessage" class="{{ $input }}">
            <button type="submit" class="rounded-xl bg-slate-800 px-5 py-2 text-sm font-bold text-white hover:bg-slate-700">Bhejein</button>
        </form>
        @if ($testResult)
            <div class="rounded-xl border px-4 py-3 text-sm {{ $testResult['ok'] ? 'border-emerald-500/30 bg-emerald-500/10 text-emerald-200' : 'border-amber-500/30 bg-amber-500/10 text-amber-200' }}">
                @if ($testResult['ok'] && $testResult['status'] === 'queued')
                    Queue mein chala gaya: gateway phone agli dafa check karte hi bhej dega (neeche log mein status dekhein).
                @elseif ($testResult['ok'])
                    Kamyab: paigham bhej diya gaya.
                @elseif ($testResult['status'] === 'link')
                    Abhi "WhatsApp link" chuna hai, is liye khud nahi bheja. <a href="{{ $testResult['link'] }}" target="_blank" rel="noopener" class="underline">Link se kholein</a>
                @else
                    Nakaam: {{ $testResult['error'] }}
                @endif
            </div>
        @endif
    </section>

    {{-- 4. Log --}}
    <section class="space-y-3 rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h2 class="font-black text-white">4. Haal hi ke paighamat</h2>
            <p class="text-xs text-slate-400">Aaj: @forelse ($todayStats as $st => $n)<span class="ms-2 rounded-full px-2 py-0.5 {{ $statusTone[$st] ?? '' }}">{{ $st }} {{ $n }}</span>@empty — @endforelse</p>
        </div>
        <div class="space-y-2">
            @forelse ($logs as $log)
                <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3 text-xs">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <span class="font-mono text-slate-300">{{ $log->to }} · {{ $log->template_key ?? 'test' }} · owner #{{ $log->owner_id ?? '—' }}@if ($log->driver === 'gateway') · {{ $log->segments }} SMS · Rs. {{ number_format((float) $log->cost, 2) }}@if ($log->refunded) (wapas)@endif @endif</span>
                        <span class="flex items-center gap-2"><span class="rounded-full px-2 py-0.5 font-bold {{ $statusTone[$log->status] ?? '' }}">{{ $log->status }}</span><span class="text-slate-500">{{ $log->created_at->diffForHumans() }}</span></span>
                    </div>
                    <p class="mt-1 truncate text-slate-400">{{ $log->body }}</p>
                    @if ($log->error)<p class="mt-1 text-red-300">{{ $log->error }}</p>@endif
                </div>
            @empty
                <p class="py-4 text-center text-sm text-slate-500">Abhi koi paigham khudkaar tareeqe se nahi bheja gaya.</p>
            @endforelse
        </div>
    </section>
</div>
