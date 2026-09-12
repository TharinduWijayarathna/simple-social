<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('settings')
            ->where('key', 'like', 'campus_announcement_message_%')
            ->whereNotNull('value')
            ->where('value', '!=', '')
            ->orderBy('id')
            ->each(function (object $setting): void {
                $campusId = (int) ((string) str($setting->key)->afterLast('_'));

                if ($campusId < 1 || ! DB::table('users')->where('id', $campusId)->exists()) {
                    return;
                }

                $enabled = DB::table('settings')
                    ->where('key', "campus_announcement_enabled_{$campusId}")
                    ->value('value') === '1';

                DB::table('announcements')->insert([
                    'campus_id' => $campusId,
                    'title' => 'Campus update',
                    'body' => $setting->value,
                    'priority' => 'standard',
                    'audience' => 'everyone',
                    'published_at' => $enabled ? now() : null,
                    'created_at' => $setting->created_at ?? now(),
                    'updated_at' => now(),
                ]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
