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
        Schema::create('partner_api_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained('partners');
            $table->text('api_key');
            $table->dateTime('expires_at')->nullable(); // Set to null means it doesn't expire
            $table->dateTime('last_used_at')->nullable(); // Set to null means it doesn't expire
            $table->json('api_scopes')->nullable();
            $table->boolean('has_been_used')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_api_settings');
    }
};
