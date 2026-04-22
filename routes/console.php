<?php

use Illuminate\Support\Facades\Schedule;

// تشغيل الـ Queue Worker كل دقيقة لمدة 25 ثانية
Schedule::command('queue:work --stop-when-empty --max-time=25')
    ->everyMinute();

// تنظيف التوكنات
Schedule::command('auth:clear-resets')->hourly();
Schedule::command('sanctum:prune-expired --hours=24')->daily();
