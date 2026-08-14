<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$items = \App\Models\Menu::all();
echo "TOTAL MENUS IN DB: " . $items->count() . "\n";
foreach ($items as $i) {
    echo "ID: {$i->id} | Name: {$i->name} | URL: {$i->url} | Parent: {$i->parent_id} | Order: {$i->order}\n";
}
