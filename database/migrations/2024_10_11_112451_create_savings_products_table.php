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
        Schema::create('savings_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId("partner_id")
                ->constrained("partners")
                ->index();
            $table->string("name");
            $table->text("description");
            $table->text("code");
            $table->integer("interest_rate")->default(0);
            $table->string("interest_payment_frequency")->default('Monthly');
            $table->string("interest_payment_computation_on")->default('Closing Balance'); // Closing Balance, Opening Balance
            $table->decimal("opening_balance", 19, 2)->default(0);
            $table->decimal("minimum_balance", 19, 2)->default(0);
            $table->decimal("current_balance", 19, 2)->default(0);
            $table->decimal("previous_balance", 19, 2)->default(0);
            $table->boolean("is_active")->default(true);
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
        Schema::dropIfExists('savings_products');
    }
};
