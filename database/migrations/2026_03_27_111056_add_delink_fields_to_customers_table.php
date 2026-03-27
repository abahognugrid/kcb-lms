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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('Telephone_Number')->nullable()->change();
            $table->boolean('Is_Delinked')->default(false)->after('Telephone_Number');
            $table->timestamp('Delinked_At')->nullable()->after('Is_Delinked');
            $table->string('Delinked_Phone_Number')->nullable()->after('Delinked_At');

            $table->index(['Is_Delinked']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['Is_Delinked']);
            $table->dropColumn([
                'Is_Delinked',
                'Delinked_At',
                'Delinked_Phone_Number'
            ]);
            $table->string('Telephone_Number')->nullable(false)->change();
        });
    }
};
