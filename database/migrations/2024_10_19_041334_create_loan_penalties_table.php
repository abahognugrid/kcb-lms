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
        Schema::create('loan_penalties', function (Blueprint $table) {
            $table->id();
            $table->foreignId("partner_id")->constrained("partners");
            $table->foreignId("Customer_ID")->constrained("customers");
            $table->foreignId("Loan_ID")->constrained("loans");
            $table->foreignId("Penalty_ID")->constrained("loan_schedules");
            $table->foreignId("Loan_Penalty_ID")->constrained("loan_product_penalties");
            $table->decimal("Amount_To_Pay", 19, 2);
            $table->decimal("Amount", 19, 2);
            $table->string("Status")->default(value: "Not Paid"); // Patially Paid, Fully Paid
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_penalties');
    }
};
