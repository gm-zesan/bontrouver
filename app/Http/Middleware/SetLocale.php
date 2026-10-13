<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Supported application locales.
     */
    public const SUPPORTED_LOCALES = ['en', 'fr'];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check query parameter override (?lang=fr or ?locale=fr)
        $queryLocale = $request->query('lang') ?? $request->query('locale');
        if ($queryLocale && in_array($queryLocale, self::SUPPORTED_LOCALES, true)) {
            session(['locale' => $queryLocale]);
            $locale = $queryLocale;
        } else {
            // 2. Check session or persistent cookie
            $locale = session('locale') ?? $request->cookie('locale');
        }

        if (!$locale || !in_array($locale, self::SUPPORTED_LOCALES, true)) {
            $locale = config('app.locale', 'en');
        }

        app()->setLocale($locale);

        return $next($request);
    }
}


