<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_payments', function (Blueprint $table) {
            $table->string('for_month', 7)->nullable()->after('pay_date');
            $table->index('for_month');
        });
    }

    public function down(): void
    {
        Schema::table('school_payments', function (Blueprint $table) {
            $table->dropIndex(['for_month']);
            $table->dropColumn('for_month');
        });
    }
};
