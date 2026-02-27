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
        Schema::create('black_listed_clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId("Customer_ID")->constrained("customers");
            $table->foreignId("partner_id")->constrained("partners");
            $table->dateTime("Date_Blacklisted");
            $table->decimal('Amount_Disbursed', 19, 4)->nullable();
            $table->decimal('Outstanding_Balance', 19, 4)->nullable();
            $table->string('Reason_For_Blacklisting');
            $table->smallInteger("User_ID");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('black_listed_clients');
    }
};
