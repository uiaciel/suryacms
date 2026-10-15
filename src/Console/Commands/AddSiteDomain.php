<?php

namespace Uiaciel\SuryaCms\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Uiaciel\SuryaCms\Models\Site;
use Uiaciel\SuryaCms\Models\SiteDomain;

class AddSiteDomain extends Command
{
    protected $signature = 'suryacms:site:domain {site : Site ID} {domain : Additional domain name}';

    protected $description = 'Map an additional domain to an existing SuryaCMS site.';

    public function handle(): int
    {
        if (! config('suryacms.multisite.enabled', false)) {
            $this->error('Enable SURYACMS_MULTISITE_ENABLED before adding site domains.');

            return self::FAILURE;
        }

        if (! Schema::hasTable('sites') || ! Schema::hasTable('site_domains')) {
            $this->error('Run the SuryaCMS migrations before adding site domains.');

            return self::FAILURE;
        }

        $site = Site::query()->find($this->argument('site'));

        if (! $site) {
            $this->error('The requested site does not exist.');

            return self::FAILURE;
        }

        $domain = SiteDomain::normalize($this->argument('domain'));

        if (SiteDomain::query()->where('domain', $domain)->exists()) {
            $this->error("Domain {$domain} is already assigned to a site.");

            return self::FAILURE;
        }

        $site->domains()->create(['domain' => $domain]);
        $this->info("Domain {$domain} was mapped to site {$site->getKey()}.");

        return self::SUCCESS;
    }
}
