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
        Schema::create('bulk_sms_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id');
            $table->foreignId('campaign_id')->nullable();
            $table->text('telephone_numbers');
            $table->unsignedInteger('bulk_count');
            $table->decimal('bulk_cost', 12)->default(0.00);
            $table->string('reference_id')->nullable();
            $table->string('status')->nullable();
            $table->string('status_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulk_sms_logs');
    }
};
