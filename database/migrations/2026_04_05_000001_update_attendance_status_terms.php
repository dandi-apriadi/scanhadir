<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MySQL, we can use change() for enums or raw SQL
        // To be safe across drivers, we change it to string first or use raw MySQL
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('status')->default('Alpa')->change();
        });

        // Optional: Update existing data if any (though currently empty)
        DB::table('attendances')->where('status', 'present')->update(['status' => 'Hadir']);
        DB::table('attendances')->where('status', 'late')->update(['status' => 'Telat']);
        DB::table('attendances')->where('status', 'sick')->update(['status' => 'Sakit']);
        DB::table('attendances')->where('status', 'excused')->update(['status' => 'Izin']);
        DB::table('attendances')->where('status', 'absent')->update(['status' => 'Alpa']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->enum('status', ['present', 'late', 'sick', 'excused', 'absent'])->default('absent')->change();
        });
    }
};
