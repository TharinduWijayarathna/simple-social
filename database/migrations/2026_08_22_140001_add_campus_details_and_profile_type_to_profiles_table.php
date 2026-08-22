<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('batch')->nullable()->after('department');
            $table->string('program')->nullable()->after('batch');
            $table->string('profile_type')->default('General Student Account')->after('program')->index();
            $table->foreignId('primary_talent_id')->nullable()->after('profile_type')->constrained('talents')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropForeign(['primary_talent_id']);
            $table->dropColumn(['batch', 'program', 'profile_type', 'primary_talent_id']);
        });
    }
};
