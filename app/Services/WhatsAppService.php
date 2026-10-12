<?php

namespace App\Services;

class WhatsAppService
{
    /**
     * Pakistani number ko standard format (923001234567) mein convert karna
     */
    public static function formatNumber(?string $phone): string
    {
        if (!$phone) return '';
        
        // Remove spaces, dashes, plus signs
        $number = preg_replace('/[^0-9]/', '', $phone);

        // Agar 03xx se shuru ho raha ho to 92 replace karein
        if (str_starts_with($number, '0')) {
            $number = '92' . substr($number, 1);
        }

        return $number;
    }

    /**
     * Click-to-Chat Direct URL Generator
     */
    public static function generateChatUrl(string $phone, string $message): string
    {
        $formattedPhone = self::formatNumber($phone);
        return "https://wa.me/{$formattedPhone}?text=" . urlencode($message);
    }

    /**
     * Automated API Dispatcher (Optional: e.g., UltraMsg / GreenAPI / Wati)
     */
    public static function sendAutomatedMessage(string $phone, string $message): bool
    {
        $formattedPhone = self::formatNumber($phone);
        $instanceId = config('services.whatsapp.instance_id');
        $token = config('services.whatsapp.token');

        if (!$instanceId || !$token) {
            return false;
        }

        try {
            \Illuminate\Support\Facades\Http::post("https://api.ultramsg.com/{$instanceId}/messages/chat", [
                'token' => $token,
                'to'    => $formattedPhone,
                'body'  => $message,
            ]);
            return true;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('WhatsApp API Error: ' . $e->getMessage());
            return false;
        }
    }
}