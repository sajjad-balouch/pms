<?php

namespace App\Services\Messaging;

/** Paigham bhejne ka koi bhi tareeqa (WhatsApp API, SMS gateway...) */
interface Channel
{
    /**
     * @param  string  $to  92XXXXXXXXXX
     * @param  array  $params  template ke variables, Templates::ALL[$key]['vars'] ki tarteeb mein (Cloud API ke liye)
     * @return array{ok:bool, id:?string, error:?string}
     */
    public function send(string $to, string $body, ?string $templateKey, array $params): array;
}
