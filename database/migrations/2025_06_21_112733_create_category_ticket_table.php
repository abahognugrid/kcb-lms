<?php

namespace Coderflex\LaravelTicket\Database\Factories;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('category_ticket', function (Blueprint $table) {
            $table->foreignId('category_id');
            $table->foreignId('ticket_id');
        });
    }
    public function down()
    {
        Schema::dropIfExists('category_ticket');
    }
};
