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
        Schema::create('bulk_import_errors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bulk_import_job_id');
            $table->integer('row_number');
            $table->json('row_data');
            $table->string('error_type'); // 'validation', 'processing', 'system'
            $table->text('error_message');
            $table->json('error_details')->nullable();
            $table->timestamps();

            $table->foreign('bulk_import_job_id')->references('id')->on('bulk_import_jobs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulk_import_errors');
    }
};
