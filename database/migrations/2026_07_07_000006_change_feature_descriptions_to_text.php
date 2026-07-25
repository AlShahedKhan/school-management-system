<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('features', function (Blueprint $table) {
            $table->text('description_en')->change();
            $table->text('description_bn')->change();
        });
    }

    public function down(): void
    {
        Schema::table('features', function (Blueprint $table) {
            $table->string('description_en', 180)->change();
            $table->string('description_bn', 180)->change();
        });
    }
};
