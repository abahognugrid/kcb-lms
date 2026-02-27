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
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')
                ->constrained('partners');
            $table->foreignId('customer_id')
                ->nullable()
                ->constrained('customers');
            $table->string('accounting_type'); // debit or credit
            $table->foreignId('account_id')
                ->constrained('accounts');
            $table->string('account_name');
            $table->string('cash_type');
            $table->decimal('amount', 19, 2);
            $table->decimal('debit_amount', 19, 2)->nullable();
            $table->decimal('credit_amount', 19, 2)->nullable();
            $table->decimal('current_balance', 19, 2)->nullable();
            $table->decimal('previous_balance', 19, 2)->nullable();
            $table->string('txn_id')->nullable();
            $table->string("transactable")
                ->index()
                ->nullable();
            $table->foreignId("transactable_id")
                ->index()
                ->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
