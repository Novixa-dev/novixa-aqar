<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('email', 'admin@example.com')->first();
auth()->setUser($user);

echo "User: " . $user->name . " (Roles: " . $user->roles->pluck('name')->implode(', ') . ")\n";

$panel = \Filament\Facades\Filament::getPanel('admin');
\Filament\Facades\Filament::setCurrentPanel($panel);

foreach ($panel->getResources() as $resource) {
    $can = $resource::canViewAny();
    $navLabel = $resource::getNavigationLabel();
    echo ($can ? "[ALLOWED] " : "[DENIED]  ") . $resource . " -> " . $navLabel . "\n";
}
