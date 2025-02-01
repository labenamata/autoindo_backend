<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('koin', function (Blueprint $table) {
            $table->id('koin_id');
            $table->string('name');
            $table->string('currency');
            $table->string('image');
            $table->string('fee');
            $table->string('ticker');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('koin');
    }
};
