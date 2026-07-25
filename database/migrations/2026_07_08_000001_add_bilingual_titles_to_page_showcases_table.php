<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('page_showcases')) {
            return;
        }

        Schema::table('page_showcases', function (Blueprint $table) {
            if (! Schema::hasColumn('page_showcases', 'title_en')) {
                $table->string('title_en')->nullable()->after('title');
            }

            if (! Schema::hasColumn('page_showcases', 'title_bn')) {
                $table->string('title_bn')->nullable()->after('title_en');
            }
        });

        DB::table('page_showcases')
            ->select('id', 'title', 'title_en')
            ->orderBy('id')
            ->get()
            ->each(function (object $showcase): void {
                $title = trim((string) ($showcase->title ?? ''));
                $titleEn = trim((string) ($showcase->title_en ?? ''));

                if ($title !== '' && $titleEn === '') {
                    DB::table('page_showcases')
                        ->where('id', $showcase->id)
                        ->update(['title_en' => $title]);
                }
            });
    }

    public function down(): void
    {
        if (! Schema::hasTable('page_showcases')) {
            return;
        }

        Schema::table('page_showcases', function (Blueprint $table) {
            if (Schema::hasColumn('page_showcases', 'title_bn')) {
                $table->dropColumn('title_bn');
            }

            if (Schema::hasColumn('page_showcases', 'title_en')) {
                $table->dropColumn('title_en');
            }
        });
    }
};
