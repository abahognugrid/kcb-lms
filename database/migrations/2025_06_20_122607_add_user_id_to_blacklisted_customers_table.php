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
        Schema::table('blacklisted_customers', function (Blueprint $table) {
            $table->foreignId('blacklisted_by')->nullable()->constrained('users')->after('partner_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blacklisted_customers', function (Blueprint $table) {
            $table->dropForeign(['blacklisted_by']);
            $table->dropColumn('blacklisted_by');
        });
    }
};
