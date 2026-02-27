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
        Schema::create('loan_loss_provisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('loan_product_id')->constrained()->cascadeOnDelete();
            $table->string('ageing_category');
            $table->decimal('provision_rate', 5, 2)->default(0);
            $table->unsignedBigInteger('provision_amount')->default(0);
            $table->timestamps();

            // Ensure a partner can't have duplicate ageing categories for the same loan product
            $table->unique(['partner_id', 'loan_product_id', 'ageing_category'], 'unique_partner_loan_product_ageing_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_loss_provisions');
    }
};
