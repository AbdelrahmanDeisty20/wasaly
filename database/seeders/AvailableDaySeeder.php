<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AvailableDaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $days = [
            ['name_ar' => 'السبت', 'name_en' => 'Saturday'],
            ['name_ar' => 'الأحد', 'name_en' => 'Sunday'],
            ['name_ar' => 'الاثنين', 'name_en' => 'Monday'],
            ['name_ar' => 'الثلاثاء', 'name_en' => 'Tuesday'],
            ['name_ar' => 'الأربعاء', 'name_en' => 'Wednesday'],
            ['name_ar' => 'الخميس', 'name_en' => 'Thursday'],
            ['name_ar' => 'الجمعة', 'name_en' => 'Friday'],
        ];

        foreach ($days as $day) {
            \App\Models\AvailableDay::updateOrCreate(['name_en' => $day['name_en']], $day);
        }
    }
}
