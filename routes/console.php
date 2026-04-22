<?php

use Illuminate\Support\Facades\Schedule;

// تنظيف توكنات استعادة كلمة المرور كل ساعة
Schedule::command('auth:clear-resets')->hourly();

// تنظيف توكنات API المنتهية يومياً لتقليل حجم الداتابيز
Schedule::command('sanctum:prune-expired --hours=24')->daily();
