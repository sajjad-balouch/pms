<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\PageView;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class TrackPageViews
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Sirf GET HTML web pages track karein (Livewire internal updates, APIs, aur assets ignore karein)
        if ($request->isMethod('GET') && !$request->ajax() && !$request->is('livewire/*') && !$request->is('admin/*')) {
            $ip = $request->ip();
            
            // Localhost testing IP fallback
            if ($ip === '127.0.0.1' || $ip === '::1') {
                $ip = '103.255.4.14'; // Sample Pakistan IP for local testing
            }

            // IP location ko 24 hours ke liye cache karein taake external API rate-limit na ho
            $geo = Cache::remember('ip_geo_' . $ip, 86400, function () use ($ip) {
                try {
                    $res = Http::timeout(2)->get("http://ip-api.com/json/{$ip}?fields=status,country,regionName,city");
                    if ($res->successful() && $res->json('status') === 'success') {
                        return $res->json();
                    }
                } catch (\Exception $e) {}
                return ['city' => 'Local / Unknown', 'regionName' => 'Punjab', 'country' => 'Pakistan'];
            });

            // Browser aur Device detection
            $userAgent = $request->userAgent() ?? '';
            $device = preg_match('/(Mobile|Android|iPhone|iPad)/i', $userAgent) ? 'Mobile' : 'Desktop';
            
            $browser = 'Other';
            if (str_contains($userAgent, 'Chrome')) $browser = 'Chrome';
            elseif (str_contains($userAgent, 'Firefox')) $browser = 'Firefox';
            elseif (str_contains($userAgent, 'Safari')) $browser = 'Safari';
            elseif (str_contains($userAgent, 'Edge')) $browser = 'Edge';

            PageView::create([
                'user_id'    => Auth::id(),
                'url'        => $request->fullUrl(),
                'page_title' => $request->path() === '/' ? 'Home Page' : ucwords(str_replace(['-', '/'], ' ', $request->path())),
                'ip_address' => $request->ip(),
                'city'       => $geo['city'] ?? 'Unknown',
                'region'     => $geo['regionName'] ?? 'Unknown',
                'country'    => $geo['country'] ?? 'Unknown',
                'device'     => $device,
                'browser'    => $browser,
                'view_date'  => now()->toDateString(),
                'viewed_at'  => now(),
            ]);
        }

        return $response;
    }
}