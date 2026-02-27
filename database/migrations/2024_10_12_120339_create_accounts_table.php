<?php

use App\Models\Accounts\Account;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id(); // Bigint unsigned for id
            $table->unsignedBigInteger('parent_id')->nullable(); // Match with id's type
            $table->integer('position', false, true);
            $table->foreignId('partner_id')->constrained('partners');
            $table->string('type_letter')->nullable();
            $table->string('identifier', 20)->nullable();
            $table->string('name', Account::NAME_MAX_LENGTH);
            $table->string('slug', Account::NAME_MAX_LENGTH)->nullable();
            $table->bigInteger('accountable_id')->nullable();
            $table->string('accountable_type')->nullable();
            $table->decimal('balance', 20, 2)->default(0);
            $table->boolean('is_fixed')->default(0);
            $table->boolean('is_managed')->default(0);
            $table->softDeletes();

            $table->unique(['id', 'partner_id', 'parent_id', 'name']);
            $table->unique(['partner_id', 'identifier']);

            $table->foreign('parent_id')
                ->references('id')
                ->on('accounts')
                ->onDelete('set null'); // Foreign key linking parent_id to id in accounts table
            $table->timestamps();
        });

        Schema::create('account_closure', function (Blueprint $table) {
            $table->increments('closure_id');

            // Updated to unsignedBigInteger to match the accounts' id type
            $table->unsignedBigInteger('ancestor');
            $table->unsignedBigInteger('descendant');
            $table->integer('depth', false, true);

            $table->foreign('ancestor')
                ->references('id')
                ->on('accounts')
                ->onDelete('cascade');

            $table->foreign('descendant')
                ->references('id')
                ->on('accounts')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_closure');
        Schema::dropIfExists('accounts');
    }
};
