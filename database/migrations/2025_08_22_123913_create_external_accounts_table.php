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
        Schema::create('external_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('partner_id');
            $table->decimal('disbursement_account', 15, 2)->default(0);
            $table->decimal('collection_account', 15, 2)->default(0);
            $table->string('service_provider', 50);
            $table->timestamps();

            $table->foreign('partner_id')->references('id')->on('partners')->onDelete('cascade');
            $table->index(['partner_id', 'service_provider', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_accounts');
    }
};
