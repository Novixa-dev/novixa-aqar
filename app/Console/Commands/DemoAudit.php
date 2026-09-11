<?php

namespace App\Console\Commands;

use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Exercises the deployed demo over real HTTP, from inside its own container.
 *
 * This exists because the staging host is not reachable from the machines
 * this project is developed on, so "does the demo actually work" could only
 * ever be answered by a person opening a browser. It runs against the live
 * RoadRunner server on localhost, with real sessions, real cookies, a real
 * login per persona and a real fetch of an image through the signed-URL
 * redirect -- so it covers everything a browser would except JavaScript and
 * how the page looks.
 *
 * Read-only by design: it issues GETs plus the login POST each persona
 * needs. It creates and deletes nothing.
 *
 * Every line is prefixed AUDIT| so the whole run can be pulled out of a
 * deploy log with one filter.
 */
class DemoAudit extends Command
{
    protected $signature = 'demo:audit {--base=http://127.0.0.1:8000 : Base URL to exercise}';

    protected $description = 'Exercise every public page, persona and panel of the deployed demo over real HTTP';

    private string $base;

    private int $passed = 0;

    private int $failed = 0;

    private array $failures = [];

    /** Public pages any visitor can reach. */
    private const PUBLIC_ROUTES = [
        '/', '/properties', '/properties/search', '/news', '/news-list',
        '/about', '/services', '/contact', '/contact-us', '/calculators',
        '/terms', '/terms-and-conditions', '/privacy', '/privacy-policy',
        '/submit-property', '/property-request', '/wishlist',
        '/booking-calendar', '/login', '/register', '/sitemap.xml', '/up',
    ];

    /** email => [panel path, label]. Password is the seeded demo one. */
    private const PERSONAS = [
        'admin@darhadhramaut.ye' => ['/admin', 'super admin'],
        'staff@darhadhramaut.ye' => ['/staff', 'staff'],
        'agent@darhadhramaut.ye' => ['/agent', 'agent'],
        'buyer@darhadhramaut.ye' => ['/buyer', 'buyer'],
        'seller@darhadhramaut.ye' => ['/seller', 'seller'],
        'landlord@darhadhramaut.ye' => ['/landlord', 'landlord'],
        'tenant@darhadhramaut.ye' => ['/tenant', 'tenant'],
        'contractor@darhadhramaut.ye' => ['/contractor', 'contractor'],
    ];

    public function handle(): int
    {
        $this->base = rtrim((string) $this->option('base'), '/');

        $this->out('=== DEMO AUDIT START === '.now()->toIso8601String());
        $this->out('base='.$this->base);

        $this->auditPublicPages();
        $this->auditLanguages();
        $this->auditMedia();
        $this->auditPersonas();
        $this->auditAuthorization();
        $this->auditErrorHandling();

        $this->out('--- SUMMARY ---');
        $this->out("passed={$this->passed} failed={$this->failed}");

        foreach ($this->failures as $failure) {
            $this->out('FAILURE: '.$failure);
        }

        $this->out('=== DEMO AUDIT END ===');

        // Never fail the boot over an audit result; the log is the product.
        return self::SUCCESS;
    }

    private function auditPublicPages(): void
    {
        $this->out('--- PUBLIC PAGES ---');

        foreach (self::PUBLIC_ROUTES as $route) {
            $this->check("GET {$route}", function () use ($route) {
                $started = microtime(true);
                $response = $this->client()->get($this->base.$route);
                $ms = (int) ((microtime(true) - $started) * 1000);
                $body = $response->body();

                $note = "{$response->status()} {$ms}ms";

                if (! $response->successful()) {
                    return [false, $note];
                }

                foreach (['Lorem ipsum', 'Whoops', 'SQLSTATE', 'Undefined variable'] as $smell) {
                    if (str_contains($body, $smell)) {
                        return [false, $note." contains [{$smell}]"];
                    }
                }

                return [true, $note.' '.strlen($body).'b'];
            });
        }

        // A property detail page, resolved from whatever the listing shows.
        $id = $this->firstPropertyId();

        if ($id !== null) {
            $this->check("GET /properties/{$id}", function () use ($id) {
                $response = $this->client()->get($this->base."/properties/{$id}");

                return [$response->successful(), (string) $response->status()];
            });
        } else {
            $this->note('could not resolve a property id from /properties');
        }
    }

    private function auditLanguages(): void
    {
        $this->out('--- LANGUAGE / DIRECTION ---');

        $this->check('arabic is rtl', function () {
            $jar = new CookieJar;
            $this->client($jar)->get($this->base.'/lang/ar');
            $body = $this->client($jar)->get($this->base.'/')->body();

            return [str_contains($body, 'dir="rtl"'), str_contains($body, 'dir="rtl"') ? 'dir=rtl' : 'no dir=rtl'];
        });

        $this->check('english is ltr', function () {
            $jar = new CookieJar;
            $this->client($jar)->get($this->base.'/lang/en');
            $body = $this->client($jar)->get($this->base.'/')->body();

            return [str_contains($body, 'dir="ltr"'), str_contains($body, 'dir="ltr"') ? 'dir=ltr' : 'no dir=ltr'];
        });

        $this->check('arabic content renders', function () {
            $jar = new CookieJar;
            $this->client($jar)->get($this->base.'/lang/ar');
            $body = $this->client($jar)->get($this->base.'/properties')->body();

            $hasArabic = (bool) preg_match('/\p{Arabic}/u', $body);

            // A raw translation key leaking into the page looks like text but
            // is a bug -- e.g. "messages.welcome" rendered verbatim.
            $leaked = (bool) preg_match('/>\s*[a-z_]+\.[a-z_]{3,}\s*</', $body);

            return [$hasArabic && ! $leaked, ($hasArabic ? 'arabic-ok' : 'NO-ARABIC').($leaked ? ' LEAKED-KEY' : '')];
        });
    }

    private function auditMedia(): void
    {
        $this->out('--- MEDIA (object storage) ---');

        $body = $this->client()->get($this->base.'/properties')->body();

        if (! preg_match('#(?:href|src)="([^"]*/media/[^"]+)"#', $body, $m)) {
            $this->note('no /media/ URL found on /properties -- cannot verify image delivery');

            return;
        }

        $url = html_entity_decode($m[1]);
        $this->out("media url: {$url}");

        $path = parse_url($url, PHP_URL_PATH);

        $this->check('media route redirects', function () use ($path) {
            $response = Http::withoutRedirecting()->timeout(20)->get($this->base.$path);
            $location = $response->header('Location');

            $this->out('  cache-control: '.($response->header('Cache-Control') ?: '(none)'));
            $this->out('  signed host:   '.(parse_url((string) $location, PHP_URL_HOST) ?: '(none)'));

            return [$response->status() === 302 && filled($location), (string) $response->status()];
        });

        $this->check('image downloads from the bucket', function () use ($path) {
            $redirect = Http::withoutRedirecting()->timeout(20)->get($this->base.$path);
            $location = $redirect->header('Location');

            if (blank($location)) {
                return [false, 'no redirect target'];
            }

            $image = Http::timeout(30)->get($location);
            $type = (string) $image->header('Content-Type');
            $bytes = strlen($image->body());

            return [
                $image->successful() && str_starts_with($type, 'image/') && $bytes > 1000,
                "{$image->status()} {$type} {$bytes}b",
            ];
        });
    }

    private function auditPersonas(): void
    {
        $this->out('--- PERSONAS ---');

        foreach (self::PERSONAS as $email => [$panel, $label]) {
            $jar = new CookieJar;

            $this->check("login {$label}", function () use ($email, $jar) {
                $token = $this->csrfToken($jar);

                if ($token === null) {
                    return [false, 'no csrf token on /login'];
                }

                $response = Http::withOptions(['cookies' => $jar, 'allow_redirects' => false])
                    ->timeout(30)
                    ->asForm()
                    ->post($this->base.'/login', [
                        '_token' => $token,
                        'email' => $email,
                        'password' => 'password',
                    ]);

                // A successful Fortify login redirects; a rejected one comes
                // back 302 to /login or 422, so check where it points.
                $target = (string) $response->header('Location');
                $ok = $response->status() === 302 && ! str_contains($target, '/login');

                return [$ok, $response->status().' -> '.($target ?: '(none)')];
            });

            $this->check("panel {$panel} ({$label})", function () use ($panel, $jar) {
                $started = microtime(true);
                $response = $this->client($jar)->get($this->base.$panel);
                $ms = (int) ((microtime(true) - $started) * 1000);
                $body = $response->body();

                $broken = null;

                foreach (['Whoops', 'SQLSTATE', 'Undefined variable', 'does not have a configured driver'] as $smell) {
                    if (str_contains($body, $smell)) {
                        $broken = $smell;
                        break;
                    }
                }

                return [
                    $response->successful() && $broken === null,
                    $response->status()." {$ms}ms".($broken ? " contains [{$broken}]" : ''),
                ];
            });
        }
    }

    private function auditAuthorization(): void
    {
        $this->out('--- AUTHORIZATION ---');

        // A tenant must not reach the admin panel.
        $jar = new CookieJar;
        $token = $this->csrfToken($jar);

        if ($token !== null) {
            Http::withOptions(['cookies' => $jar, 'allow_redirects' => false])
                ->timeout(30)
                ->asForm()
                ->post($this->base.'/login', [
                    '_token' => $token,
                    'email' => 'tenant@darhadhramaut.ye',
                    'password' => 'password',
                ]);

            $this->check('tenant is denied /admin', function () use ($jar) {
                $response = Http::withOptions(['cookies' => $jar, 'allow_redirects' => false])
                    ->timeout(30)
                    ->get($this->base.'/admin');

                // Anything but a rendered admin panel is acceptable here.
                $denied = $response->status() !== 200;

                return [$denied, (string) $response->status()];
            });
        }

        $this->check('guest is denied /admin', function () {
            $response = Http::withoutRedirecting()->timeout(30)->get($this->base.'/admin');

            return [$response->status() !== 200, (string) $response->status()];
        });
    }

    private function auditErrorHandling(): void
    {
        $this->out('--- ERROR HANDLING ---');

        $this->check('unknown page returns 404', function () {
            $response = $this->client()->get($this->base.'/this-page-does-not-exist-'.uniqid());

            return [$response->status() === 404, (string) $response->status()];
        });

        $this->check('unknown property returns 404', function () {
            $response = $this->client()->get($this->base.'/properties/99999999');

            return [in_array($response->status(), [404, 302], true), (string) $response->status()];
        });

        $this->check('debug details are not exposed', function () {
            $response = $this->client()->get($this->base.'/this-page-does-not-exist-'.uniqid());
            $body = $response->body();

            $leaks = str_contains($body, '/var/www/html/app/')
                || str_contains($body, 'Stack trace:')
                || str_contains($body, 'vendor/laravel/framework');

            return [! $leaks, $leaks ? 'LEAKS STACK TRACE' : 'clean'];
        });
    }

    private function firstPropertyId(): ?int
    {
        $body = $this->client()->get($this->base.'/properties')->body();

        return preg_match('#/properties/(\d+)#', $body, $m) ? (int) $m[1] : null;
    }

    private function csrfToken(CookieJar $jar): ?string
    {
        $body = $this->client($jar)->get($this->base.'/login')->body();

        return preg_match('/name="_token"\s+value="([^"]+)"/', $body, $m) ? $m[1] : null;
    }

    private function client(?CookieJar $jar = null)
    {
        $options = ['allow_redirects' => ['max' => 5]];

        if ($jar !== null) {
            $options['cookies'] = $jar;
        }

        return Http::withOptions($options)->timeout(45)->withoutVerifying();
    }

    private function check(string $label, callable $probe): void
    {
        try {
            [$ok, $note] = $probe();
        } catch (Throwable $e) {
            $ok = false;
            $note = 'EXCEPTION '.class_basename($e).': '.$e->getMessage();
        }

        if ($ok) {
            $this->passed++;
            $this->out("[PASS] {$label} -- {$note}");
        } else {
            $this->failed++;
            $this->failures[] = "{$label} -- {$note}";
            $this->out("[FAIL] {$label} -- {$note}");
        }
    }

    private function note(string $message): void
    {
        $this->out("[NOTE] {$message}");
    }

    private function out(string $line): void
    {
        $this->line('AUDIT| '.$line);
    }
}
