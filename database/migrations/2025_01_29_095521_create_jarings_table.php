<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('jaring', function (Blueprint $table) {
            $table->id(); // Primary key, auto-increment
            $table->string('email')->nullable(); // Unique email
            $table->string('koin_id')->nullable(); // Nullable string for koin_id
            $table->string('modal')->nullable(); // Nullable string for modal
            $table->string('buy')->nullable(); // Nullable string for buy
            $table->string('sell')->nullable(); // Nullable string for sell
            $table->string('profit')->nullable(); // Nullable string for profit
            $table->string('status')->nullable(); // Nullable string for status
            $table->string('order_id')->nullable(); // Nullable string for order_id
            $table->timestamps(); // created_at and updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('jaring');
    }
};
