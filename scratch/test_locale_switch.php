<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

// 1. Send request to switch to english
$request1 = \Illuminate\Http\Request::create('/lang/en', 'GET');
$response1 = $kernel->handle($request1);

echo "Switch to EN Status: " . $response1->getStatusCode() . "\n";
$cookies = $response1->headers->getCookies();
$cookieVal = null;
foreach ($cookies as $c) {
    if ($c->getName() === 'locale') {
        $cookieVal = $c->getValue();
        echo "Got locale cookie: " . $cookieVal . "\n";
    }
}

// 2. Send request to / with the cookie or session
$request2 = \Illuminate\Http\Request::create('/', 'GET');
if ($cookieVal) {
    $request2->cookies->set('locale', $cookieVal);
}
$response2 = $kernel->handle($request2);
echo "GET / Status: " . $response2->getStatusCode() . "\n";
$html = $response2->getContent();

if (str_contains($html, 'dir="ltr"') && str_contains($html, 'lang="en"')) {
    echo "SUCCESS: Page rendered in English with dir=ltr!\n";
} else {
    echo "HTML snippet: " . substr($html, 0, 200) . "\n";
}
