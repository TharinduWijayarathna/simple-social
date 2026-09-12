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
        Schema::table('users', function (Blueprint $table) {
            $table->string('campus_phone', 30)->nullable()->after('campus_name');
            $table->string('campus_address', 500)->nullable()->after('campus_phone');
            $table->string('campus_website')->nullable()->after('campus_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['campus_phone', 'campus_address', 'campus_website']);
        });
    }
};
