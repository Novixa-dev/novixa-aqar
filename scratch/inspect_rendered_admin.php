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
$content = $response->getContent();

echo "Content preview of <main>:\n";
if (preg_match('/<main[^>]*>(.*?)<\/main>/s', $content, $m)) {
    echo substr(strip_tags($m[1]), 0, 800) . "\n";
} else {
    echo "NO MAIN TAG FOUND\n";
}

echo "\nSidebar content preview:\n";
if (preg_match('/<aside[^>]*>(.*?)<\/aside>/s', $content, $m)) {
    echo substr(strip_tags($m[1]), 0, 800) . "\n";
} else {
    echo "NO ASIDE TAG FOUND\n";
}
