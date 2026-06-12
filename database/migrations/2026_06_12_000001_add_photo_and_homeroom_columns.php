<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'photo_path')) {
                $table->string('photo_path')->nullable()->after('role');
            }
        });

        Schema::table('classes', function (Blueprint $table) {
            if (! Schema::hasColumn('classes', 'homeroom_teacher_id')) {
                $table->foreignId('homeroom_teacher_id')
                    ->nullable()
                    ->after('major')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            if (Schema::hasColumn('classes', 'homeroom_teacher_id')) {
                $table->dropConstrainedForeignId('homeroom_teacher_id');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'photo_path')) {
                $table->dropColumn('photo_path');
            }
        });
    }
};
