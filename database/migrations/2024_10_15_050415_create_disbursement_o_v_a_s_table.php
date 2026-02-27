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
        Schema::create('disbursement_o_v_a_s', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')
                ->constrained('partners', 'id');
            $table->string('name')->default('Bank Disbursement Escrow Account');
            $table->decimal('balance', 19, 4)->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disbursement_o_v_a_s');
    }
};
