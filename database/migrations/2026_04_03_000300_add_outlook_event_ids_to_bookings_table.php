<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('student_outlook_event_id')->nullable()->after('scheduled_deletion_at');
            $table->string('adviser_outlook_event_id')->nullable()->after('student_outlook_event_id');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['student_outlook_event_id', 'adviser_outlook_event_id']);
        });
    }
};