<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Expertise;

class ExpertiseSeeder extends Seeder
{
    public function run(): void
    {
        $expertiseAreas = [
            [
                'name' => 'Academic Performance & Study Skills',
                'description' => 'Help with improving grades, study techniques, time management, and exam preparation.'
            ],
            [
                'name' => 'Dissertation & Research Guidance',
                'description' => 'Support for dissertation planning, research methodology, and academic writing.'
            ],
            [
                'name' => 'Module Selection & Course Planning',
                'description' => 'Guidance on choosing modules, understanding course requirements, and academic pathway planning.'
            ],
            [
                'name' => 'Assignment Help & Feedback',
                'description' => 'Assistance with understanding assignment requirements, structure, and incorporating feedback.'
            ],
            [
                'name' => 'Personal & Wellbeing Support',
                'description' => 'Support for personal issues affecting studies, stress management, and work-life balance.'
            ],
            [
                'name' => 'Career & Employability Advice',
                'description' => 'Guidance on career planning, CV writing, interview preparation, and work experience.'
            ],
            [
                'name' => 'Technical & Programming Support',
                'description' => 'Help with programming assignments, debugging code, and understanding technical concepts.'
            ],
            [
                'name' => 'Mathematics & Statistics',
                'description' => 'Support with mathematical concepts, statistical analysis, and quantitative methods.'
            ],
        ];

        foreach ($expertiseAreas as $area) {
            Expertise::create($area);
        }
    }
}
