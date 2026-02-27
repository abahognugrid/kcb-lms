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
            $table->decimal('Written_Off_Amount', 12)->nullable()->after('Credit_Account_Closure_Reason');
            $table->dateTime('Last_Recovered_At')->nullable()->after('Written_Off_Amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn(['Written_Off_Amount', 'Last_Recovered_At']);
        });
    }
};
