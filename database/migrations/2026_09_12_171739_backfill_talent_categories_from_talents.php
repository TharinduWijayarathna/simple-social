<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('talents') || ! Schema::hasTable('talent_categories')) {
            return;
        }

        $now = now();
        $globalTalentCategories = DB::table('talents')
            ->whereNull('campus_id')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->pluck('category');

        foreach ($globalTalentCategories as $categoryName) {
            $categoryExists = DB::table('talent_categories')
                ->where('name', $categoryName)
                ->whereNull('campus_id')
                ->exists();

            if (! $categoryExists) {
                DB::table('talent_categories')->insert([
                    'name' => $categoryName,
                    'campus_id' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        $globalCategoryNames = DB::table('talent_categories')
            ->whereNull('campus_id')
            ->pluck('name');

        $campusTalentCategories = DB::table('talents')
            ->select(['category', 'campus_id'])
            ->whereNotNull('campus_id')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->get();

        foreach ($campusTalentCategories as $talentCategory) {
            if ($globalCategoryNames->contains($talentCategory->category)) {
                continue;
            }

            $categoryExists = DB::table('talent_categories')
                ->where('name', $talentCategory->category)
                ->where('campus_id', $talentCategory->campus_id)
                ->exists();

            if (! $categoryExists) {
                DB::table('talent_categories')->insert([
                    'name' => $talentCategory->category,
                    'campus_id' => $talentCategory->campus_id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This data backfill is intentionally irreversible because existing
        // categories cannot be distinguished safely from backfilled rows.
    }
};
