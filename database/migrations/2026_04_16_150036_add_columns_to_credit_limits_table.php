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
        Schema::table('credit_limits', function (Blueprint $table) {
            $table->decimal('previous_credit_limit')->nullable();
            $table->decimal('loan_days_late_multiplier')->nullable();
            $table->decimal('loan_repayment_multiplier')->nullable();
            $table->boolean('is_excluded')->default(false);
            $table->json('exclusions')->nullable();
            $table->json('data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credit_limits', function (Blueprint $table) {
            $table->dropColumn('previous_credit_limit');
            $table->dropColumn('loan_days_late_multiplier');
            $table->dropColumn('loan_repayment_multiplier');
            $table->dropColumn('is_excluded');
            $table->dropColumn('exclusions');
            $table->dropColumn('data');
        });
    }
};
