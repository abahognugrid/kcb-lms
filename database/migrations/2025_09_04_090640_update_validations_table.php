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
        Schema::table('validations', function (Blueprint $table) {
            $table->unsignedInteger('card_number')->nullable()->after('nin');
            $table->decimal('cost')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('validations', function (Blueprint $table) {
            $table->dropColumn(['card_number', 'cost']);
        });
    }
};
