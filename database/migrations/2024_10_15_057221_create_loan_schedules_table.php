<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id');
            $table->integer('installment_number');
            $table->decimal('principal', 19, 4);
            $table->decimal('interest', 19, 4);
            $table->decimal('total_payment', 19, 4);
            $table->decimal('principal_remaining', 19, 4)->default(0);  // Track remaining principal
            $table->decimal('interest_remaining', 19, 4)->default(0);
            $table->decimal('total_outstanding', 19, 4);
            $table->date('payment_due_date');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loan_schedules');
    }
}
