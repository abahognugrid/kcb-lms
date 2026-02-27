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
        Schema::create('sms_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('Loan_Product_ID'); // Ensure this is unsignedBigInteger
            $table->unsignedBigInteger('partner_id');
            $table->integer('Day');
            $table->string('Template');
            $table->timestamps();

            // Foreign keys
            $table->foreign('Loan_Product_ID')->references('id')->on('loan_products')->onDelete('cascade');
            $table->foreign('partner_id')->references('id')->on('partners')->onDelete('cascade');

            $table->unique(['Loan_Product_ID', 'Day']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_templates');
    }
};
