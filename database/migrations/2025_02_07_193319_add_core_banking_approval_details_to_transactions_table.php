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
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('Approval_Reference')->nullable();
            $table->dateTime('Approval_Date')->nullable();
            $table->string('Rejection_Reference')->nullable();
            $table->dateTime('Rejection_Date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['Approval_Reference', 'Approval_Date', 'Rejection_Reference', 'Rejection_Date']);
        });
    }
};
