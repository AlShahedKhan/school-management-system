<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('home_page_settings', function (Blueprint $table) {
            $table->string('hero_activity_title')->nullable()->after('hero_attendance_value');
        });
    }

    public function down(): void
    {
        Schema::table('home_page_settings', function (Blueprint $table) {
            $table->dropColumn('hero_activity_title');
        });
    }
};
