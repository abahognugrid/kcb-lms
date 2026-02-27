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
        Schema::table('loan_products', function (Blueprint $table) {
            $table->after('arrears_auto_write_off_days', function ($table) {
                $table->boolean('can_write_off_interest')->default(false);
                $table->boolean('can_write_off_penalties')->default(false);
                $table->boolean('can_write_off_fees')->default(false);
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_products', function (Blueprint $table) {
            $table->dropColumn([
                'can_write_off_interest',
                'can_write_off_penalties',
                'can_write_off_fees'
            ]);
        });
    }
};
