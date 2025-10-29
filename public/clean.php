<?php
use Illuminate\Support\Facades\Artisan;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$commands = [
    'cache:clear',
    'config:clear',
    'route:clear',
    'view:clear',
    'event:clear',
    'clear-compiled',
    'optimize:clear',
    'optimize',
];

foreach ($commands as $command) {
    echo "Ejecutando: php artisan $command<br>";
    $kernel->call($command);
    echo nl2br($kernel->output()) . "<br>";
}