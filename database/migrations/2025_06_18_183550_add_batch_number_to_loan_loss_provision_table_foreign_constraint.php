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
            $table->dropForeign(['partner_id']);
            $table->dropForeign('loan_loss_provisions_loan_product_id_foreign');

            $table->dropUnique('unique_partner_loan_product_ageing_category');
            $table->unique(['batch_number', 'ageing_category'], 'unique_batch_number_ageing_category');

            $table
                ->foreign('loan_product_id')->references('id')
                ->on('loan_products')->onDelete('cascade');
            $table
                ->foreign('partner_id')
                ->references('id')->on('partners')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_loss_provisions', function (Blueprint $table) {
            $table->dropUnique('unique_batch_number_ageing_category');
            $table->unique(['partner_id', 'loan_product_id', 'ageing_category'], 'unique_partner_loan_product_ageing_category');
        });
    }
};
