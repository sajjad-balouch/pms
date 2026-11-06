<?php

namespace App\Services\Messaging;

use App\Models\MessagingSetting;
use Illuminate\Support\Facades\Http;

/**
 * Koi bhi HTTP gateway, sirf settings se: UltraMsg, GreenAPI, Wati, ya Pakistani SMS company.
 * URL, headers aur payload mein {to} {message} {token} {sender} likhein; bhejte waqt asal qeemat lag jati hai.
 */
class HttpGatewayChannel implements Channel
{
    public function __construct(private MessagingSetting $settings) {}

    public function send(string $to, string $body, ?string $templateKey, array $params): array
    {
        $s = $this->settings;
        $number = match ($s->get('number_format', '92')) {
            '+92' => '+' . $to,
            '0' => '0' . substr($to, 2),
            default => $to,
        };
        $vars = ['{to}' => $number, '{message}' => $body, '{token}' => (string) $s->get('token', ''), '{sender}' => (string) $s->get('sender', '')];
        // URL / query mein qeemat encode ho kar jaye; JSON/form mein waisi hi
        $urlVars = array_map('rawurlencode', $vars);

        $url = strtr((string) $s->get('url', ''), $urlVars);
        if (! preg_match('#^https?://#i', $url)) {
            return ['ok' => false, 'id' => null, 'error' => 'Gateway URL sahi nahi'];
        }

        $headers = [];
        foreach (preg_split('/\r?\n/', (string) $s->get('headers', '')) as $line) {
            if (str_contains($line, ':')) {
                [$k, $v] = array_map('trim', explode(':', $line, 2));
                $headers[$k] = strtr($v, $vars);
            }
        }

        $payload = trim((string) $s->get('payload', ''));
        $data = [];
        if ($payload !== '') {
            // Payload JSON ki shakal mein likha jata hai; har qeemat mein placeholders bhar do
            $decoded = json_decode($payload, true);
            if (! is_array($decoded)) {
                return ['ok' => false, 'id' => null, 'error' => 'Payload sahi JSON nahi'];
            }
            array_walk_recursive($decoded, function (&$v) use ($vars) {
                if (is_string($v)) {
                    $v = strtr($v, $vars);
                }
            });
            $data = $decoded;
        }

        try {
            $req = Http::timeout(15)->withHeaders($headers);
            $res = match ($s->get('method', 'POST')) {
                // Khaali array dene se Laravel URL ki apni query (?to=..&msg=..) mita deta hai
                'GET' => $data ? $req->get($url, $data) : $req->get($url),
                default => $s->get('body_type', 'json') === 'form' ? $req->asForm()->post($url, $data) : $req->post($url, $data),
            };
        } catch (\Throwable $e) {
            return ['ok' => false, 'id' => null, 'error' => mb_substr($e->getMessage(), 0, 300)];
        }

        $expect = trim((string) $s->get('success_contains', ''));
        $ok = $res->successful() && ($expect === '' || str_contains($res->body(), $expect));

        return [
            'ok' => $ok,
            'id' => $ok ? (string) (data_get($res->json(), 'id') ?? data_get($res->json(), 'idMessage') ?? data_get($res->json(), 'messages.0.id') ?? '') : null,
            'error' => $ok ? null : 'HTTP ' . $res->status() . ': ' . mb_substr($res->body(), 0, 300),
        ];
    }
}
