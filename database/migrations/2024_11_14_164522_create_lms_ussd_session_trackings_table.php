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
        Schema::create('lms_ussd_session_trackings', function (Blueprint $table) {
            $table->id();
            $table->string('requestId');
            $table->string('Customer_Phone_Number');
            $table->integer('Loan_Application_ID')->nullable();
            $table->string("Loan_Producd_Code")->nullable();
            $table->string("Loan_Producd_Term_Code")->nullable();
            $table->string("Credit_Payment_Frequency")->nullable();
            $table->string("Number_of_Payments")->nullable();
            $table->string("Date_of_First_Payment")->nullable();
            $table->string("Maturity_Date")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lms_ussd_session_trackings');
    }
};
