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
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->string('Approval_Reference')->nullable()->after('Currency_Approved');
            $table->dateTime('Approval_Date')->nullable()->after('Approval_Reference');
            $table->foreignId('Approved_By')->nullable()->constrained('users')->nullOnDelete()->after('Approval_Date');
            $table->string('Rejection_Reference')->nullable()->after('Rejection_Reason');
            $table->dateTime('Rejection_Date')->nullable()->after('Rejection_Reference');
            $table->foreignId('Rejected_By')->nullable()->constrained('users')->nullOnDelete()->after('Rejection_Date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->dropForeign('Approved_By');
            $table->dropForeign('Rejected_By');
            $table->dropColumn(['Approval_Reference', 'Approval_Date', 'Approved_By', 'Rejection_Reference', 'Rejection_Date', 'Rejected_By']);
        });
    }
};
