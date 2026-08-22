<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // University ID for students (their student card number)
            $table->string('university_id')->nullable()->after('email');

            // Links a student to a campus admin (null for campus admins / super admin)
            $table->foreignId('campus_id')
                ->nullable()
                ->after('university_id')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['campus_id']);
            $table->dropColumn(['university_id', 'campus_id']);
        });
    }
};
