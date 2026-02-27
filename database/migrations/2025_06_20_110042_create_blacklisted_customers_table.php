<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('blacklisted_customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('partner_id')->constrained('partners')->onDelete('cascade');
            $table->text('reason');
            $table->timestamps();

            // Ensure a customer can't be blacklisted by the same partner twice
            $table->unique(['customer_id', 'partner_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('blacklisted_customers');
    }
};
