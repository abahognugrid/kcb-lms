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
        Schema::table('loans', function (Blueprint $table) {
            $table->decimal('Credit_Limit')->nullable();
            $table->decimal('Days_Late_Multiplier')->nullable();
            $table->decimal('Repayment_Multiplier')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn('Credit_Limit');
            $table->dropColumn('Days_Late_Multiplier');
            $table->dropColumn('Repayment_Multiplier');
        });
    }
};
