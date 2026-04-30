<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$u = \Illuminate\Support\Facades\DB::table('users')->where('username', 'admin')->first();
echo "admin raw pass: " . $u->password . "\n";
