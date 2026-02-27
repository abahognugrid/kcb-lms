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
            $table->boolean('is_part_of_interest')->default(false)->after('charge_interest');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_product_fees', function (Blueprint $table) {
            $table->dropColumn('is_part_of_interest');
        });
    }
};
