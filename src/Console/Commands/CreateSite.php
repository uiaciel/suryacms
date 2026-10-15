<?php

namespace Uiaciel\SuryaCms\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Uiaciel\SuryaCms\Models\Site;
use Uiaciel\SuryaCms\Models\SiteDomain;
use Uiaciel\SuryaCms\Models\Setting;

class CreateSite extends Command
{
    protected $signature = 'suryacms:site:create {domain : Primary domain name} {name : Site display name}';

    protected $description = 'Create a SuryaCMS site and map its primary domain.';

    public function handle(): int
    {
        if (! config('suryacms.multisite.enabled', false)) {
            $this->error('Enable SURYACMS_MULTISITE_ENABLED before creating additional sites.');

            return self::FAILURE;
        }

        if (! Schema::hasTable('sites') || ! Schema::hasTable('site_domains')) {
            $this->error('Run the SuryaCMS migrations before creating sites.');

            return self::FAILURE;
        }

        $domain = SiteDomain::normalize($this->argument('domain'));

        if (SiteDomain::query()->where('domain', $domain)->exists()) {
            $this->error("Domain {$domain} is already assigned to a site.");

            return self::FAILURE;
        }

        $site = DB::transaction(function () use ($domain): Site {
            $site = Site::query()->create(['name' => $this->argument('name')]);
            $site->domains()->create([
                'domain' => $domain,
                'is_primary' => true,
            ]);

            Setting::query()->create([
                'site_id' => $site->getKey(),
                'sitename' => $site->name,
            ]);

            return $site;
        });

        $this->info("Site {$site->getKey()} created for {$domain}.");

        return self::SUCCESS;
    }
}
