<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Keep visitor analytics tidy (runs when the Laravel scheduler is active: php artisan schedule:work).
Schedule::command('analytics:prune')->weekly();
