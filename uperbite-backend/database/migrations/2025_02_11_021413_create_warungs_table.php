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
        Schema::create('warungs', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->enum('lokasi', ['kantin atas', 'kantin bawah']);
            $table->foreignId('penjual_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warungs');
    }
};
