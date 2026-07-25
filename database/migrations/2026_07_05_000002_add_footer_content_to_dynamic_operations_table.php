<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dynamic_operations', function (Blueprint $table) {
            $table->text('footer_description')->nullable()->after('promotion_text');
            $table->json('footer_trust_badges')->nullable()->after('footer_description');
        });
    }

    public function down(): void
    {
        Schema::table('dynamic_operations', function (Blueprint $table) {
            $table->dropColumn(['footer_description', 'footer_trust_badges']);
        });
    }
};
