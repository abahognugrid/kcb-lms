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
        Schema::table('written_off_loans', function (Blueprint $table) {
            $table->after('amount_written_off', function ($table) {
                $table->decimal('interest', 15, 2)->nullable();
                $table->decimal('penalties', 15, 2)->nullable();
                $table->decimal('fees', 15, 2)->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('written_off_loans', function (Blueprint $table) {
            $table->dropColumn([
                'interest',
                'penalties',
                'fees'
            ]);
        });
    }
};
