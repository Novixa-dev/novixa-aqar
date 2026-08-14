<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Bookings table columns: \n";
print_r(\Illuminate\Support\Facades\Schema::getColumnListing('bookings'));

echo "\nTransactions table columns: \n";
print_r(\Illuminate\Support\Facades\Schema::getColumnListing('transactions'));
