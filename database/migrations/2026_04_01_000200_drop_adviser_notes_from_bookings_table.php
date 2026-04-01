<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('bookings', 'adviser_notes')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropColumn('adviser_notes');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('bookings', 'adviser_notes')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->text('adviser_notes')->nullable()->after('completion_notes');
            });
        }
    }
};
