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
        Schema::table('galleries', function (Blueprint $table) {
            $table->string('image_path')->nullable()->change();

            // Menambahkan kolom baru
            $table->string('cover_path')->nullable()->after('image_path');
            $table->enum('mime_type', ['image', 'pdf'])->default('image')->after('cover_path');
            $table->string('file_size')->nullable()->after('mime_type');
            $table->string('alt_text')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('galleries', function (Blueprint $table) {
            $table->dropColumn(['cover_path', 'mime_type', 'file_size', 'alt_text']);
            $table->string('image_path')->nullable(false)->change();
        });
    }
};
