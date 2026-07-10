<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Bootstrapped\n";

$seeder = new Database\Seeders\DatabaseSeeder();
$seeder->run();
echo "Seeder finished\n";
