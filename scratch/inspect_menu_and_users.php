<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "MENUS IN DATABASE:\n";
foreach (\App\Models\Menu::all() as $menu) {
    echo "ID: {$menu->id} | Name: {$menu->name} | URL: {$menu->url} | Parent: {$menu->parent_id}\n";
}

echo "\nTEST USERS & ROLES:\n";
foreach (\App\Models\User::all() as $u) {
    $roles = implode(', ', $u->getRoleNames()->toArray());
    echo "ID: {$u->id} | Name: {$u->name} | Email: {$u->email} | Roles: [{$roles}]\n";
}
