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
        Schema::table('loan_schedules', function (Blueprint $table) {
            // Make existing columns nullable
            $table->decimal('principal', 15, 2)->nullable()->change();
            $table->decimal('interest', 15, 2)->nullable()->change();
            $table->decimal('total_payment', 15, 2)->nullable()->change();
            $table->decimal('principal_remaining', 15, 2)->nullable()->change();
            $table->decimal('interest_remaining', 15, 2)->nullable()->change();
            $table->decimal('total_outstanding', 15, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_schedules', function (Blueprint $table) {
            // Revert columns back to non-nullable if needed
            $table->decimal('principal', 15, 2)->nullable(false)->change();
            $table->decimal('interest', 15, 2)->nullable(false)->change();
            $table->decimal('total_payment', 15, 2)->nullable(false)->change();
            $table->decimal('principal_remaining', 15, 2)->nullable(false)->change();
            $table->decimal('interest_remaining', 15, 2)->nullable(false)->change();
            $table->decimal('total_outstanding', 15, 2)->nullable(false)->change();
        });
    }
};
