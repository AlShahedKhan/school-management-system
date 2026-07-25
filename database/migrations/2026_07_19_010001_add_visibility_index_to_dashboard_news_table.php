<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dashboard_news', function (Blueprint $table) {
            $table->index(['is_active', 'starts_at', 'ends_at', 'sort_order'], 'dashboard_news_visibility_index');
        });
    }

    public function down(): void
    {
        Schema::table('dashboard_news', function (Blueprint $table) {
            $table->dropIndex('dashboard_news_visibility_index');
        });
    }
};
