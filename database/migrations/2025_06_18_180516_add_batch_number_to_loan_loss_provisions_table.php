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
        Schema::table('loan_loss_provisions', function (Blueprint $table) {
            $table->integer('batch_number')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_loss_provisions', function (Blueprint $table) {
            $table->dropColumn('batch_number');
        });
    }
};
