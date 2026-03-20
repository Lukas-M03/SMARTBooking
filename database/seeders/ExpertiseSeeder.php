<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Expertise;

class ExpertiseSeeder extends Seeder
{
    public function run(): void
    {
		// Replace existing module rows so old names are removed from UI dropdowns.
		Expertise::query()->delete();

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

        // Seed the canonical module list used by student/adviser registration.
        foreach ($modules as $module) {
            Expertise::create($module);
        }
    }
}
