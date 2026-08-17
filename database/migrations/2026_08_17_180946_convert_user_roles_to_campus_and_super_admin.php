<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->where('role', 'organizer')->update(['role' => 'campus_admin']);
        DB::table('users')->where('role', 'admin')->update(['role' => 'super_admin']);
    }

    public function down(): void
    {
        DB::table('users')->where('role', 'campus_admin')->update(['role' => 'organizer']);
        DB::table('users')->where('role', 'super_admin')->update(['role' => 'admin']);
    }
};
