<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

foreach (User::all() as $user) {
    $roleFirst = $user->getRoleNames()->first();
    $roleNames = $user->getRoleNames()->toArray();
    echo "User: {$user->email} | First Role: " . json_encode($roleFirst) . " | All Roles: " . json_encode($roleNames) . "\n";
}
