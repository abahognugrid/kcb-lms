<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans_import', function (Blueprint $table) {
            $table->id();

            $table->string('telephone_number')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('other_name')->nullable();
            $table->string('gender')->nullable();

            $table->string('date_of_birth')->nullable();

            $table->string('id_type')->nullable();
            $table->string('id_number')->nullable();

            $table->string('loan_application_date')->nullable();
            $table->string('maturity_date')->nullable();
            $table->string('loan_amount')->nullable();
            $table->string('amount_paid')->nullable();
            $table->string('loan_penalty')->nullable();
            $table->string('outstanding_amount')->nullable();
            $table->string('loan_status')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans_import');
    }
};
