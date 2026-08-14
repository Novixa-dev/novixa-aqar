<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = \App\Models\User::where('email', 'admin@example.com')->first();
if (!$user) {
    echo "NO ADMIN USER FOUND!\n";
    exit;
}

echo "User ID: " . $user->id . "\n";
echo "User Name: " . $user->name . "\n";
echo "Roles: " . $user->roles->pluck('name')->implode(', ') . "\n";
echo "Permissions count: " . $user->getAllPermissions()->count() . "\n";
echo "Teams: " . $user->teams->pluck('name')->implode(', ') . "\n";

// Check super admin gate
echo "Is super admin gate passing? " . ($user->hasRole('super_admin') ? 'YES' : 'NO') . "\n";
echo "Has role admin? " . ($user->hasRole('admin') ? 'YES' : 'NO') . "\n";

// Check Filament panel access
echo "Can access Admin panel: ";
try {
    $panel = \Filament\Facades\Filament::getPanel('admin');
    echo ($user->canAccessPanel($panel) ? 'YES' : 'NO') . "\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

// Check registered resources for Admin panel
try {
    \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));
    $resources = \Filament\Facades\Filament::getResources();
    echo "Total Registered Resources in Admin Panel: " . count($resources) . "\n";
    foreach ($resources as $res) {
        $canView = $res::canViewAny();
        echo " - " . class_basename($res) . ": canViewAny = " . ($canView ? 'TRUE' : 'FALSE') . "\n";
    }
} catch (\Throwable $e) {
    echo "ERROR checking resources: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}
