<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\Messaging\Messenger;
use App\Services\Messaging\SmsGateway;
use Illuminate\Http\Request;

/**
 * Mobile app se paigham (agents aur town owners). Admin ka chuna hua tareeqa hi chalta hai:
 * WhatsApp link / WhatsApp API / HTTP gateway / apna SMS gateway (wallet se charge).
 */
class MessageController extends ApiController
{
    /** App batata hai button par kya likhe aur kitna kharcha hoga */
    public function channel()
    {
        $driver = Messenger::isAutomatic() ? Messenger::settings()->driver : 'link';

        return $this->ok([
            'channel' => Messenger::channelName(),
            'mode' => $driver === 'link' ? 'link' : 'automatic',
            'sms_rate' => $driver === 'gateway' ? SmsGateway::rate() : null,
        ]);
    }

    /**
     * status: sent | queued (bhej diya / queue mein) | link (app khud "link" kholay)
     *         no_balance | limited | failed (wajah "message" mein; app phir bhi "link" khol sakti hai)
     */
    public function send(Request $request)
    {
        $data = $request->validate([
            'phone' => 'required|string|max:20',
            'message' => 'required|string|max:1000',
            'context' => 'nullable|array',
        ]);

        $r = Messenger::sendText($data['phone'], $data['message'], array_merge(
            array_intersect_key((array) ($data['context'] ?? []), array_flip(['lead_id', 'deal_id', 'property_id', 'plot_id', 'demand_id'])),
            ['source' => 'mobile_app']
        ));

        $payload = ['status' => $r['status'], 'channel' => Messenger::channelName(), 'link' => $r['link']];

        if ($r['ok'] || $r['status'] === 'link') {
            return $this->ok($payload, $r['ok'] ? __('Message sent.') : null);
        }

        return response()->json(['success' => false, 'message' => $r['error'], 'data' => $payload], $r['status'] === 'no_balance' ? 402 : 422);
    }
}
