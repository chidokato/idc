<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::first();
if ($user) {
    echo "First user: " . $user->id . " - dept2: " . $user->department_lv2 . "\n";
    $users = App\Models\User::select('id', 'yourname', 'email', 'employee_code')
            ->where('id', '!=', $user->id)
            ->where('department_lv2', $user->department_lv2)
            ->orderBy('email')
            ->limit(500)
            ->get();
    echo "Users found for this user: " . count($users) . "\n";
} else {
    echo "No users in DB.\n";
}
