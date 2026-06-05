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
        Schema::create('custom_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama blok
            $table->string('category')->default('Custom');
            $table->longText('html')->nullable(); // Komponen HTML
            $table->longText('css')->nullable();  // Komponen CSS
            $table->json('settings')->nullable(); // Simpan data konfigurasi GrapesJS
            $table->string('thumbnail')->nullable(); // Path gambar preview
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_blocks');
    }
};
