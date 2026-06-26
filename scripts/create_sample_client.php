<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

DB::table('external_clients')->insert([
    'company_name' => 'Acme Corp',
    'full_name' => 'John Doe',
    'email' => 'client1@acme.test',
    'phone' => '555-0101',
    'username' => 'client1',
    'password' => Hash::make('clientpass'),
    'assigned_to_user_id' => 2,
    'category' => 'Standard',
    'status' => 'active',
    'created_by' => 2,
    'created_at' => now(),
    'updated_at' => now(),
]);

echo "Inserted sample external client\n";
