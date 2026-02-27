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
        Schema::table('loan_products', function (Blueprint $table) {
            $table->boolean('Allows_Multiple_Loans')->default(false);
            $table->boolean('Allows_Users_With_Loans_From_Other_Partners')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('loan_products', function (Blueprint $table) {
            $table->dropColumn('Allows_Multiple_Loans');
            $table->dropColumn('Allows_Users_With_Loans_From_Other_Partners');
        });
    }
};
