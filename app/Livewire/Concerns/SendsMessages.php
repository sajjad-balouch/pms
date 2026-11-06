<?php

namespace App\Livewire\Concerns;

use App\Services\Messaging\Messenger;

/**
 * Agents (aur doosre users) ke paighamat admin ke chune hue tareeqe se:
 * - "WhatsApp link" chuna ho: pehle ki tarah WhatsApp khul jata hai, user khud bhejta hai
 * - WhatsApp API / HTTP / apna SMS gateway: seedha chala jata hai (gateway par wallet se paise)
 * - Nakaam ho (balance kam, had poori, API error): wajah dikhti hai aur WhatsApp khul jata hai taa-ke paigham ruke nahi
 */
trait SendsMessages
{
    protected function deliverMessage(?string $phone, string $body, array $context = []): array
    {
        $r = Messenger::sendText($phone, $body, $context);

        if ($r['ok']) {
            session()->flash('message', $r['status'] === 'queued'
                ? __(':channel queue mein — chand second mein chala jayega.', ['channel' => Messenger::channelName()])
                : __(':channel bhej diya gaya.', ['channel' => Messenger::channelName()]));

            return $r;
        }

        if ($r['status'] !== 'link') {
            session()->flash('message', __('Paigham khud nahi gaya: :error — WhatsApp se bhej dein.', ['error' => $r['error'] ?: __('wajah maloom nahi')]));
        }

        $this->openWhatsApp($phone, $r['body']);

        return $r;
    }

    /** Purana tareeqa: WhatsApp app (ya web) tayyar paigham ke saath kholna */
    protected function openWhatsApp(?string $phone, string $body): void
    {
        $clean = preg_replace('/[^0-9]/', '', (string) $phone);
        if (str_starts_with($clean, '0')) {
            $clean = '92' . substr($clean, 1);
        }
        $encoded = urlencode($body);
        $to = $clean !== '' ? "phone={$clean}&" : '';

        $this->dispatch('trigger-whatsapp',
            appUrl: "whatsapp://send?{$to}text={$encoded}",
            webUrl: "https://web.whatsapp.com/send?{$to}text={$encoded}"
        );
    }
}
