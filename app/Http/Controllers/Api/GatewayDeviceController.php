<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GatewayDevice;
use App\Services\Messaging\SmsGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Admin ke Android gateway phone ki API (users ke liye nahi).
 * Har request mein header: "Authorization: Bearer gw_..." (admin portal mein phone banate waqt milta hai).
 *
 * POST /api/gateway/pull    -> agle SMS jo phone ne bhejne hain (sath phone ki battery/signal)
 * POST /api/gateway/report  -> kaunsa SMS gaya, kaunsa nahi
 */
class GatewayDeviceController extends Controller
{
    public function pull(Request $request): JsonResponse
    {
        $device = $this->device($request);
        $data = $request->validate([
            'max' => 'nullable|integer|min:1|max:50',
            'battery' => 'nullable|integer|min:0|max:100',
            'signal' => 'nullable|string|max:30',
            'app_version' => 'nullable|string|max:20',
        ]);

        $device->update([
            'last_seen_at' => now(),
            'last_status' => array_filter(array_intersect_key($data, array_flip(['battery', 'signal', 'app_version'])), fn ($v) => $v !== null),
        ]);

        $messages = SmsGateway::claim($device, (int) ($data['max'] ?? 10));

        return response()->json([
            'success' => true,
            'active' => $device->is_active,
            'poll_seconds' => 20,
            // SIM block se bachne ke liye har SMS ke darmiyan waqfa
            'delay_ms' => (int) (\App\Services\Messaging\Messenger::settings()->get('sms_delay_ms', 3000)),
            'messages' => $messages->map(fn ($m) => ['id' => $m->id, 'to' => '+' . $m->to, 'body' => $m->body])->values(),
        ]);
    }

    public function report(Request $request): JsonResponse
    {
        $device = $this->device($request);
        $data = $request->validate([
            'results' => 'required|array|max:100',
            'results.*.id' => 'required|integer',
            'results.*.status' => 'required|in:sent,failed',
            'results.*.error' => 'nullable|string|max:300',
        ]);

        $device->update(['last_seen_at' => now()]);

        $accepted = 0;
        foreach ($data['results'] as $r) {
            if (SmsGateway::report($device, (int) $r['id'], $r['status'] === 'sent', $r['error'] ?? null)) {
                $accepted++;
            }
        }

        return response()->json(['success' => true, 'accepted' => $accepted]);
    }

    private function device(Request $request): GatewayDevice
    {
        $device = GatewayDevice::findByToken($request->bearerToken());
        abort_unless($device, 401, 'Gateway token sahi nahi.');

        return $device;
    }
}
