<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request and attach recommended HTTP security headers.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent clickjacking by not allowing this site to be embedded in an external iframe
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Prevent MIME-sniffing exploits
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Enable cross-site scripting filter in legacy browsers
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Control referrer information sent in HTTP requests
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Restrict sensitive browser features (camera, microphone) while permitting geolocation for location-based search
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(self)');

        // Force HTTPS via HSTS if the incoming connection is secure
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
