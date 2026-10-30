<?php

namespace App\Support;

class WhatsApp
{
    // Pakistani numbers: 0300..., 0092300..., +92300..., 300... => 92300...
    public static function normalize($phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if (str_starts_with($digits, '0092')) {
            $digits = substr($digits, 2);
        } elseif (str_starts_with($digits, '0')) {
            $digits = '92' . substr($digits, 1);
        } elseif (strlen($digits) === 10 && str_starts_with($digits, '3')) {
            $digits = '92' . $digits;
        }

        return strlen($digits) >= 11 ? $digits : null;
    }

    // No number: WhatsApp asks whom to send it to (partner, family group...)
    public static function shareUrl(string $message): string
    {
        return 'https://wa.me/?text=' . rawurlencode($message);
    }

    // wa.me link with a ready message; null when the phone number is not usable
    public static function url($phone, string $message): ?string
    {
        $number = self::normalize($phone);

        return $number ? 'https://wa.me/' . $number . '?text=' . rawurlencode($message) : null;
    }
}
