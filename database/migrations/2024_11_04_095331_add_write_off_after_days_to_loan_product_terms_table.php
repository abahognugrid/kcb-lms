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
        Schema::table('loan_product_terms', function (Blueprint $table) {
            $table->integer('Write_Off_After_Days')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_product_terms', function (Blueprint $table) {
            $table->dropColumn('Write_Off_After_Days');
        });
    }
};
