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
        Schema::table('contacts', function (Blueprint $table) {
            if (! Schema::hasColumn('contacts', 'forwarded_at')) {
                $table->timestamp('forwarded_at')->nullable()->after('referrer');
            }
            if (! Schema::hasColumn('contacts', 'is_important')) {
                $table->boolean('is_important')->default(false)->after('forwarded_at');
            }
            if (! Schema::hasColumn('contacts', 'notes')) {
                $table->text('notes')->nullable()->after('is_important');
            }
        });

        Schema::table('settings', function (Blueprint $table) {
            if (! Schema::hasColumn('settings', 'contact_form_honeypot')) {
                $table->boolean('contact_form_honeypot')->default(true)->after('email_forwarder');
            }
            if (! Schema::hasColumn('settings', 'contact_form_rate_limit')) {
                $table->integer('contact_form_rate_limit')->default(3)->after('contact_form_honeypot');
            }
            if (! Schema::hasColumn('settings', 'contact_form_auto_forward')) {
                $table->boolean('contact_form_auto_forward')->default(false)->after('contact_form_rate_limit');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn(['forwarded_at', 'is_important', 'notes']);
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['contact_form_honeypot', 'contact_form_rate_limit', 'contact_form_auto_forward']);
        });
    }
};
