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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('school_name', 255)->default('SMK Negeri 1 Bandung');
            $table->string('npsn', 32)->nullable();
            $table->text('school_address')->nullable();
            $table->time('attendance_start_time')->default('07:00:00');
            $table->unsignedSmallInteger('late_tolerance_minutes')->default(15);
            $table->json('active_days')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
