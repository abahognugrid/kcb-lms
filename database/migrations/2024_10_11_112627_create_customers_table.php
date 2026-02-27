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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('First_Name');
            $table->string('Last_Name');
            $table->string('Other_Name')->nullable();
            $table->string('Gender');
            $table->string('Marital_Status')->nullable();
            $table->string('Date_of_Birth');
            $table->string('ID_Type');
            $table->string('ID_Number');
            $table->string('Email_Address')->unique()->nullable();
            $table->string('Classification')->default('Individual');
            $table->string('Telephone_Number')->unique();
            $table->boolean("IS_Barned")->default(false);
            $table->string("Barning_Reason")->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
