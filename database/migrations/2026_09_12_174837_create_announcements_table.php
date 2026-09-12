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
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campus_id')->constrained('users')->cascadeOnDelete();
            $table->string('title', 120);
            $table->text('body');
            $table->string('priority', 24)->default('standard');
            $table->string('audience', 24)->default('everyone');
            $table->string('audience_value')->nullable();
            $table->timestamp('starts_at')->nullable()->index();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->boolean('is_pinned')->default(false);
            $table->string('link_url', 2048)->nullable();
            $table->string('link_label', 60)->nullable();
            $table->timestamps();

            $table->index(['campus_id', 'published_at']);
            $table->index(['campus_id', 'audience', 'audience_value']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
