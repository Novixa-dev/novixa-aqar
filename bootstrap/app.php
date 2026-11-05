<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Railway terminates TLS at its edge and forwards to this container
        // over plain HTTP, so without trusting the forwarded headers the app
        // believes every request is insecure: Request::isSecure() is false,
        // and every generated absolute URL and post-login redirect comes out
        // as http:// on an https-only site.
        //
        // App\Http\Middleware\TrustProxies exists in this repo but is
        // Laravel 10-style configuration that nothing registers, so it has
        // never taken effect. Trusting all proxies is the correct setting on
        // a platform where the container is only ever reachable through that
        // edge -- there is no path by which a client can set these headers
        // itself.
        $middleware->trustProxies(at: '*');

        // Locale must resolve on EVERY request — including unmatched URLs
        // rendering 404 pages — so it lives on the global stack, not the web
        // group (which never runs for non-routed paths). The middleware falls
        // back to its cookie when no session is started yet; that cookie is
        // kept plaintext because EncryptCookies sits in the web group and
        // never touches non-routed requests.
        $middleware->append(\App\Http\Middleware\SetLocaleMiddleware::class);
        $middleware->encryptCookies(except: ['locale']);

        // App\Http\Middleware\TrimStrings exists in this repo with a
        // password exception list, but it's Laravel 10-style configuration
        // that nothing registers (this app uses the 11+ bootstrap/app.php
        // middleware API) -- the global stack was inserting the framework's
        // own TrimStrings instead, with no exceptions at all, silently
        // trimming whitespace from every login/register/password-reset
        // field including the password itself before validation/hashing.
        $middleware->trimStrings(except: [
            'current_password',
            'password',
            'password_confirmation',
        ]);

        // Stripe delivers webhooks with their own signature scheme; the
        // Laravel CSRF token is neither present nor applicable.
        $middleware->validateCsrfTokens(except: [
            'payments/webhook',
        ]);

        // RoleBasedRedirect is deliberately NOT global. Appended here it ran
        // before the session started, so it never saw a signed-in user in
        // production (dead code) - while under any ordering where it did, it
        // would bounce every signed-in buyer, tenant or staff member off the
        // public site to their panel, making booking, wishlist and reviews
        // unusable. Post-login routing is LoginResponse's job; the alias
        // below remains for any route that wants it explicitly.
        $middleware->append([
            \App\Http\Middleware\SecurityHeaders::class,
        ]);

        $middleware->alias([
            'role.redirect'      => \App\Http\Middleware\RoleBasedRedirect::class,
            'teams.permission'   => \App\Http\Middleware\TeamsPermission::class,
            'role'               => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'         => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            // Same Laravel-10-vs-11 config gap as TrimStrings above: without
            // this, the `guest` middleware (routes/web.php's /login,
            // /register group) ran the framework's own RedirectIfAuthenticated
            // instead of this app's role-aware one -- every already-logged-in
            // user of any role landed on the public homepage when revisiting
            // /login, instead of their own panel.
            'guest'              => \App\Http\Middleware\RedirectIfAuthenticated::class,
        ]);

        $middleware->throttleApi();
    })
    ->booted(function () {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('chatbot', function (Request $request) {
            return Limit::perMinute(20)->by($request->ip());
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->report(function (\App\Exceptions\OnTheMarketApiException $e) {
            Log::error('OnTheMarket API Error: ' . $e->getMessage());
        });

        $exceptions->report(function (\Illuminate\Database\QueryException $e) {
            Log::error('Database Query Error: ' . $e->getMessage());
        });
    })->create();
