<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations
     */
    public function up(): void
    {
        Schema::create('school_memberships', function (Blueprint $table) {

            $table->id();

            // Multi-tenant key
            $table->unsignedBigInteger('school_id');

            // Member information
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('mobile_number');
            $table->string('income_source')->nullable();

            // Donation / Membership amount
            $table->decimal('amount', 10, 2)->default(0);

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */
            $table->index('school_id');
            $table->index('mobile_number');

            /*
            |--------------------------------------------------------------------------
            | Foreign Key
            |--------------------------------------------------------------------------
            */
            $table->foreign('school_id')
                ->references('id')
                ->on('schools')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations
     */
    public function down(): void
    {
        Schema::dropIfExists('school_memberships');
    }
};
