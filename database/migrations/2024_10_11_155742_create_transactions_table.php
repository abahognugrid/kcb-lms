<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')
                ->constrained("partners");
            $table->string("Type")
                ->index();
            $table->string("Status")->index();
            $table->decimal("Amount", 19, 2);
            $table->string("Telephone_Number")->index();
            $table->string("TXN_ID")
                ->index();
            $table->string("Provider_TXN_ID")
                ->index()
                ->nullable();
            $table->string("Narration")
                ->index()
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
