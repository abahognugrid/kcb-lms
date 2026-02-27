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
        Schema::create('loan_products', function (Blueprint $table) {
            $table->id();
            $table->string('Name');
            $table->integer('partner_id');
            $table->integer('Loan_Product_Type_ID');
            // $table->string('Applicable_On');
            $table->float('Minimum_Principal_Amount');
            $table->float('Default_Principal_Amount');
            $table->float('Maximum_Principal_Amount');
            $table->string('Decimal_Place');
            $table->boolean('Round_UP_or_Off_all_Interest');
            $table->json('Repayment_Order');
            $table->json('Whitelisted_Customers')->nullable();
            $table->string('Code');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_products');
    }
};
