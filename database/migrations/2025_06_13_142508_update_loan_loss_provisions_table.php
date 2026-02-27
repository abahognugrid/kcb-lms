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
            $table->unsignedTinyInteger('minimum_days')->change();
            $table->unsignedInteger('maximum_days')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_loss_provisions', function (Blueprint $table) {
            $table->tinyInteger('minimum_days')->change();
            $table->tinyInteger('maximum_days')->change();
        });
    }
};
