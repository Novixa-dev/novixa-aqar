<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('email', 'admin@example.com')->first();
if (!$user) {
    echo "Admin user not found!\n";
    exit(1);
}

echo "User: " . $user->name . " (ID: " . $user->id . ")\n";
echo "Roles: " . $user->roles->pluck('name')->implode(', ') . "\n";
echo "Direct permissions: " . $user->permissions->count() . "\n";
echo "All permissions count: " . $user->getAllPermissions()->count() . "\n";
echo "Has super_admin role? " . ($user->hasRole('super_admin') ? 'YES' : 'NO') . "\n";
echo "Has admin role? " . ($user->hasRole('admin') ? 'YES' : 'NO') . "\n";

// Check SuperAdmin gate / shield config
$superAdminRole = config('filament-shield.super_admin.name', 'super_admin');
echo "Shield super admin role config: " . $superAdminRole . "\n";
