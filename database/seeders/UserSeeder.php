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
        // Use updateOrCreate so seeding can be safely re-run without duplicates.
        // This is important because expertise is reseeded and adviser mappings must be restored.

        // Create Admin
        User::updateOrCreate(['email' => 'admin@smartbooking.com'], [
            'name' => 'System Administrator',
            'email' => 'admin@smartbooking.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create Studies Advisers
        // Each adviser is mapped to exactly one module using sync([...]).
        // sync() ensures stale pivot rows are replaced on reseed.
        $adviser1 = User::updateOrCreate(['email' => 'sarah.thompson@smartbooking.com'], [
            'name' => 'Dr. Sarah Thompson',
            'email' => 'sarah.thompson@smartbooking.com',
            'password' => Hash::make('password'),
            'role' => 'adviser',
            'phone' => '+44 20 1234 5678',
        ]);
        $adviser1->expertise()->sync([
            Expertise::where('name', 'Computer Science')->firstOrFail()->id
        ]);

        $adviser2 = User::updateOrCreate(['email' => 'michael.chen@smartbooking.com'], [
            'name' => 'Prof. Michael Chen',
            'email' => 'michael.chen@smartbooking.com',
            'password' => Hash::make('password'),
            'role' => 'adviser',
            'phone' => '+44 20 1234 5679',
        ]);
        $adviser2->expertise()->sync([
            Expertise::where('name', 'Mathematics')->firstOrFail()->id
        ]);

        $adviser3 = User::updateOrCreate(['email' => 'emily.roberts@smartbooking.com'], [
            'name' => 'Dr. Emily Roberts',
            'email' => 'emily.roberts@smartbooking.com',
            'password' => Hash::make('password'),
            'role' => 'adviser',
            'phone' => '+44 20 1234 5680',
        ]);
        $adviser3->expertise()->sync([
            Expertise::where('name', 'Biology')->firstOrFail()->id
        ]);

        // Create Sample Students
        // Students are also idempotent to avoid duplicate records across runs.
        User::updateOrCreate(['email' => 'john.smith@student.ac.uk'], [
            'name' => 'John Smith',
            'email' => 'john.smith@student.ac.uk',
            'password' => Hash::make('password'),
            'role' => 'student',
            'student_id' => 'ST2024001',
            'phone' => '+44 7700 900001',
        ]);

        User::updateOrCreate(['email' => 'emma.johnson@student.ac.uk'], [
            'name' => 'Emma Johnson',
            'email' => 'emma.johnson@student.ac.uk',
            'password' => Hash::make('password'),
            'role' => 'student',
            'student_id' => 'ST2024002',
            'phone' => '+44 7700 900002',
        ]);

        User::updateOrCreate(['email' => 'james.williams@student.ac.uk'], [
            'name' => 'James Williams',
            'email' => 'james.williams@student.ac.uk',
            'password' => Hash::make('password'),
            'role' => 'student',
            'student_id' => 'ST2024003',
            'phone' => '+44 7700 900003',
        ]);
    }
}
