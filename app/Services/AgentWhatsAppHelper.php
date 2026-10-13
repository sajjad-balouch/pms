<?php

namespace App\Services;

class AgentWhatsAppHelper
{
    public static function cleanPhone(?string $phone): string
    {
        if (!$phone) return '';
        $num = preg_replace('/[^0-9]/', '', $phone);
        return str_starts_with($num, '0') ? '92' . substr($num, 1) : $num;
    }

    /**
     * Professional Urdu Listing Brochure Format
     */
    public static function generateListingBrochure($property, $agentName): string
    {
        $demand = number_format($property->price);
        $cityName = $property->city?->name ?? 'Pakistan';
        $propUrl = url('/property/' . $property->id);
        $mapUrl = $property->google_map_url ?: "https://www.google.com/maps/search/?api=1&query=" . urlencode($property->location . ' ' . $cityName);

        return "🏢 *{$property->title}*\n\n"
             . "🔹 *نوعیت:* " . ucfirst($property->property_type) . " (" . str_replace('_', ' ', $property->purpose) . ")\n"
             . "🔹 *رقبہ:* {$property->area_size}\n"
             . "🔹 *ڈیمانڈ قیمت:* PKR {$demand}\n"
             . "📍 *لوکیشن:* {$property->location}، {$cityName}\n\n"
             . "🗺️ *گوگل میپ لوکیشن:*\n{$mapUrl}\n\n"
             . "📸 *تصاویر اور مکمل تفصیلات:*\n{$propUrl}\n\n"
             . "👤 *رابطہ برائے ڈیل:* {$agentName}\n"
             . "⚡ پیشکش بذریعہ AssanZameen.com";
    }

    /**
     * Client Follow-up Templates
     */
    public static function getTemplate(string $type, string $clientName, $property = null): string
    {
        $propTitle = $property->title ?? 'پراپرٹی';
        
        switch ($type) {
            case 'site_visit':
                return "السلام علیکم {$clientName} صاحب!\n"
                     . "آپ نے *{$propTitle}* میں دلچسپی ظاہر کی تھی۔\n"
                     . "کیا ہم آج شام یا کل کے لیے موقع وزٹ (Site Visit) شیڈول کر لیں؟\n"
                     . "براہِ کرم اپنی دستیابی سے آگاہ فرمائیں۔ شکریہ!";

            case 'token_reminder':
                return "السلام علیکم {$clientName} صاحب!\n"
                     . "امید ہے آپ خیریت سے ہوں گے۔ *{$propTitle}* کے حوالے سے آپ کی بات چیت جاری تھی۔\n"
                     . "چند مزید خریدار بھی اس پراپرٹی میں دلچسپی لے رہے ہیں۔ اگر آپ ڈیل فائنل کرنا چاہتے ہیں تو مطلع فرمائیں۔";

            case 'price_negotiation':
                return "السلام علیکم {$clientName} صاحب!\n"
                     . "آپ کی آفر مالک (Owner) تک پہنچا دی گئی ہے۔ کیا آپ آج آفس آ کر آمنے سامنے بیٹھ کر حتمی بات کرنا چاہیں گے؟";

            default:
                return "السلام علیکم {$clientName} صاحب! آسان زمین کے ذریعے آپ سے رابطہ کیا جا رہا ہے۔";
        }
    }
}