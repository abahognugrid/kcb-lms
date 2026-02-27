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
        Schema::table('loan_product_penalties', function (Blueprint $table) {
            $table->integer('Penalty_Starts_After_Days')->nullable()->after('Description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_product_penalties', function (Blueprint $table) {
            $table->dropColumn('Penalty_Starts_After_Days');
        });
    }
};
