    <?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('admin_sms_credentials')) {
            Schema::create('admin_sms_credentials', function (Blueprint $table) {
                $table->id();
                $table->string('provider_name')->default('Greenweb');
                $table->string('api_key')->nullable();
                $table->string('sender_id')->nullable();
                $table->string('api_url')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_sms_credentials');
    }
};
