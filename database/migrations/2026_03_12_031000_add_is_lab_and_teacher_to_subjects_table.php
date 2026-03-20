<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('subjects', 'is_lab')) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->boolean('is_lab')->default(false)->after('term');
            });
        }

        if (!Schema::hasColumn('subjects', 'teacher_id')) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->foreignId('teacher_id')->nullable()->after('course_id')->constrained('teachers')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('subjects', 'teacher_id')) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->dropConstrainedForeignId('teacher_id');
            });
        }

        if (Schema::hasColumn('subjects', 'is_lab')) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->dropColumn('is_lab');
            });
        }
    }
};

