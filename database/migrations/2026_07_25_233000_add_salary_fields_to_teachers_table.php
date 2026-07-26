<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->decimal('salary_amount', 10, 2)->nullable()->after('mobile');
            $table->date('salary_start_date')->nullable()->after('salary_amount');
            $table->string('pay_date')->nullable()->after('salary_start_date');
        });
    }

    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn(['salary_amount', 'salary_start_date', 'pay_date']);
        });
    }
};
