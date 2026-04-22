<?php

use Illuminate\Support\Facades\Schedule;

// هنخلي الـ Worker يشتغل لمدة 55 ثانية عشان يغطي الدقيقة كلها
// ده هيخلي الإرسال أسرع بكتير لأن الـ Worker هيفضل مستني أي جوب جديد
Schedule::command('queue:work --stop-when-empty --max-time=55')
    ->everyMinute()
    ->withoutOverlapping();

// تنظيف التوكنات
Schedule::command('auth:clear-resets')->hourly();
Schedule::command('sanctum:prune-expired --hours=24')->daily();
