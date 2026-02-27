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
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('Identification_Code')->unique();
            $table->string('Institution_Type');
            $table->string('Institution_Name')->unique();
            $table->string('License_Issuing_Date');
            $table->string('License_Number')->unique();
            $table->string('Telephone_Number')->unique();
            $table->string('Email_Address')->unique();
            $table->string('Country_Code')->default('UG');
            $table->integer('Minimum_Sms_Balance')->default(0);
            $table->float('Minimum_Float_Percentage')->default(10);
            $table->text('Sms_Reminder_Recipients')->nullable();
            $table->text('Email_Reminder_Recipients')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};
