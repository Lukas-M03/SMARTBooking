<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Expertise;

class ExpertiseSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            ['name' => 'Computer Science'],
            ['name' => 'Biology'],
            ['name' => 'Mathematics'],
            ['name' => 'Physics'],
            ['name' => 'Chemistry'],
            ['name' => 'English Literature'],
            ['name' => 'Business Studies'],
            ['name' => 'Psychology'],
        ];

        foreach ($modules as $module) {
            Expertise::create($module);
        }
    }
}
