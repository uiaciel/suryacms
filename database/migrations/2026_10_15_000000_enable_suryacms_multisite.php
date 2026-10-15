<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Uiaciel\SuryaCms\Models\SiteDomain;

return new class extends Migration
{
    private array $tenantTables = [
        'settings',
        'categories',
        'languages',
        'pages',
        'posts',
        'menus',
        'galleries',
        'contacts',
        'comments',
        'youtube_videos',
        'visitors',
        'custom_blocks',
        'suryacms_settings',
    ];

    public function up(): void
    {
        Schema::create('sites', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('site_domains', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
            $table->string('domain')->unique();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        $siteId = DB::table('sites')->insertGetId([
            'name' => config('app.name', 'SuryaCMS'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $configuredHost = parse_url((string) config('app.url'), PHP_URL_HOST);
        $primaryDomain = SiteDomain::normalize(is_string($configuredHost) && $configuredHost !== '' ? $configuredHost : 'localhost');

        DB::table('site_domains')->insert([
            'site_id' => $siteId,
            'domain' => $primaryDomain,
            'is_primary' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ($this->tenantTables as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($siteId): void {
                $table->unsignedBigInteger('site_id')->default($siteId)->index();
            });
        }

        $this->replaceUniqueIndexes('posts', 'posts_slug_unique', ['site_id', 'slug'], 'posts_site_id_slug_unique');
        $this->replaceUniqueIndexes('pages', 'pages_slug_unique', ['site_id', 'slug'], 'pages_site_id_slug_unique');
        $this->replaceUniqueIndexes('contacts', 'contacts_email_unique', ['site_id', 'email'], 'contacts_site_id_email_unique');
        $this->replaceUniqueIndexes('suryacms_settings', 'suryacms_settings_key_unique', ['site_id', 'key'], 'suryacms_settings_site_id_key_unique');
    }

    public function down(): void
    {
        foreach ([
            ['posts', 'posts_site_id_slug_unique', ['slug']],
            ['pages', 'pages_site_id_slug_unique', ['slug']],
            ['contacts', 'contacts_site_id_email_unique', ['email']],
            ['suryacms_settings', 'suryacms_settings_site_id_key_unique', ['key']],
        ] as [$tableName, $index, $columns]) {
            if (Schema::hasTable($tableName) && $this->hasIndex($tableName, $index)) {
                Schema::table($tableName, function (Blueprint $table) use ($index, $columns): void {
                    $table->dropUnique($index);
                    $table->unique($columns);
                });
            }
        }

        foreach (array_reverse($this->tenantTables) as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'site_id')) {
                Schema::table($tableName, function (Blueprint $table): void {
                    $table->dropIndex(['site_id']);
                    $table->dropColumn('site_id');
                });
            }
        }

        Schema::dropIfExists('site_domains');
        Schema::dropIfExists('sites');
    }

    private function replaceUniqueIndexes(string $tableName, string $oldIndex, array $columns, string $newIndex): void
    {
        if (! Schema::hasTable($tableName) || ! $this->hasIndex($tableName, $oldIndex)) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($oldIndex, $columns, $newIndex): void {
            $table->dropUnique($oldIndex);
            $table->unique($columns, $newIndex);
        });
    }

    private function hasIndex(string $tableName, string $indexName): bool
    {
        foreach (Schema::getIndexes($tableName) as $index) {
            if ($index['name'] === $indexName) {
                return true;
            }
        }

        return false;
    }
};
