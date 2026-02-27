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
            $table->foreignId('Loss_Provision_Account_ID')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('Written_Off_Expense_Account_ID')->nullable()->constrained('accounts')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_products', function (Blueprint $table) {
            $table->dropForeign('Loss_Provision_Account_ID');
            $table->dropForeign('Written_Off_Expense_Account_ID');
            $table->dropColumn(['Loss_Provision_Account_ID', 'Written_Off_Expense_Account_ID']);
        });
    }
};
