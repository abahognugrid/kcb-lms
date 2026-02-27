<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('loan_repayments', function (Blueprint $table) {
            $table->id();
            $table->foreignId("Loan_ID")->constrained("loans");
            $table->foreignId("Customer_ID")->constrained("customers");
            $table->foreignId("partner_id")->constrained("partners");
            $table->dateTime("Transaction_Date");
            $table->decimal('amount', 19, 4);
            $table->string("Last_Payment_Date");
            $table->string("Last_Payment_Amount");
            $table->string("Current_Balance_Amount");
            $table->string("Current_Balance_Amount_UGX_Equivalent");
            $table->string("Current_Balance_Indicator")->nullable();
            $table->string("Credit_Account_Status")->nullable();
            $table->date("Last_Status_Change_Date")->nullable();
            $table->string("Credit_Account_Risk_Classification")->nullable();
            $table->date("Credit_Account_Arrears_Date")->nullable();
            $table->string("Number_of_Days_in_Arrears")->nullable();
            $table->string("Balance_Overdue")->nullable();
            $table->string("Risk_Classification_Criteria")->nullable();
            $table->string("Opening_Balance_Indicator")->nullable();
            $table->string("Annual_Interest_Rate_at_Reporting")->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_repayments');
    }
};
