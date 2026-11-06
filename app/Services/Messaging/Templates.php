<?php

namespace App\Services\Messaging;

/**
 * Customers ko jaane wale paighamat ki fehrist. Default alfaaz yahan; admin portal se badle ja sakte hain.
 * 'vars' ki tarteeb WhatsApp Cloud API ke template parameters ({{1}}, {{2}}...) ki tarteeb bhi hai.
 */
class Templates
{
    public const ALL = [
        'installment_overdue' => [
            'label' => 'Qist late (defaulter)',
            'vars' => ['name', 'place', 'count', 'amount', 'since', 'portal'],
            'body' => 'Assalam o Alaikum {name}, {place}: aap ki {count} qist(ein) Rs. {amount} late hain (sab se purani {since} se). Barah-e-karam jald ada karein.{portal} Shukriya.',
        ],
        'installment_due_today' => [
            'label' => 'Qist aaj due',
            'vars' => ['name', 'place', 'amount', 'portal'],
            'body' => 'Assalam o Alaikum {name}, {place}: aap ki qist Rs. {amount} aaj due hai.{portal} Shukriya.',
        ],
        'installment_due_soon' => [
            'label' => 'Qist aane wali (yaad-dehani)',
            'vars' => ['name', 'place', 'amount', 'due_date', 'portal'],
            'body' => 'Assalam o Alaikum {name}, {place}: yaad-dehani — aap ki agli qist Rs. {amount} {due_date} ko due hai.{portal} Shukriya.',
        ],
        'installment_unpaid' => [
            'label' => 'Ek khaas qist due / late',
            'vars' => ['name', 'place', 'what', 'amount', 'due_date', 'status', 'portal'],
            'body' => 'Assalam o Alaikum {name}, {place}: aap ki {what} Rs. {amount} {due_date} {status}{portal}',
        ],
        'payment_received' => [
            'label' => 'Wusooli ki raseed',
            'vars' => ['name', 'place', 'what', 'amount', 'date', 'balance', 'receipt'],
            'body' => 'Assalam o Alaikum {name}, {place}: aap ki {what} Rs. {amount} {date} ko wusool ho gayi. Shukriya! Baqi raqam: Rs. {balance}.{receipt}',
        ],
        'portal_link' => [
            'label' => 'Hisaab ka link',
            'vars' => ['name', 'place', 'url'],
            'body' => 'Assalam o Alaikum {name}, {place} — aap ke plot ka mukammal hisaab, qistein aur receipts is link par dekhein: {url}',
        ],
    ];

    public static function keys(): array
    {
        return array_keys(self::ALL);
    }

    public static function exists(string $key): bool
    {
        return isset(self::ALL[$key]);
    }
}
