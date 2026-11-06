<?php

namespace App\Services\Messaging;

use App\Models\GatewayDevice;
use App\Models\MessageLog;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

/**
 * Platform ka apna SMS gateway.
 *
 * SIM aur phone admin ke paas; users (town owners waghera) paigham bhejte hain to:
 *   1. enqueue(): SMS ke hisse (segments) gin kar user ke wallet se paise katte hain, paigham queue mein (status queued)
 *   2. claim():   admin ka Android phone har chand second baad agle paighamat utha leta hai (status sending)
 *   3. report():  phone batata hai bhej diya (sent) ya nahi; 3 koshishon ke baad bhi nakaam ho to paise wapas
 */
class SmsGateway
{
    public const MAX_ATTEMPTS = 3;

    /** Itni der phone ka jawab na aaye to paigham doosre phone / agli dafa ke liye wapas queue mein */
    public const CLAIM_TIMEOUT_MINUTES = 10;

    /** Rs. fi SMS hissa (admin portal se) */
    public static function rate(): float
    {
        return max(0, (float) Messenger::settings()->get('sms_rate', 1));
    }

    /**
     * Ek SMS 160 angrezi huroof (lamba ho to 153 fi hissa). Urdu / emoji ho to Unicode: 70 (lamba ho to 67).
     */
    public static function segments(string $body): int
    {
        $gsm = "@£\$¥èéùìòÇ\nØø\rÅåΔ_ΦΓΛΩΠΨΣΘΞÆæßÉ !\"#¤%&'()*+,-./0123456789:;<=>?¡ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÑÜ§¿abcdefghijklmnopqrstuvwxyzäöñüà";
        $extended = "^{}\\[~]|€";
        $isGsm = true;
        $len = 0;
        foreach (mb_str_split($body) as $ch) {
            if (mb_strpos($extended, $ch) !== false) {
                $len += 2;
            } elseif (mb_strpos($gsm, $ch) !== false) {
                $len++;
            } else {
                $isGsm = false;
                break;
            }
        }

        if (! $isGsm) {
            $len = mb_strlen($body, 'UTF-8');

            return $len <= 70 ? 1 : (int) ceil($len / 67);
        }

        return $len <= 160 ? 1 : (int) ceil($len / 153);
    }

    public static function quote(string $body): float
    {
        return round(self::segments($body) * self::rate(), 2);
    }

    public static function hasActiveDevice(): bool
    {
        return GatewayDevice::where('is_active', true)->exists();
    }

    /**
     * Paigham queue mein dalna aur paise kaatna. Admin (ya bina account) ke paighamat muft.
     *
     * @return array{ok:bool, status:string, error:?string, log:?MessageLog}
     */
    public static function enqueue(?int $payerId, ?int $userId, string $to, string $body, ?string $key, array $context): array
    {
        $segments = self::segments($body);

        return DB::transaction(function () use ($payerId, $userId, $to, $body, $key, $context, $segments) {
            $payer = $payerId ? User::whereKey($payerId)->lockForUpdate()->first() : null;
            $cost = $payer && ! $payer->isAdmin() ? round($segments * self::rate(), 2) : 0.0;

            if ($cost > 0 && (float) $payer->wallet_balance < $cost) {
                $error = 'Wallet mein balance kam hai: is SMS ke Rs. ' . number_format($cost, 2) . ' chahiye, baqi Rs. ' . number_format((float) $payer->wallet_balance, 2) . '. Wallet top-up karein.';
                $log = self::newLog($payerId, $userId, $to, $body, $key, $context, $segments, 0, 'no_balance', $error);

                return ['ok' => false, 'status' => 'no_balance', 'error' => $error, 'log' => $log];
            }

            $log = self::newLog($payerId, $userId, $to, $body, $key, $context, $segments, $cost, 'queued', null);

            if ($cost > 0) {
                $payer->decrement('wallet_balance', $cost);
                WalletTransaction::create([
                    'user_id' => $payer->id,
                    'amount' => $cost,
                    'type' => 'message_fee',
                    'status' => 'approved',
                    'description' => "SMS to {$to} ({$segments} SMS)",
                    'reference_id' => (string) $log->id,
                ]);
            }

            return ['ok' => true, 'status' => 'queued', 'error' => null, 'log' => $log];
        });
    }

    /**
     * Phone ke liye agle paighamat. Har paigham sirf ek phone ko milta hai.
     *
     * @return \Illuminate\Support\Collection<int, MessageLog>
     */
    public static function claim(GatewayDevice $device, int $max = 10)
    {
        self::releaseStale();

        $room = $device->daily_limit > 0 ? max(0, $device->daily_limit - $device->sentToday()) : $max;
        $take = min($max, $room);
        if (! $device->is_active || $take <= 0) {
            return collect();
        }

        $claimed = [];
        foreach (MessageLog::where('status', 'queued')->where('driver', 'gateway')->orderBy('id')->limit($take * 3)->pluck('id') as $id) {
            // Shart ke saath update: do phone ek saath maangein to bhi ek hi ko milega
            $won = MessageLog::whereKey($id)->where('status', 'queued')->update([
                'status' => 'sending', 'device_id' => $device->id, 'claimed_at' => now(), 'attempts' => DB::raw('attempts + 1'), 'updated_at' => now(),
            ]);
            if ($won) {
                $claimed[] = $id;
            }
            if (count($claimed) >= $take) {
                break;
            }
        }

        return MessageLog::whereIn('id', $claimed)->orderBy('id')->get();
    }

    /** Phone ka jawab. Sirf usi phone ka jawab mana jata hai jis ne paigham uthaya tha. */
    public static function report(GatewayDevice $device, int $id, bool $sent, ?string $error = null): ?MessageLog
    {
        $log = MessageLog::whereKey($id)->where('device_id', $device->id)->where('status', 'sending')->first();
        if (! $log) {
            return null;
        }

        if ($sent) {
            $log->update(['status' => 'sent', 'sent_at' => now(), 'error' => null]);

            return $log;
        }

        $error = mb_substr(trim((string) $error) ?: 'Phone se nahi gaya', 0, 300);
        if ($log->attempts < self::MAX_ATTEMPTS) {
            $log->update(['status' => 'queued', 'device_id' => null, 'claimed_at' => null, 'error' => $error]);
        } else {
            $log->update(['status' => 'failed', 'error' => $error]);
            self::refund($log);
        }

        return $log;
    }

    /** Admin queue mein pare paighamat rok de: paise wapas */
    public static function cancel(MessageLog $log): bool
    {
        $done = MessageLog::whereKey($log->id)->where('status', 'queued')->update(['status' => 'cancelled', 'error' => 'Admin ne rok diya', 'updated_at' => now()]);
        if ($done) {
            self::refund($log->fresh());
        }

        return (bool) $done;
    }

    public static function refund(MessageLog $log): void
    {
        DB::transaction(function () use ($log) {
            $log = MessageLog::whereKey($log->id)->lockForUpdate()->first();
            if (! $log || $log->refunded || (float) $log->cost <= 0 || ! $log->owner_id) {
                return;
            }

            User::whereKey($log->owner_id)->increment('wallet_balance', (float) $log->cost);
            WalletTransaction::create([
                'user_id' => $log->owner_id,
                'amount' => $log->cost,
                'type' => 'refund',
                'status' => 'approved',
                'description' => "SMS to {$log->to} nahi gaya — paise wapas",
                'reference_id' => (string) $log->id,
            ]);
            $log->update(['refunded' => true]);
        });
    }

    /** Phone band / net gaya: uthaye hue paighamat wapas queue mein (ya hadd poori ho to wapas paise) */
    public static function releaseStale(): void
    {
        $stale = MessageLog::where('status', 'sending')->where('claimed_at', '<', now()->subMinutes(self::CLAIM_TIMEOUT_MINUTES))->get();
        foreach ($stale as $log) {
            if ($log->attempts >= self::MAX_ATTEMPTS) {
                $log->update(['status' => 'failed', 'error' => 'Phone se jawab nahi aaya']);
                self::refund($log);
            } else {
                $log->update(['status' => 'queued', 'device_id' => null, 'claimed_at' => null, 'error' => 'Phone se jawab nahi aaya, dobara koshish']);
            }
        }
    }

    private static function newLog(?int $payerId, ?int $userId, string $to, string $body, ?string $key, array $context, int $segments, float $cost, string $status, ?string $error): MessageLog
    {
        return MessageLog::create([
            'owner_id' => $payerId,
            'user_id' => $userId,
            'template_key' => $key,
            'to' => $to,
            'driver' => 'gateway',
            'body' => $body,
            'segments' => $segments,
            'cost' => $cost,
            'status' => $status,
            'error' => $error,
            'context' => $context ?: null,
        ]);
    }
}
