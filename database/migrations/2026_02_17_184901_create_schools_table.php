<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');

            $table->string('school_name');
            $table->string('division');
            $table->string('district');
            $table->string('upazila');
            $table->string('village');

            $table->string('id_number');
            $table->string('eiin_number');

            $table->string('mobile');
            $table->string('email');

            $table->string('logo')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
