<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response;

class TrustedProxyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $trusted = env('TRUSTED_PROXIES', '*');
        $proxies = $trusted === '*' ? ['0.0.0.0/0', '::/0'] : array_map('trim', explode(',', $trusted));

        SymfonyRequest::setTrustedProxies(
            $proxies,
            SymfonyRequest::HEADER_X_FORWARDED_FOR |
            SymfonyRequest::HEADER_X_FORWARDED_HOST |
            SymfonyRequest::HEADER_X_FORWARDED_PORT |
            SymfonyRequest::HEADER_X_FORWARDED_PROTO
        );

        return $next($request);
    }
}
