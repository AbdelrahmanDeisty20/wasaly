<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('queue:work --max-time=30')
    ->everyMinute()
    ->withoutOverlapping();

// Clean up expired password reset tokens hourly
Schedule::command('auth:clear-resets')->hourly();

// Clean up expired API tokens daily to keep the database light
Schedule::command('sanctum:prune-expired --hours=24')->daily();

Schedule::command('queue:work --max-time=30')
    ->everyMinute()
    ->withoutOverlapping();

// Clean up expired password reset tokens hourly
Schedule::command('auth:clear-resets')->hourly();

// Clean up expired API tokens daily to keep the database light
Schedule::command('sanctum:prune-expired --hours=24')->daily();
