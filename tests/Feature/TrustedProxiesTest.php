<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class TrustedProxiesTest extends TestCase
{
    /**
     * Railway terminates TLS at its edge and forwards over plain HTTP.
     *
     * Without trusting the forwarded headers the application believes every
     * request is insecure, and every generated absolute URL and post-login
     * redirect comes out as http:// on an https-only site. That was the
     * live behaviour until trustProxies was registered: the repo carried a
     * TrustProxies middleware class written for Laravel 10, which nothing
     * in bootstrap/app.php ever wired up.
     */
    public function test_a_forwarded_https_request_is_treated_as_secure(): void
    {
        $request = Request::create('http://example.test/up', 'GET');
        $request->headers->set('X-Forwarded-Proto', 'https');
        $request->headers->set('X-Forwarded-For', '203.0.113.10');

        $response = $this->app->handle($request);

        $this->assertTrue(
            $request->isSecure(),
            'A request forwarded with X-Forwarded-Proto: https was not treated as secure. '
            .'Generated URLs and redirects will use http:// on an https-only deployment.',
        );

        $this->assertLessThan(500, $response->getStatusCode());
    }
}
