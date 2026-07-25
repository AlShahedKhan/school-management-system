<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('incomes', function (Blueprint $blueprint) {
            // This adds the Member Number string after school_id
            $blueprint->string('member_no')->nullable()->after('school_id');
        });
    }

    public function down(): void
    {
        Schema::table('incomes', function (Blueprint $blueprint) {
            $blueprint->dropColumn('member_no');
        });
    }
};