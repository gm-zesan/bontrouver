<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    /**
     * Switch application locale and redirect back safely.
     */
    public function switch(string $locale, Request $request): RedirectResponse
    {
        if (in_array($locale, SetLocale::SUPPORTED_LOCALES, true)) {
            session(['locale' => $locale]);
            app()->setLocale($locale);
            cookie()->queue(cookie()->forever('locale', $locale));
        }

        $referer = $request->headers->get('referer');
        if ($referer && !str_contains($referer, '/locale/')) {
            return redirect()->back(fallback: route('home'));
        }

        return redirect()->route('home');
    }
}


