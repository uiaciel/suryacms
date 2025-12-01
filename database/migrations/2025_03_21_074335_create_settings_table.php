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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('sitename');
            $table->string('sitename_translation')->nullable();
            $table->string('tagline')->nullable();
            $table->string('tagline_translation')->nullable();
            $table->text('description')->nullable();
            $table->text('description_translation')->nullable();
            $table->text('keywords')->nullable();
            $table->text('keywords_translation')->nullable();
            $table->text('googlesiteverification')->nullable();

            $table->text('address')->nullable();
            $table->string('email')->nullable();
            $table->text('phone')->nullable();

            $table->string('whatsapp')->nullable();
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('instagram')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('youtube')->nullable();
            $table->string('tiktok')->nullable();

            $table->string('url')->nullable();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('images')->nullable();
            $table->string('active_theme')->default('default');

            $table->string('homepage_type')->default('index');
            $table->string('homepage_id')->nullable();

            $table->text('google_analytics')->nullable();
            $table->text('google_adsense')->nullable();
            $table->string('language')->default('en');
            $table->enum('is_multilingual', ['Yes', 'No'])->default('No');

            $table->string('color_primary')->nullable();
            $table->string('color_secondary')->nullable();
            $table->string('color_success')->nullable();
            $table->string('color_danger')->nullable();
            $table->string('color_warning')->nullable();
            $table->string('color_info')->nullable();
            $table->string('color_light')->nullable();
            $table->string('color_dark')->nullable();

            $table->boolean('site_maintenance')->default(false);

            $table->string('email_forwarder')->nullable();

            $table->string('date_format')->default('d/m/Y');

            $table->string('code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
