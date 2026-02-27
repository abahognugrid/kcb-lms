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
        Schema::create('written_off_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId("Loan_ID")->constrained("loans");
            $table->foreignId("Customer_ID")->constrained("customers");
            $table->foreignId("partner_id")->constrained("partners");
            $table->decimal('Amount_Written_Off', 19, 4);
            $table->dateTime("Written_Off_Date");
            $table->smallInteger("Written_Off_By")->nullable();
            $table->boolean('Is_Recovered')->default(0);
            $table->decimal('Balance_After_Recovery', 19, 4)->nullable();
            $table->dateTime("Date_Recovered")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('written_off_loans');
    }
};
