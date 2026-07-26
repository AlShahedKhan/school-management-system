<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'invoice_no')) {
                $table->string('invoice_no')->nullable()->after('school_id');
            }
            if (!Schema::hasColumn('expenses', 'details')) {
                $table->text('details')->nullable()->after('expense_reason');
            }
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['invoice_no', 'details']);
        });
    }
};
