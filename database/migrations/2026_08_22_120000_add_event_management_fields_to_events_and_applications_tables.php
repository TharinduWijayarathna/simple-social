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
        Schema::table('events', function (Blueprint $table) {
            $table->string('event_type')->nullable()->after('talent_id');
            $table->text('requirements')->nullable()->after('description');
            $table->string('contact_email')->nullable()->after('location');
            $table->string('contact_phone')->nullable()->after('contact_email');
            $table->text('contact_instructions')->nullable()->after('contact_phone');
            $table->string('cover_image')->nullable()->after('contact_instructions');
        });

        Schema::table('event_applications', function (Blueprint $table) {
            $table->foreignId('talent_id')->nullable()->after('user_id')->constrained('talents')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'event_type',
                'requirements',
                'contact_email',
                'contact_phone',
                'contact_instructions',
                'cover_image',
            ]);
        });

        Schema::table('event_applications', function (Blueprint $table) {
            $table->dropForeign(['talent_id']);
            $table->dropColumn('talent_id');
        });
    }
};
