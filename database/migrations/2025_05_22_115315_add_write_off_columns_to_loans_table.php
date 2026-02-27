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
            $table->date('Written_Off_Date')->nullable()->after('Credit_Account_Closure_Reason');
            $table->string('Written_Off_Reason')->nullable()->after('Written_Off_Date');
            $table->unsignedBigInteger('Written_Off_Officer')->nullable()->after('Written_Off_Reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn(['Written_Off_Date', 'Written_Off_Reason', 'Written_Off_Officer']);
        });
    }
};
