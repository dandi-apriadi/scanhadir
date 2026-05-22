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
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreignId('schedule_id')
                ->nullable()
                ->constrained('schedules')
                ->cascadeOnDelete();
            $table->enum('metode_absensi', ['QR Code', 'RFID', 'Fingerprint', 'Face Recognition', 'Barcode'])
                ->default('QR Code')
                ->after('date');
            $table->unique(['student_id', 'schedule_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropUnique(['student_id', 'schedule_id', 'date']);
            $table->dropForeign(['schedule_id']);
            $table->dropColumn(['schedule_id', 'metode_absensi']);
        });
    }
};