<?php
use Illuminate\Support\Facades\Mail;
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing SMTP Connection...\n";
try {
    $transport = app('mailer')->getSymfonyTransport();
    $transport->start();
    echo "Connection Successful!\n";
} catch (\Exception $e) {
    echo "Connection Failed: " . $e->getMessage() . "\n";
}
