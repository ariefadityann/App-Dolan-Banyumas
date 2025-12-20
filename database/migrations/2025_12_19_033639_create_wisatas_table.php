<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wisatas', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kategori');
            $table->string('deskripsi');
            $table->text('caption');
            $table->string('jarak')->nullable();
            $table->string('harga')->nullable();
            $table->string('gambar_url');
            $table->json('images')->nullable();
            $table->string('alamat');
            $table->string('telepon')->nullable();
            $table->string('jam_buka');
            $table->double('lat');
            $table->double('lng');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wisatas');
    }
};
