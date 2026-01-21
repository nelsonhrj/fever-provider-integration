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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('provider_id')->unique();
            $table->string('title');
            $table->string('sell_mode');
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->json('zones')->nullable();
            $table->json('raw_data')->nullable();
            $table->timestamp('last_seen_at')->useCurrent();
            $table->timestamps();

            $table->index(['starts_at', 'ends_at']);
            $table->index('sell_mode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
