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
        Schema::table('loan_product_fees', function (Blueprint $table) {
            // change value to default to 0
            $table->float('Value')->nullable()->change();
            $table->json('Tiers')->nullable()->after('Value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_product_fees', function (Blueprint $table) {
            // revert value to not have a default
            $table->float('Value')->nullable(false)->change();
            $table->dropColumn('Tiers');
        });
    }
};
