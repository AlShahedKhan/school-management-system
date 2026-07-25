<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('demo_requests')) {
            return;
        }

        Schema::table('demo_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('demo_requests', 'student_qty')) {
                $table->unsignedInteger('student_qty')->nullable()->after('phone');
            }

            if (! Schema::hasColumn('demo_requests', 'booking_date')) {
                $table->date('booking_date')->nullable()->after('student_qty');
            }

            if (! Schema::hasColumn('demo_requests', 'booking_time')) {
                $table->time('booking_time')->nullable()->after('booking_date');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('demo_requests')) {
            return;
        }

        Schema::table('demo_requests', function (Blueprint $table) {
            if (Schema::hasColumn('demo_requests', 'booking_time')) {
                $table->dropColumn('booking_time');
            }

            if (Schema::hasColumn('demo_requests', 'booking_date')) {
                $table->dropColumn('booking_date');
            }

            if (Schema::hasColumn('demo_requests', 'student_qty')) {
                $table->dropColumn('student_qty');
            }
        });
    }
};
