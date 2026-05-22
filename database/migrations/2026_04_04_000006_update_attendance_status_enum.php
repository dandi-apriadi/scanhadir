<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, migrate existing data from English to Indonesian
        DB::table('attendances')->where('status', 'present')->update(['status' => 'Hadir']);
        DB::table('attendances')->where('status', 'late')->update(['status' => 'Telat']);
        DB::table('attendances')->where('status', 'sick')->update(['status' => 'Sakit']);
        DB::table('attendances')->where('status', 'excused')->update(['status' => 'Izin']);
        DB::table('attendances')->where('status', 'absent')->update(['status' => 'Alpa']);

        // Then update the enum column definition
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('status', 20)->default('Alpa')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert data back to English
        DB::table('attendances')->where('status', 'Hadir')->update(['status' => 'present']);
        DB::table('attendances')->where('status', 'Telat')->update(['status' => 'late']);
        DB::table('attendances')->where('status', 'Sakit')->update(['status' => 'sick']);
        DB::table('attendances')->where('status', 'Izin')->update(['status' => 'excused']);
        DB::table('attendances')->where('status', 'Alpa')->update(['status' => 'absent']);

        Schema::table('attendances', function (Blueprint $table) {
            $table->string('status', 20)->default('absent')->change();
        });
    }
};