<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('admission_students', function (Blueprint $table) {

            $table->enum('status',['pending','approved','rejected'])
                  ->default('pending')
                  ->after('image');

        });
    }

    public function down()
    {
        Schema::table('admission_students', function (Blueprint $table) {

            $table->dropColumn('status');

        });
    }
};
