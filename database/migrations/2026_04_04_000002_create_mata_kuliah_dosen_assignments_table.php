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
        Schema::create('mata_kuliah_dosen_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->unique()->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // Backfill assignments for subjects that currently have exactly one distinct lecturer.
        $singleLecturerSubjects = DB::table('schedules')
            ->select('subject_id', DB::raw('MIN(teacher_id) as user_id'))
            ->groupBy('subject_id')
            ->havingRaw('COUNT(DISTINCT teacher_id) = 1')
            ->get();

        foreach ($singleLecturerSubjects as $row) {
            DB::table('mata_kuliah_dosen_assignments')->insert([
                'subject_id' => (int) $row->subject_id,
                'user_id' => (int) $row->user_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mata_kuliah_dosen_assignments');
    }
};