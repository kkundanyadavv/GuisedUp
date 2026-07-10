<?php
$outFile = __DIR__ . '/seed_result.txt';
ob_start();
require __DIR__ . '/pdo_seed.php';
$contents = ob_get_clean();
file_put_contents($outFile, $contents);
echo "WROTE: " . $outFile . PHP_EOL;
