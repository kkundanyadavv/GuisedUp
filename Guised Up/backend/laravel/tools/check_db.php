<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Models\User;
use App\Models\Post;
use App\Models\Interaction;
use App\Models\Follow;

echo "Users: " . User::count() . PHP_EOL;
echo "Posts: " . Post::count() . PHP_EOL;
echo "Interactions: " . Interaction::count() . PHP_EOL;
echo "Follows: " . Follow::count() . PHP_EOL;
