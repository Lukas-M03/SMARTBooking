<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('adviser_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('expertise_id')->constrained('expertise')->onDelete('cascade');
            $table->string('topic');
            $table->text('description')->nullable();
            $table->dateTime('preferred_datetime');
            $table->enum('meeting_type', ['in-person', 'online', 'phone'])->default('in-person');
            $table->enum('status', ['pending', 'confirmed', 'denied', 'cancelled', 'completed'])->default('pending');
            $table->text('adviser_notes')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('scheduled_deletion_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
