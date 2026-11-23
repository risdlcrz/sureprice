<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Recalculate late-delivery penalties on open purchase orders (was defined in the unused app/Console/Kernel.php).
Schedule::command('purchase-orders:update-penalties')->daily();

// Recovery when no administrator can sign in to unlock an account (e.g. an admin auto-locked after failed sign-ins).
Artisan::command('users:unlock {login : The email or username of the account}', function (string $login) {
    $user = \App\Models\User::where('email', $login)->orWhere('username', $login)->first();
    if (!$user) {
        $this->error('No account matches that email or username.');

        return 1;
    }
    if (!$user->isLocked()) {
        $this->info("{$user->name}'s account is not locked.");

        return 0;
    }
    $user->unlock();
    $this->info("{$user->name}'s account has been unlocked.");

    return 0;
})->purpose('Unlock a locked user account');
