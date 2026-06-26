<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$client = DB::table('external_clients')->where('username', 'client1')->first();
if ($client) {
    echo "Found client: " . $client->full_name . " (" . $client->email . ")\n";
} else {
    echo "Client not found\n";
}
