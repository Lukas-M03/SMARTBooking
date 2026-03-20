<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Expertise;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin
        User::create([
            'name' => 'System Administrator',
            'email' => 'admin@smartbooking.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create Studies Advisers
        $adviser1 = User::create([
            'name' => 'Dr. Sarah Thompson',
            'email' => 'sarah.thompson@smartbooking.com',
            'password' => Hash::make('password'),
            'role' => 'adviser',
            'phone' => '+44 20 1234 5678',
        ]);
        $adviser1->expertise()->attach(
            Expertise::where('name', 'Computer Science')->firstOrFail()->id
        );

        $adviser2 = User::create([
            'name' => 'Prof. Michael Chen',
            'email' => 'michael.chen@smartbooking.com',
            'password' => Hash::make('password'),
            'role' => 'adviser',
            'phone' => '+44 20 1234 5679',
        ]);
        $adviser2->expertise()->attach(
            Expertise::where('name', 'Mathematics')->firstOrFail()->id
        );

        $adviser3 = User::create([
            'name' => 'Dr. Emily Roberts',
            'email' => 'emily.roberts@smartbooking.com',
            'password' => Hash::make('password'),
            'role' => 'adviser',
            'phone' => '+44 20 1234 5680',
        ]);
        $adviser3->expertise()->attach(
            Expertise::where('name', 'Biology')->firstOrFail()->id
        );

        // Create Sample Students
        User::create([
            'name' => 'John Smith',
            'email' => 'john.smith@student.ac.uk',
            'password' => Hash::make('password'),
            'role' => 'student',
            'student_id' => 'ST2024001',
            'phone' => '+44 7700 900001',
        ]);

        User::create([
            'name' => 'Emma Johnson',
            'email' => 'emma.johnson@student.ac.uk',
            'password' => Hash::make('password'),
            'role' => 'student',
            'student_id' => 'ST2024002',
            'phone' => '+44 7700 900002',
        ]);

        User::create([
            'name' => 'James Williams',
            'email' => 'james.williams@student.ac.uk',
            'password' => Hash::make('password'),
            'role' => 'student',
            'student_id' => 'ST2024003',
            'phone' => '+44 7700 900003',
        ]);
    }
}
