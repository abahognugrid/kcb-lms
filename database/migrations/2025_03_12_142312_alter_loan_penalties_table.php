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
        Schema::table('loan_penalties', function (Blueprint $table) {
            $table->renameColumn('Loan_Penalty_ID', 'Product_Penalty_ID');
            $table->dropForeign(['Penalty_ID']);
            $table->dropColumn('Penalty_ID');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_penalties', function (Blueprint $table) {
            $table->foreignId('Penalty_ID')->constrained('loan_schedules');
            $table->renameColumn('Product_Penalty_ID', 'Loan_Penalty_ID');
        });
    }
};
