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
        Schema::table('loan_loss_provisions', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->after('provision_amount')->nullable();
            $table->dateTime('approved_at')->after('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->after('approved_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_loss_provisions', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'approved_at', 'approved_by']);
        });
    }
};
