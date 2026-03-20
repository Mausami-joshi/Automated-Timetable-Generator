<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->string('level', 10)->default('UG')->after('name'); // UG / PG
            $table->unsignedTinyInteger('division_count')->default(1)->after('semester');
            $table->string('division_names')->nullable()->after('division_count'); // e.g. "A,B,C"
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['level', 'division_count', 'division_names']);
        });
    }
};

