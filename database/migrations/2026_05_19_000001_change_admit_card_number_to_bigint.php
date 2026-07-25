<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_exam_admit_cards', function (Blueprint $table) {
            $table->dropColumn(['admit_card_start_number', 'admit_card_end_number']);
        });

        Schema::table('school_exam_admit_cards', function (Blueprint $table) {
            $table->dropUnique(['admit_card_number']);
        });

        Schema::table('school_exam_admit_cards', function (Blueprint $table) {
            $table->bigInteger('admit_card_number')->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('school_exam_admit_cards', function (Blueprint $table) {
            $table->dropUnique(['admit_card_number']);
        });

        Schema::table('school_exam_admit_cards', function (Blueprint $table) {
            $table->string('admit_card_number')->unique()->change();
        });

        Schema::table('school_exam_admit_cards', function (Blueprint $table) {
            $table->string('admit_card_start_number')->nullable();
            $table->string('admit_card_end_number')->nullable();
        });
    }
};
