<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "=== Admin User Check ===\n";

// Check all users with admin role or user_type
echo "Users with role='admin':\n";
$adminUsers = User::where('role', 'admin')->get();
foreach ($adminUsers as $user) {
    echo "- " . $user->name . " (" . $user->email . ") - Role: " . $user->role . ", Type: " . $user->user_type . "\n";
}

echo "\nUsers with user_type='admin':\n";
$adminTypeUsers = User::where('user_type', 'admin')->get();
foreach ($adminTypeUsers as $user) {
    echo "- " . $user->name . " (" . $user->email . ") - Role: " . $user->role . ", Type: " . $user->user_type . "\n";
}

echo "\nUsers with role='manager':\n";
$managerUsers = User::where('role', 'manager')->get();
foreach ($managerUsers as $user) {
    echo "- " . $user->name . " (" . $user->email . ") - Role: " . $user->role . ", Type: " . $user->user_type . "\n";
}

// Get the first admin user for testing
$adminUser = User::where('role', 'admin')->first();

if ($adminUser) {
    echo "\n=== Login Logic Test for Admin User ===\n";
    echo "Testing user: " . $adminUser->name . " (" . $adminUser->email . ")\n";
    
    if ($adminUser->role === 'manager') {
        echo "Would redirect to: manager.dashboard\n";
    } elseif ($adminUser->role === 'admin') {
        echo "Would redirect to: admin.dashboard\n";
    } elseif ($adminUser->user_type === 'employee') {
        echo "Would redirect to: employee dashboard\n";
    } elseif ($adminUser->user_type === 'company') {
        echo "Would redirect to: company dashboard\n";
    } else {
        echo "Would redirect to: pending.approval (default)\n";
    }
} else {
    echo "\nNo admin user found\n";
}

echo "\n=== Test Complete ===\n"; 