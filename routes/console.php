<?php

use Illuminate\Support\Facades\Schedule;

// تنفيذ الـ Queue Worker للمهام المعلقة وإنهاؤه فوراً عند الفراغ لمنع استهلاك السيرفر
Schedule::command('queue:work --stop-when-empty')
    ->everyMinute()
    ->withoutOverlapping();

// تنظيف التوكنات
Schedule::command('auth:clear-resets')->hourly();
Schedule::command('sanctum:prune-expired --hours=24')->daily();
