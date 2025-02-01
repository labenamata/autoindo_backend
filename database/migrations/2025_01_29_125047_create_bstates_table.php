<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bstate', function (Blueprint $table) {
            $table->id(); // Primary key, auto-increment
            $table->string('email')->unique(); // Unique string with indexing
            $table->string('state')->nullable(); // Nullable string
            $table->timestamps(); // created_at and updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('bstate');
    }
};
