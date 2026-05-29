<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');

        if ($contentSecurityPolicy = config('security.headers.content_security_policy')) {
            $response->headers->set('Content-Security-Policy', $contentSecurityPolicy);
        }

        if ($request->isSecure() && config('security.headers.hsts.enabled')) {
            $hsts = 'max-age='.config('security.headers.hsts.max_age', 31536000);

            if (config('security.headers.hsts.include_subdomains')) {
                $hsts .= '; includeSubDomains';
            }

            if (config('security.headers.hsts.preload')) {
                $hsts .= '; preload';
            }

            $response->headers->set('Strict-Transport-Security', $hsts);
        }

        return $response;
    }
}
