<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('credit_scores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customerId')->nullable();

            // Customer Information (common fields)
            $table->string('mnoName')->nullable();
            $table->string('mnoGender')->nullable();
            $table->date('mnoDateOfBirth')->nullable();
            $table->string('crbName')->nullable();
            $table->string('crbGender')->nullable();
            $table->date('crbDateOfBirth')->nullable();
            $table->string('altName')->nullable();
            $table->string('altGender')->nullable();
            $table->date('altDateOfBirth')->nullable();

            // MNO Data
            $table->json('mnoPhoneNumbers')->nullable();
            $table->string('mnoPeriod')->nullable();
            $table->string('mnoScore')->nullable();
            $table->string('mnoBand')->nullable();
            $table->string('mnoRating')->nullable();
            $table->string('mnoProbabilityOfDefaultPercent')->nullable();
            $table->string('mnoLikelihoodToDefault')->nullable();
            $table->string('mnoPreviousScore')->nullable();
            $table->string('mnoMonthsActive')->nullable();
            $table->string('mnoAccounts12Months')->nullable();
            $table->string('mnoTotalLoanAmount12Months')->nullable();
            $table->string('mnoClosedAccounts12Months')->nullable();
            $table->string('mnoTotalPayment12Months')->nullable();
            $table->string('mnoTotalTurnoverCount6Months')->nullable();
            $table->string('mnoMonthlyTurnoverAmount6Months')->nullable();
            $table->string('mnoTotalSpendCount6Months')->nullable();
            $table->string('mnoMonthlySpendAmount6Months')->nullable();
            $table->string('mnoTotalTurnoverCount3Months')->nullable();
            $table->string('mnoMonthlyTurnoverAmount3Months')->nullable();
            $table->string('mnoTotalSpendCount3Months')->nullable();
            $table->string('mnoMonthlySpendAmount3Months')->nullable();

            // CRB Data
            $table->string('crbStatus')->nullable();
            $table->string('crbDisputed')->nullable();
            $table->string('crbPeriod')->nullable();
            $table->string('crbScore')->nullable();
            $table->string('crbBand')->nullable();
            $table->string('crbRating')->nullable();
            $table->string('crbProbabilityOfDefaultPercent')->nullable();
            $table->string('crbLikelihoodToDefault')->nullable();
            $table->string('crbTotalAccounts')->nullable();
            $table->string('crbTotalAccounts12Months')->nullable();
            $table->string('crbOpenAccounts')->nullable();
            $table->string('crbOpenAccounts12Months')->nullable();
            $table->string('crbClosedAccounts')->nullable();
            $table->string('crbClosedAccounts12Months')->nullable();
            $table->string('crbAdverseAccounts')->nullable();
            $table->string('crbAdverseAccounts12Months')->nullable();
            $table->string('crbWorstActiveDaysInArrears')->nullable();
            $table->string('crbWorstDaysInArrears')->nullable();
            $table->string('crbWorstDaysInArrears12Months')->nullable();

            // Alternative Data
            $table->string('altStatus')->nullable();
            $table->string('altDisputed')->nullable();
            $table->string('altPeriod')->nullable();
            $table->string('altScore')->nullable();
            $table->string('altBand')->nullable();
            $table->string('altRating')->nullable();
            $table->string('altProbabilityOfDefaultPercent')->nullable();
            $table->string('altLikelihoodToDefault')->nullable();
            $table->string('altTotalAccounts')->nullable();
            $table->string('altTotalAccounts12Months')->nullable();
            $table->string('altOpenAccounts')->nullable();
            $table->string('altOpenAccounts12Months')->nullable();
            $table->string('altClosedAccounts')->nullable();
            $table->string('altClosedAccounts12Months')->nullable();
            $table->string('altAdverseAccounts')->nullable();
            $table->string('altAdverseAccounts12Months')->nullable();
            $table->string('altWorstActiveDaysInArrears')->nullable();
            $table->string('altWorstDaysInArrears')->nullable();
            $table->string('altWorstDaysInArrears12Months')->nullable();
            $table->timestamps();

            // Index for customerId
            $table->index('customerId');
        });
    }

    public function down()
    {
        Schema::dropIfExists('credit_scores');
    }
};
