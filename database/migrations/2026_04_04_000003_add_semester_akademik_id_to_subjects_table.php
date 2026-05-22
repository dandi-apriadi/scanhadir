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
        Schema::table('subjects', function (Blueprint $table) {
            $table->foreignId('semester_akademik_id')
                ->nullable()
                ->constrained('semester_akademik')
                ->cascadeOnDelete();
            $table->integer('sks')->default(3);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropForeign(['semester_akademik_id']);
            $table->dropColumn(['semester_akademik_id', 'sks']);
        });
    }
};