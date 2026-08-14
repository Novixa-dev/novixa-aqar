<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$panel = \Filament\Facades\Filament::getPanel('admin');
echo "Panel ID: " . $panel->getId() . "\n";
echo "Resources registered: " . count($panel->getResources()) . "\n";
foreach ($panel->getResources() as $res) {
    echo " - " . $res . "\n";
}

echo "\nPages registered: " . count($panel->getPages()) . "\n";
foreach ($panel->getPages() as $page) {
    echo " - " . $page . "\n";
}

echo "\nWidgets registered: " . count($panel->getWidgets()) . "\n";
foreach ($panel->getWidgets() as $w) {
    echo " - " . $w . "\n";
}
