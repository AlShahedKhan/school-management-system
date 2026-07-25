<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_groups', function (Blueprint $table) {
            $table->dropColumn([
                'section',
                'session',
                'roll',
                'student_id_number',
                'student_name',
                'total_payable',
                'payable_due',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('school_groups', function (Blueprint $table) {
            $table->string('section')->nullable()->after('group_name');
            $table->string('session')->nullable()->after('section');
            $table->string('roll')->nullable()->after('session');
            $table->string('student_id_number')->nullable()->after('roll');
            $table->string('student_name')->nullable()->after('student_id_number');
            $table->decimal('total_payable', 12, 2)->nullable()->default(0)->after('student_name');
            $table->decimal('payable_due', 12, 2)->nullable()->default(0)->after('total_payable');
        });
    }
};
