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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId("partner_id")
                ->constrained("partners");
            $table->foreignId("Customer_ID")
                ->constrained("customers");
            $table->foreignId("Loan_Product_ID")
                ->constrained("loan_products");
            $table->foreignId("Loan_Application_ID")
                ->constrained("loan_applications");
            $table->foreignId("Loan_Term_ID")->index();
            $table->string("Credit_Application_Status")->default("Approved");
            $table->integer("Credit_Account_Status")->default(5);
            $table->date("Last_Status_Change_Date")->default(now());
            $table->string("Credit_Account_Reference");
            $table->date("Credit_Account_Date");
            $table->string("Credit_Amount");
            $table->string("Facility_Amount_Granted");
            $table->string("Credit_Amount_Drawdown");
            $table->string("Credit_Account_Type");
            $table->string("Currency")->default("UGX");
            $table->date("Maturity_Date");
            $table->string("Annual_Interest_Rate_at_Disbursement");
            $table->date("Date_of_First_Payment");
            $table->string("Credit_Amortization_Type")->default(3);
            $table->string("Credit_Payment_Frequency");
            $table->string("Number_of_Payments");
            $table->string("Instalment_Amount")->nullable();
            $table->date("Credit_Account_Closure_Date")->nullable();
            $table->string("Credit_Account_Closure_Reason")->nullable();
            $table->string("Specific_Provision_Amount")->nullable();
            $table->string("Client_Consent_Flag")->default("Yes");
            $table->string("Client_Advice_Notice_Flag")->default("Yes");
            $table->string("Term");
            $table->float('Interest_Rate');
            $table->string('Interest_Calculation_Method');
            $table->string("Type_of_Interest");
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
