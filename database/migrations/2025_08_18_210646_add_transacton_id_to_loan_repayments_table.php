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
        Schema::table('loan_repayments', function (Blueprint $table) {
            $table->foreignId('Transaction_ID')->nullable()->after('partner_id');
            $table->index('Transaction_ID');

            $table->decimal('Principal', 19)->nullable()->after('amount');
            $table->decimal('Interest', 19)->nullable()->after('Principal');
            $table->decimal('Fee', 19)->nullable()->after('Interest');
            $table->decimal('Penalty', 19)->nullable()->after('Fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_repayments', function (Blueprint $table) {
            $table->dropIndex(['Transaction_ID']);
            $table->dropColumn(['Transaction_ID', 'Principal', 'Interest', 'Fee', 'Penalty']);
        });
    }
};
