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
        Schema::table('loan_loss_provisions', function (Blueprint $table) {
            $table->string('ageing_category_slug')->after('ageing_category');
            $table->tinyInteger('minimum_days')->after('ageing_category_slug');
            $table->tinyInteger('maximum_days')->after('minimum_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_loss_provisions', function (Blueprint $table) {
            $table->dropColumn(['ageing_category_slug', 'minimum_days', 'maximum_days']);
        });
    }
};
