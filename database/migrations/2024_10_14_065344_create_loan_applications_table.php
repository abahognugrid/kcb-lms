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
        Schema::create('loan_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId("partner_id")
                ->constrained("partners");
            $table->foreignId("Customer_ID")
                ->constrained("customers");
            $table->foreignId("Loan_Product_ID")
                ->constrained("loan_products");
            $table->string("Loan_Purpose");
            $table->string("Client_Number")->nullable();
            $table->string("Credit_Application_Reference")->nullable();
            $table->string("Applicant_Classification");
            $table->date("Credit_Application_Date");
            $table->decimal("Amount", 19, 2);
            $table->string("Currency")->default("UGX");
            $table->string("Credit_Account_or_Loan_Product_Type");
            $table->string("Credit_Application_Status");
            $table->string("Last_Status_Change_Date");
            $table->string("Credit_Application_Duration");
            $table->string("Rejection_Reason")->nullable();
            $table->string("Client_Consent_flag");
            $table->string("Group_Identification_Joint_Account_Identification")->nullable();
            $table->string("Credit_Amount_Approved");
            $table->string("Currency_Approved")->default("UGX");
            $table->string("PCI_Country_Code")->default("UG");
            $table->string("PCI_Flag_of_Ownership")->nullable();
            $table->string("PCI_Period_At_Address")->nullable();
            $table->string("Country")->nullable();
            $table->string("District")->nullable();
            $table->string("Subcounty")->nullable();
            $table->string("Parish")->nullable();
            $table->string("Village")->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_applications');
    }
};
