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
            $table->string('Payable_To')->nullable()->after('Applicable_On');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_product_fees', function (Blueprint $table) {
            $table->dropColumn('Payable_To');
        });
    }
};
