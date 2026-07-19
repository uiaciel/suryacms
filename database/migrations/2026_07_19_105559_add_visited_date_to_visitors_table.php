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
        Schema::table('visitors', function (Blueprint $table) {
            $table->date('visited_date')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            // Hapus index dan kolom jika migration di-rollback
            $table->dropIndex(['visited_date']); // Menghapus index-nya terlebih dahulu
            $table->dropColumn('visited_date');  // Baru hapus kolomnya
        });
    }
};
