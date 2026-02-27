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
        Schema::create('loan_product_penalties', function (Blueprint $table) {
            $table->id();
            $table->string('Name');
            $table->integer('partner_id');
            $table->string('Calculation_Method');
            $table->float('Value');
            $table->string('Applicable_On');
            $table->integer('Loan_Product_ID');
            $table->text('Description');
            $table->boolean('Has_Recurring_Penalty')->nullable()->default(false);
            $table->float('Recurring_Penalty_Interest_Value')->nullable();
            $table->string('Recurring_Penalty_Interest_Period_Type')->nullable();
            $table->integer('Recurring_Penalty_Interest_Period_Value')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_product_penalties');
    }
};
