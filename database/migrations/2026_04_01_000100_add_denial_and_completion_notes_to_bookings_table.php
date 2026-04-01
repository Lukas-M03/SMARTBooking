<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->text('denial_reason')->nullable()->after('status');
            $table->text('completion_notes')->nullable()->after('denial_reason');
        });

        // Backfill old adviser_notes into the new columns by booking status.
        DB::table('bookings')
            ->where('status', 'denied')
            ->whereNotNull('adviser_notes')
            ->update(['denial_reason' => DB::raw('adviser_notes')]);

        DB::table('bookings')
            ->where('status', 'completed')
            ->whereNotNull('adviser_notes')
            ->update(['completion_notes' => DB::raw('adviser_notes')]);
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['denial_reason', 'completion_notes']);
        });
    }
};
