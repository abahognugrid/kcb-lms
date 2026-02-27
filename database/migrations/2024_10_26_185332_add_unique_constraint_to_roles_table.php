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
        Schema::table('roles', function (Blueprint $table) {
            // Drop existing unique constraint on name and guard_name
            $table->dropUnique(['name', 'guard_name']);
            // Add unique constraint for partner_id, name, and guard_name
            $table->unique(['partner_id', 'name', 'guard_name'], 'roles_partner_name_guard_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropUnique('roles_partner_name_guard_unique');
            // Optionally, restore the original unique constraint
            $table->unique(['name', 'guard_name']);
        });
    }
};
