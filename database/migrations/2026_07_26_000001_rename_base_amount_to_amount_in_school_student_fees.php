<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE school_student_fees CHANGE base_amount amount DECIMAL(12, 2) DEFAULT 0.00");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE school_student_fees CHANGE amount base_amount DECIMAL(12, 2) DEFAULT 0.00");
    }
};
