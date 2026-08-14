<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

echo "Checking global vs web middleware...\n";
// Let's test language switch flow
$request1 = \Illuminate\Http\Request::create('/lang/en', 'GET');
$response1 = $kernel->handle($request1);

$cookies = $response1->headers->getCookies();
echo "Cookies returned from /lang/en: " . count($cookies) . "\n";
foreach ($cookies as $cookie) {
    echo "Cookie: " . $cookie->getName() . "\n";
}
