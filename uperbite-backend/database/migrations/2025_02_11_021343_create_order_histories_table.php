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
        Schema::create('order_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('warung_id')->constrained()->onDelete('cascade');
            $table->decimal('total_harga', 10, 2);
            $table->string('status'); // pending, diproses, selesai
            $table->timestamps();
        });
    }

    public function down()
    {

    }

};
