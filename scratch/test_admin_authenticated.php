<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('email', 'admin@example.com')->first();

$request = \Illuminate\Http\Request::create('/admin', 'GET');
$app->instance('request', $request);

$session = new \Illuminate\Session\Store('aqar_session', new \Illuminate\Session\ArraySessionHandler(60));
$session->setId(\Illuminate\Support\Str::random(40));
$session->start();
$session->put('login_web_' . sha1('Illuminate\Auth\SessionGuard'), $user->id);
$request->setLaravelSession($session);
$request->setUserResolver(fn() => $user);
auth()->guard('web')->setUser($user);

$response = $kernel->handle($request);
echo "Admin Dashboard Status: " . $response->getStatusCode() . "\n";
$content = $response->getContent();
echo "Content length: " . strlen($content) . " bytes\n";

if ($response->getStatusCode() === 200) {
    if (str_contains($content, 'Total Properties')) {
        echo "Found PropertyStatsOverview widget: YES\n";
    }
    if (str_contains($content, 'Latest Properties')) {
        echo "Found Latest Properties widget: YES\n";
    }
    if (str_contains($content, 'Recent Bookings')) {
        echo "Found Recent Bookings widget: YES\n";
    }
    if (str_contains($content, 'Welcome') || str_contains($content, 'Admin User')) {
        echo "Found Account widget: YES\n";
    }
    // Check sidebar navigation groups
    preg_match_all('/<span[^>]*class="[^"]*fi-sidebar-item-label[^"]*"[^>]*>([^<]+)<\/span>/i', $content, $matches);
    echo "Sidebar items count: " . count($matches[1]) . "\n";
    echo "First 10 Sidebar items: " . implode(', ', array_slice($matches[1], 0, 10)) . "...\n";
} else {
    echo "Response preview:\n" . substr($content, 0, 500) . "\n";
}
