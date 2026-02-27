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
        Schema::table('partner_ovas', function (Blueprint $table) {
            $table->string('client_key', 500)->after('airtel_public_key')->nullable();
            $table->string('client_secret', 500)->after('airtel_public_key')->nullable();
            $table->string('pin')->after('airtel_public_key')->nullable();
            $table->renameColumn('airtel_app_name', 'app_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partner_ovas', function (Blueprint $table) {
            $table->dropColumn(['client_key', 'client_secret', 'pin']);
            $table->renameColumn('app_name', 'airtel_app_name');
        });
    }
};
