<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Recalculate late-delivery penalties on open purchase orders (was defined in the unused app/Console/Kernel.php).
Schedule::command('purchase-orders:update-penalties')->daily();
