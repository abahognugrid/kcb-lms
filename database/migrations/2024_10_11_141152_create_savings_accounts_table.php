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
        Schema::create('savings_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId("partner_id")
                ->constrained("partners");
            $table->foreignId("customer_id")
                ->constrained("customers");
            $table->foreignId("savings_product_id")
                ->constrained("savings_products");
            $table->decimal("current_balance", 19, 2)->default(0);
            $table->decimal("previous_balance", 19, 2)->default(0);
            $table->decimal("opening_balance", 19, 2)->default(0);
            $table->decimal("minimum_balance", 19, 2)->default(0);
            $table->boolean("is_active")->default(true);
            $table->dateTime("last_deposit_date")->nullable();
            $table->dateTime("last_withdraw_date")->nullable();
            $table->dateTime("active_status_changed_date")->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('savings_accounts');
    }
};
