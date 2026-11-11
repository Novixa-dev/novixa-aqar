<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

class SetLocaleMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Check direct query param ?lang=ar or ?lang=en
        $queryLocale = $request->query('lang') ?: $request->query('locale');
        if ($queryLocale && in_array($queryLocale, ['ar', 'en'])) {
            $locale = $queryLocale;
            if ($request->hasSession()) {
                $request->session()->put('locale', $locale);
            }
            Cookie::queue('locale', $locale, 60 * 24 * 365);
        } else {
            // 2. Check session or cookie or fallback to default
            $sessionLocale = $request->hasSession() ? $request->session()->get('locale') : null;
            $cookieLocale = $request->cookie('locale');
            $locale = $sessionLocale ?: $cookieLocale ?: $this->detectBrowserLocale($request) ?: config('app.locale', 'ar');
        }

        if (!in_array($locale, ['ar', 'en'])) {
            $locale = config('app.locale', 'ar');
        }

        App::setLocale($locale);

        $response = $next($request);

        return $response;
    }

    /**
     * First-visit-only fallback: honor the browser's language preference
     * when the visitor hasn't chosen a locale yet (no session/cookie/query
     * override). Arabic stays the default whenever the header is absent or
     * doesn't clearly prefer English, keeping the Arabic-first experience
     * intact for the vast majority of visitors.
     */
    private function detectBrowserLocale(Request $request): ?string
    {
        $header = $request->server('HTTP_ACCEPT_LANGUAGE');
        if (!$header) {
            return null;
        }

        foreach (explode(',', $header) as $part) {
            $lang = strtolower(trim(explode(';', $part)[0]));
            if (str_starts_with($lang, 'ar')) {
                return 'ar';
            }
            if (str_starts_with($lang, 'en')) {
                return 'en';
            }
        }

        return null;
    }
}
