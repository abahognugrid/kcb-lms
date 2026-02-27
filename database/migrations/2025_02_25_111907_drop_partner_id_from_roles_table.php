<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('roles', function (Blueprint $table) {
            // Step 1: Drop the foreign key constraint
            $table->dropForeign('roles_partner_id_foreign');

            // Step 2: Drop the unique index
            $table->dropUnique('roles_partner_name_guard_unique');

            // Step 3: Drop the partner_id column
            $table->dropColumn('partner_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
            // Step 1: Add the partner_id column back
            $table->unsignedBigInteger('partner_id')->nullable()->after('id');

            // Step 2: Recreate the unique index
            $table->unique(['partner_id', 'name', 'guard_name'], 'roles_partner_name_guard_unique');

            // Step 3: Recreate the foreign key constraint
            $table->foreign('partner_id')
                ->references('id')
                ->on('partners')
                ->onDelete('set null');
        });
    }
};
