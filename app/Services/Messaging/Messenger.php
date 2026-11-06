<?php

namespace App\Services\Messaging;

use App\Models\MessageLog;
use App\Models\MessageTemplate;
use App\Models\MessagingSetting;
use App\Support\Tenant;
use App\Support\WhatsApp;
use Illuminate\Support\Facades\Auth;

/**
 * Customers ko har paigham isi se banta aur jata hai.
 *
 * - render(): template + variables => paigham (admin ka badla hua ya default)
 * - link():   wa.me link (hamesha kaam karta hai; API na ho to yahi istemal hota hai)
 * - send():   admin portal mein API/SMS chuna ho to seedha bhej deta hai, log karta hai, rozana had dekhta hai
 *
 * Naya API lagana ho to sirf admin portal (Messaging settings) mein tareeqa aur token badlein; code nahi.
 */
class Messenger
{
    public const DRIVERS = [
        'link' => 'WhatsApp link (owner khud bhejta hai)',
        'whatsapp_cloud' => 'WhatsApp Business API (Meta Cloud)',
        'http' => 'HTTP gateway (UltraMsg, GreenAPI, SMS company...)',
        'gateway' => 'Apna SMS gateway (hamara Android phone + SIM; users ke wallet se charge)',
    ];

    /**
     * Ek request mein settings ek hi dafa parhi jati hain. Static memory ki bajaye app container mein,
     * taa-ke agli request / test / queue job purani settings na dekhe. Admin save kare to flush.
     */
    public static function flush(): void
    {
        app()->forgetInstance('messaging.settings');
        app()->forgetInstance('messaging.templates');
    }

    public static function settings(): MessagingSetting
    {
        if (! app()->bound('messaging.settings')) {
            app()->instance('messaging.settings', MessagingSetting::current());
        }

        return app('messaging.settings');
    }

    /** API/SMS laga hua hai: buttons "Bhejein" ban jate hain */
    public static function isAutomatic(): bool
    {
        $driver = self::settings()->driver;

        // Apna gateway: phone abhi band ho tab bhi paigham queue mein rehta hai aur phone aate hi chala jata hai.
        // (Admin ne SMS chuna hai to users ko WhatsApp par wapas nahi bhejna.)
        return $driver === 'gateway' || ($driver !== 'link' && self::channel() !== null);
    }

    public static function template(string $key): string
    {
        if (! app()->bound('messaging.templates')) {
            app()->instance('messaging.templates', MessageTemplate::pluck('body', 'key')->all());
        }

        return app('messaging.templates')[$key] ?? (Templates::ALL[$key]['body'] ?? '');
    }

    public static function render(string $key, array $vars): string
    {
        $body = self::template($key);
        $body = preg_replace_callback('/\{([a-z_]+)\}/', fn ($m) => array_key_exists($m[1], $vars) ? (string) $vars[$m[1]] : '', $body);

        // Khaali variables se bachi faltu jagah aur " ," saaf
        $body = preg_replace(['/[ \t]{2,}/', '/ +([,.:])/'], [' ', '$1'], $body);

        return trim($body);
    }

    public static function link(?string $phone, string $body): ?string
    {
        return WhatsApp::url($phone, $body);
    }

    /**
     * Template ka paigham bhejna. API na ho to bhejta nahi, sirf link deta hai (status 'link').
     *
     * @return array{ok:bool, status:string, error:?string, link:?string, body:string}
     */
    public static function send(?string $phone, string $key, array $vars, array $context = [], ?int $ownerId = null): array
    {
        $body = self::render($key, $vars);
        $params = array_map(fn ($v) => $vars[$v] ?? '', Templates::ALL[$key]['vars'] ?? []);

        return self::dispatch($phone, $body, $key, $params, $context, $ownerId);
    }

    /**
     * Apna likha hua paigham (agents ke follow-up, dealer ko paigham waghera). Admin ka chuna hua tareeqa hi chalta hai:
     * link => wa.me; WhatsApp API / HTTP => seedha; apna gateway => SMS (bhejne wale ke wallet se).
     * Note: Meta Cloud API par template ke baghair paigham sirf 24 ghante ki khidki mein jata hai; warna 'failed' + link.
     */
    public static function sendText(?string $phone, string $body, array $context = [], ?int $ownerId = null): array
    {
        return self::dispatch($phone, trim($body), null, [trim($body)], $context, $ownerId);
    }

    /**
     * Mobile app ke liye wa.me link sirf tab jab admin ne "WhatsApp link" chuna ho.
     * API/SMS chuna ho to null: app POST /api/v1/messages se bheje (admin ka tareeqa, wallet charge).
     */
    public static function manualLink(?string $phone, string $body): ?string
    {
        return self::isAutomatic() ? null : self::link($phone, $body);
    }

    /** Buttons / paighamat mein dikhane ke liye: abhi paigham kis raste jata hai */
    public static function channelName(): string
    {
        return match (self::isAutomatic() ? self::settings()->driver : 'link') {
            'gateway' => 'SMS',
            'http' => __('Message'),
            default => 'WhatsApp',
        };
    }

    /** Admin ka "test message" (template ke baghair) */
    public static function sendRaw(?string $phone, string $body, ?int $ownerId = null): array
    {
        return self::dispatch($phone, $body, null, [$body], ['test' => true], $ownerId);
    }

    public static function sentToday(?int $ownerId): int
    {
        return MessageLog::where('owner_id', $ownerId)->whereIn('status', ['sent', 'queued', 'sending'])->where('created_at', '>=', now()->startOfDay())->count();
    }

    private static function dispatch(?string $phone, string $body, ?string $key, array $params, array $context, ?int $ownerId): array
    {
        $link = self::link($phone, $body);
        $result = fn ($ok, $status, $error = null) => ['ok' => $ok, 'status' => $status, 'error' => $error, 'link' => $link, 'body' => $body];

        if (! self::isAutomatic()) {
            return $result(false, 'link');
        }

        $to = WhatsApp::normalize($phone);
        if (! $to) {
            return $result(false, 'failed', 'Phone number sahi nahi');
        }

        $ownerId ??= Tenant::ownerId();
        $limit = self::settings()->owner_daily_limit;
        if ($limit > 0 && self::sentToday($ownerId) >= $limit) {
            self::log($ownerId, $key, $to, $body, 'limited', null, "Rozana had ({$limit}) poori", $context);

            return $result(false, 'limited', "Aaj ki had ({$limit} paighamat) poori ho gayi. Kal dobara bhejein.");
        }

        if (self::settings()->driver === 'gateway') {
            // Apna gateway: wallet se paise kat kar queue mein; phone chand second mein bhej dega
            $q = SmsGateway::enqueue($ownerId, Auth::id(), $to, $body, $key, $context);

            return $result($q['ok'], $q['status'], $q['error']);
        }

        $sent = self::channel()->send($to, $body, $key, $params);
        self::log($ownerId, $key, $to, $body, $sent['ok'] ? 'sent' : 'failed', $sent['id'], $sent['error'], $context);

        return $result($sent['ok'], $sent['ok'] ? 'sent' : 'failed', $sent['error']);
    }

    private static function channel(): ?Channel
    {
        $s = self::settings();

        return match ($s->driver) {
            'whatsapp_cloud' => new WhatsAppCloudChannel($s),
            'http' => new HttpGatewayChannel($s),
            default => null,
        };
    }

    private static function log(?int $ownerId, ?string $key, string $to, string $body, string $status, ?string $providerId, ?string $error, array $context): void
    {
        MessageLog::create([
            'owner_id' => $ownerId,
            'user_id' => Auth::id(),
            'template_key' => $key,
            'to' => $to,
            'driver' => self::settings()->driver,
            'body' => $body,
            'status' => $status,
            'provider_id' => $providerId ?: null,
            'error' => $error,
            'context' => $context ?: null,
        ]);
    }
}
