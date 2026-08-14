<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('email', 'admin@example.com')->first();
auth()->login($user);

$request = \Illuminate\Http\Request::create('/admin', 'GET');
$request->setUserResolver(fn () => $user);

$response = $kernel->handle($request);
echo "Status Code: " . $response->getStatusCode() . "\n";
$content = $response->getContent();
echo "Content Length: " . strlen($content) . " bytes\n";

if ($response->getStatusCode() === 200) {
    echo "Widgets or Stats in HTML:\n";
    if (str_contains($content, 'fi-wi-stats-overview')) {
        echo " - Stats Overview Widget: YES\n";
    }
    if (str_contains($content, 'fi-wi-table')) {
        echo " - Table Widgets: YES\n";
    }
    
    // Check main dashboard content
    if (preg_match('/<main[^>]*>(.*?)<\/main>/s', $content, $mainMatch)) {
        echo "Main content length: " . strlen($mainMatch[1]) . " bytes\n";
        echo "Main content text:\n" . trim(preg_replace('/\s+/', ' ', strip_tags($mainMatch[1]))) . "\n";
    }
} else {
    echo "Response preview:\n" . substr($content, 0, 500) . "\n";
}
