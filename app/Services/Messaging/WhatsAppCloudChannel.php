<?php

namespace App\Services\Messaging;

use App\Models\MessageTemplate;
use App\Models\MessagingSetting;
use Illuminate\Support\Facades\Http;

/**
 * Meta ka official WhatsApp Business (Cloud) API.
 * Customer ko pehla paigham sirf Meta se manzoor shuda template se ja sakta hai; is liye har template key ke saath
 * Meta template ka naam admin portal mein likha jata hai. Naam na ho to saada text jata hai (sirf 24 ghante ki khidki mein chalta hai).
 */
class WhatsAppCloudChannel implements Channel
{
    public function __construct(private MessagingSetting $settings) {}

    public function send(string $to, string $body, ?string $templateKey, array $params): array
    {
        $phoneId = trim((string) $this->settings->get('phone_number_id', ''));
        $token = trim((string) $this->settings->get('token', ''));
        $version = trim((string) $this->settings->get('api_version', 'v21.0')) ?: 'v21.0';
        if ($phoneId === '' || $token === '') {
            return ['ok' => false, 'id' => null, 'error' => 'Phone number ID ya token nahi'];
        }

        $tpl = $templateKey ? MessageTemplate::where('key', $templateKey)->first() : null;
        if ($tpl && $tpl->meta_template) {
            $payload = [
                'messaging_product' => 'whatsapp',
                'to' => $to,
                'type' => 'template',
                'template' => [
                    'name' => $tpl->meta_template,
                    'language' => ['code' => $tpl->meta_language ?: (string) $this->settings->get('language', 'en')],
                    'components' => [[
                        'type' => 'body',
                        // Meta khaali parameter qabool nahi karta
                        'parameters' => array_map(fn ($v) => ['type' => 'text', 'text' => trim((string) $v) !== '' ? (string) $v : '-'], array_values($params)),
                    ]],
                ],
            ];
        } else {
            $payload = ['messaging_product' => 'whatsapp', 'to' => $to, 'type' => 'text', 'text' => ['body' => $body]];
        }

        try {
            $res = Http::timeout(15)->withToken($token)->post("https://graph.facebook.com/{$version}/{$phoneId}/messages", $payload);
        } catch (\Throwable $e) {
            return ['ok' => false, 'id' => null, 'error' => mb_substr($e->getMessage(), 0, 300)];
        }

        return $res->successful()
            ? ['ok' => true, 'id' => (string) data_get($res->json(), 'messages.0.id', ''), 'error' => null]
            : ['ok' => false, 'id' => null, 'error' => 'HTTP ' . $res->status() . ': ' . mb_substr((string) data_get($res->json(), 'error.message', $res->body()), 0, 300)];
    }
}
