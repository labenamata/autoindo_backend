<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKredentialTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kredential', function (Blueprint $table) {
            $table->id(); // Primary key, auto-increment
            $table->string('email')->unique(); // Unique email column
            $table->string('key')->nullable(); // Nullable key column
            $table->string('secret')->nullable(); // Nullable secret column
            $table->timestamps(); // created_at and updated_at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kredential');
    }
}