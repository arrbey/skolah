<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restrict Midtrans webhook endpoint to known Midtrans server IPs.
 *
 * Midtrans notification IPs (production):
 * @see https://docs.midtrans.com/docs/ip-addresses
 *
 * Bisa di-disable via .env MIDTRANS_IP_WHITELIST_ENABLED=false
 * untuk development/sandbox (IP sandbox berbeda dan sering berubah).
 */
class VerifyMidtransIp
{
    /**
     * Midtrans production notification server IPs.
     * Source: https://docs.midtrans.com/docs/ip-addresses
     * Last updated: 2026-04-06
     */
    protected array $allowedIps = [
        '103.208.23.0/24',
        '103.208.23.6',
        '103.127.16.0/23',
        '103.127.17.0/24',
        '34.101.78.12',
        '34.128.95.58',
        '34.101.184.69',
        '34.101.57.175',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        // Skip IP check jika disabled (development/sandbox)
        if (! config('midtrans.ip_whitelist_enabled', true)) {
            return $next($request);
        }

        $clientIp = $request->ip();

        if (! $this->isAllowedIp($clientIp)) {
            Log::channel('webhook')->warning('Midtrans webhook: BLOCKED IP', [
                'ip'       => $clientIp,
                'order_id' => $request->input('order_id'),
            ]);

            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $next($request);
    }

    protected function isAllowedIp(string $ip): bool
    {
        foreach ($this->allowedIps as $allowed) {
            // CIDR notation (e.g. 103.208.23.0/24)
            if (str_contains($allowed, '/')) {
                if ($this->ipInCidr($ip, $allowed)) {
                    return true;
                }
            } else {
                // Exact match
                if ($ip === $allowed) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if an IP address is within a CIDR range.
     */
    protected function ipInCidr(string $ip, string $cidr): bool
    {
        [$subnet, $bits] = explode('/', $cidr);

        $ipLong    = ip2long($ip);
        $subnetLong = ip2long($subnet);
        $mask      = -1 << (32 - (int) $bits);

        $subnetLong &= $mask;

        return ($ipLong & $mask) === $subnetLong;
    }
}
