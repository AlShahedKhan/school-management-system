<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->string('name');
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable()->unique();
            $table->string('ip_address');
            $table->integer('port')->default(4370);
            $table->string('communication_type')->default('LAN');
            $table->string('protocol')->nullable();
            $table->string('time_zone')->nullable();
            $table->unsignedInteger('sync_interval')->nullable()->comment('In minutes');
            $table->unsignedInteger('heartbeat_time')->nullable()->comment('In seconds');
            $table->unsignedInteger('connection_timeout')->nullable()->comment('In seconds');
            $table->string('firmware_version')->nullable();
            $table->string('device_password')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
