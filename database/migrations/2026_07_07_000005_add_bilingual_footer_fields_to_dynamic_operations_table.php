<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('dynamic_operations')) {
            return;
        }

        Schema::table('dynamic_operations', function (Blueprint $table) {
            if (! Schema::hasColumn('dynamic_operations', 'brand_title_en')) {
                $table->string('brand_title_en')->nullable()->after('brand_title');
            }

            if (! Schema::hasColumn('dynamic_operations', 'brand_title_bn')) {
                $table->string('brand_title_bn')->nullable()->after('brand_title_en');
            }

            if (! Schema::hasColumn('dynamic_operations', 'footer_description_en')) {
                $table->text('footer_description_en')->nullable()->after('footer_description');
            }

            if (! Schema::hasColumn('dynamic_operations', 'footer_description_bn')) {
                $table->text('footer_description_bn')->nullable()->after('footer_description_en');
            }

            if (! Schema::hasColumn('dynamic_operations', 'footer_trust_badges_i18n')) {
                $table->json('footer_trust_badges_i18n')->nullable()->after('footer_trust_badges');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('dynamic_operations')) {
            return;
        }

        $columns = collect([
            'brand_title_en',
            'brand_title_bn',
            'footer_description_en',
            'footer_description_bn',
            'footer_trust_badges_i18n',
        ])
            ->filter(fn (string $column) => Schema::hasColumn('dynamic_operations', $column))
            ->values()
            ->all();

        if ($columns === []) {
            return;
        }

        Schema::table('dynamic_operations', function (Blueprint $table) use ($columns) {
            $table->dropColumn($columns);
        });
    }
};
