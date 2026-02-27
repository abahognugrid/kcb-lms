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
        Schema::create('loan_product_terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')
                ->constrained('partners');
            $table->foreignId('Loan_Product_ID')
                ->constrained('loan_products');
            $table->float('Interest_Rate');
            $table->string('Interest_Calculation_Method');
            $table->string('Code');
            $table->integer('Value');
            $table->json('Repayment_Cycles');
            $table->boolean('Has_Advance_Payment')->nullable()->default(false);
            $table->string('Advance_Calculation_Method')->nullable();
            $table->integer('Advance_Value')->nullable();
            $table->boolean('Extend_Loan_After_Maturity')->nullable()->default(false);
            $table->string('Interest_Type_After_Maturity')->nullable();
            $table->float('Interest_Value_After_Maturity')->nullable();
            $table->string('Interest_After_Maturity_Calculation_Method')->nullable();
            $table->string('Recurring_Period_After_Maturity_Type')->nullable();
            $table->integer('Recurring_Period_After_Maturity_Value')->nullable();
            $table->boolean('Include_Fees_After_Maturity')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_product_terms');
    }
};
