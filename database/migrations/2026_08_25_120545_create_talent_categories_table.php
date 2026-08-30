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
        Schema::create('talent_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('campus_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // Insert initial system categories if talents table exists
        if (Schema::hasTable('talents')) {
            $categories = DB::table('talents')
                ->select('category')
                ->distinct()
                ->pluck('category');

            foreach ($categories as $cat) {
                if ($cat) {
                    DB::table('talent_categories')->insertOrIgnore([
                        'name' => $cat,
                        'campus_id' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('talent_categories');
    }
};
