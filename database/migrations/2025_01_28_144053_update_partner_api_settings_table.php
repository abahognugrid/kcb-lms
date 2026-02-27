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
        Schema::table('partner_api_settings', function (Blueprint $table) {
            $table->text('refresh_token')->after('api_key')->nullable();
            $table->string('api_name')->after('api_key')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partner_api_settings', function (Blueprint $table) {
            $table->dropColumn(['refresh_token', 'api_name']);
        });
    }
};
