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
        Schema::create('float_top_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')
                ->constrained('partners');
            $table->decimal("Amount", 19, 4);
            $table->string("Proof_Of_Payment");
            $table->string("Status")
                ->default("Pending");
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('float_top_ups');
    }
};
