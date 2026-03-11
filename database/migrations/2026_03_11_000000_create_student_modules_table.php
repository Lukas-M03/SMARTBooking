<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Creates the student_modules pivot table.
     * Links students to the expertise/module areas they need help with.
     */
    public function up(): void
    {
        Schema::create('student_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('expertise_id')->constrained('expertise')->onDelete('cascade');
            $table->unique(['student_id', 'expertise_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_modules');
    }
};
