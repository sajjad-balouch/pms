<?php

namespace App\Livewire\Admin;

use App\Models\GatewayDevice;
use App\Models\MessageLog;
use App\Models\MessageTemplate;
use App\Models\MessagingSetting;
use App\Services\Messaging\Messenger;
use App\Services\Messaging\SmsGateway;
use App\Services\Messaging\Templates;
use Livewire\Component;

/**
 * Platform ke paighamat: kaunsa tareeqa (link / WhatsApp API / SMS), us ki settings, paighamat ke alfaaz, test aur log.
 * Naya provider lagana ho to sirf yahan setting badlein.
 */
class MessagingSettings extends Component
{
    // Settings jo saaf likhi ja sakti hain (token alag; wapas form mein nahi dikhaya jata)
    public const FIELDS = ['phone_number_id', 'api_version', 'language', 'url', 'method', 'body_type', 'payload', 'headers', 'sender', 'number_format', 'success_contains', 'sms_rate', 'sms_delay_ms'];

    // Tayyar namoone; admin apne provider ke mutabiq badal le
    public const PRESETS = [
        'ultramsg' => ['label' => 'UltraMsg (WhatsApp)', 'url' => 'https://api.ultramsg.com/INSTANCE_ID/messages/chat', 'method' => 'POST', 'body_type' => 'form',
            'payload' => '{"token":"{token}","to":"{to}","body":"{message}"}', 'number_format' => '+92', 'success_contains' => '"sent":"true"'],
        'greenapi' => ['label' => 'GreenAPI (WhatsApp)', 'url' => 'https://api.green-api.com/waInstanceID_INSTANCE/sendMessage/{token}', 'method' => 'POST', 'body_type' => 'json',
            'payload' => '{"chatId":"{to}@c.us","message":"{message}"}', 'number_format' => '92', 'success_contains' => 'idMessage'],
        'sms' => ['label' => 'SMS gateway (aam namoona)', 'url' => 'https://SMS-COMPANY.pk/api/send?api_key={token}&sender={sender}&to={to}&message={message}', 'method' => 'GET', 'body_type' => 'json',
            'payload' => '', 'number_format' => '92', 'success_contains' => ''],
    ];

    public string $driver = 'link';

    public array $config = [];

    public string $token = '';

    public bool $hasToken = false;

    public int $ownerDailyLimit = 200;

    /** @var array<string,array{body:string,meta_template:?string,meta_language:?string}> */
    public array $templates = [];

    public string $testPhone = '';

    public string $testMessage = 'Assan Zameen se test paigham. Agar yeh mila hai to setting theek hai.';

    public ?array $testResult = null;

    public function mount(): void
    {
        $s = MessagingSetting::current();
        $this->driver = array_key_exists($s->driver, Messenger::DRIVERS) ? $s->driver : 'link';
        $this->config = array_merge(array_fill_keys(self::FIELDS, ''), ['method' => 'POST', 'body_type' => 'json', 'number_format' => '92', 'api_version' => 'v21.0', 'language' => 'en', 'sms_rate' => '1', 'sms_delay_ms' => '3000'],
            array_intersect_key($s->config ?? [], array_flip(self::FIELDS)));
        $this->hasToken = (string) $s->get('token', '') !== '';
        $this->ownerDailyLimit = $s->owner_daily_limit;

        $saved = MessageTemplate::all()->keyBy('key');
        foreach (Templates::ALL as $key => $t) {
            $row = $saved->get($key);
            $this->templates[$key] = ['body' => $row->body ?? $t['body'], 'meta_template' => $row->meta_template ?? '', 'meta_language' => $row->meta_language ?? ''];
        }
    }

    public function applyPreset(string $preset): void
    {
        if (isset(self::PRESETS[$preset])) {
            $this->config = array_merge($this->config, array_diff_key(self::PRESETS[$preset], ['label' => 1]));
        }
    }

    public function save(): void
    {
        $this->validate([
            'driver' => 'required|in:' . implode(',', array_keys(Messenger::DRIVERS)),
            'ownerDailyLimit' => 'required|integer|min:0|max:100000',
            // 'url' qaida nahi: URL mein {token} jaise placeholders hote hain (GreenAPI waghera)
            'config.url' => $this->driver === 'http' ? ['required', 'string', 'max:500', 'starts_with:https://,http://'] : 'nullable',
            'config.method' => 'in:GET,POST',
            'config.body_type' => 'in:json,form',
            'config.number_format' => 'in:92,+92,0',
            'config.sms_rate' => $this->driver === 'gateway' ? 'required|numeric|min:0|max:1000' : 'nullable',
            'config.sms_delay_ms' => $this->driver === 'gateway' ? 'required|integer|min:0|max:60000' : 'nullable',
            'config.phone_number_id' => $this->driver === 'whatsapp_cloud' ? 'required|string|max:64' : 'nullable',
            'config.payload' => ['nullable', 'string', 'max:2000', function ($attr, $value, $fail) {
                if (trim((string) $value) !== '' && ! is_array(json_decode($value, true))) {
                    $fail('Payload sahi JSON hona chahiye.');
                }
            }],
            'templates.*.body' => 'required|string|max:1000',
            'templates.*.meta_template' => 'nullable|string|max:100|regex:/^[a-z0-9_]*$/',
            'templates.*.meta_language' => 'nullable|string|max:10',
        ], [], ['config.url' => 'Gateway URL', 'config.phone_number_id' => 'Phone number ID']);

        if ($this->driver !== 'link' && ! $this->hasToken && trim($this->token) === '' && $this->driver === 'whatsapp_cloud') {
            $this->addError('token', 'Access token likhein.');

            return;
        }

        $s = MessagingSetting::current();
        $config = array_map(fn ($v) => is_string($v) ? trim($v) : $v, $this->config);
        // Token sirf tab badlo jab naya likha gaya ho
        $config['token'] = trim($this->token) !== '' ? trim($this->token) : (string) $s->get('token', '');
        $s->update(['driver' => $this->driver, 'config' => $config, 'owner_daily_limit' => $this->ownerDailyLimit]);
        $this->hasToken = $config['token'] !== '';
        $this->token = '';

        foreach ($this->templates as $key => $t) {
            if (! Templates::exists($key)) {
                continue;
            }
            $isDefault = trim($t['body']) === Templates::ALL[$key]['body'] && trim((string) $t['meta_template']) === '';
            if ($isDefault) {
                MessageTemplate::where('key', $key)->delete();
            } else {
                MessageTemplate::updateOrCreate(['key' => $key], [
                    'body' => trim($t['body']), 'meta_template' => trim((string) $t['meta_template']) ?: null, 'meta_language' => trim((string) $t['meta_language']) ?: null,
                ]);
            }
        }

        Messenger::flush();
        session()->flash('success', 'Settings mehfooz ho gayin.');
    }

    public function resetTemplate(string $key): void
    {
        if (Templates::exists($key)) {
            $this->templates[$key] = ['body' => Templates::ALL[$key]['body'], 'meta_template' => '', 'meta_language' => ''];
        }
    }

    // ---------- Apna gateway: phones ----------

    public string $deviceName = '';

    public string $deviceSim = '';

    public int $deviceLimit = 300;

    /** Naye phone ka token sirf ek dafa dikhaya jata hai */
    public ?string $newDeviceToken = null;

    public function addDevice(): void
    {
        $this->validate([
            'deviceName' => 'required|string|max:80',
            'deviceSim' => 'nullable|string|max:20',
            'deviceLimit' => 'required|integer|min:0|max:10000',
        ], [], ['deviceName' => 'Phone ka naam']);

        [, $this->newDeviceToken] = GatewayDevice::register(trim($this->deviceName), trim($this->deviceSim), $this->deviceLimit);
        $this->reset('deviceName', 'deviceSim');
        $this->deviceLimit = 300;
    }

    public function toggleDevice(int $id): void
    {
        $d = GatewayDevice::findOrFail($id);
        $d->update(['is_active' => ! $d->is_active]);
    }

    public function setDeviceLimit(int $id, $limit): void
    {
        GatewayDevice::whereKey($id)->update(['daily_limit' => max(0, min(10000, (int) $limit))]);
    }

    public function deleteDevice(int $id): void
    {
        $d = GatewayDevice::findOrFail($id);
        // Is phone ke paas atke paighamat doosre phone ke liye wapas
        MessageLog::where('device_id', $d->id)->where('status', 'sending')->update(['status' => 'queued', 'device_id' => null, 'claimed_at' => null]);
        $d->delete();
    }

    /** Queue mein pare tamam paighamat rok dein (users ko paise wapas) */
    public function cancelQueued(): void
    {
        $n = 0;
        MessageLog::where('status', 'queued')->get()->each(function ($log) use (&$n) {
            $n += SmsGateway::cancel($log) ? 1 : 0;
        });
        session()->flash('success', "{$n} paighamat rok diye, paise wapas.");
    }

    public function sendTest(): void
    {
        $this->validate(['testPhone' => 'required|string|max:20', 'testMessage' => 'required|string|max:500']);
        Messenger::flush();
        $r = Messenger::sendRaw($this->testPhone, $this->testMessage);
        $this->testResult = $r;
    }

    public function render()
    {
        Messenger::flush();

        return view('livewire.admin.messaging-settings', [
            'drivers' => Messenger::DRIVERS,
            'presets' => self::PRESETS,
            'definitions' => Templates::ALL,
            'isAutomatic' => Messenger::isAutomatic(),
            'logs' => MessageLog::latest('id')->limit(15)->get(),
            'todayStats' => MessageLog::where('created_at', '>=', now()->startOfDay())->selectRaw('status, COUNT(*) as n')->groupBy('status')->pluck('n', 'status'),
            'devices' => GatewayDevice::orderBy('id')->get(),
            'queued' => MessageLog::whereIn('status', ['queued', 'sending'])->count(),
            'gatewayUrl' => url('/api/gateway'),
            // Asal kamai: kate hue paise minus wapas kiye hue
            'earnedToday' => (float) MessageLog::where('driver', 'gateway')->where('refunded', false)->where('created_at', '>=', now()->startOfDay())->sum('cost'),
            'earnedMonth' => (float) MessageLog::where('driver', 'gateway')->where('refunded', false)->where('created_at', '>=', now()->startOfMonth())->sum('cost'),
        ])->layout('layouts.admin-app');
    }
}
