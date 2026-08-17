<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

echo "--- GUEST GET /admin ---\n";
$request = Request::create('/admin', 'GET');
$response = $app->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";
if ($response->isRedirection()) {
    echo "Redirect URL: " . $response->headers->get('Location') . "\n";
}

$admin = User::where('email', 'admin@example.com')->first();
echo "\n--- AUTH ADMIN (admin@example.com) GET /admin ---\n";
Auth::login($admin);
$request = Request::create('/admin', 'GET');
$response = $app->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";
if ($response->isRedirection()) {
    echo "Redirect URL: " . $response->headers->get('Location') . "\n";
}

$user = User::where('email', 'buyer@example.com')->first();
echo "\n--- AUTH BUYER (buyer@example.com) GET /admin ---\n";
Auth::login($user);
$request = Request::create('/admin', 'GET');
$response = $app->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";
if ($response->isRedirection()) {
    echo "Redirect URL: " . $response->headers->get('Location') . "\n";
}
