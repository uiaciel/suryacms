<?php

namespace Uiaciel\SuryaCms\Console\Commands;

use Illuminate\Console\Command;
use Uiaciel\SuryaCms\Models\Site;

class ListSites extends Command
{
    protected $signature = 'suryacms:site:list';

    protected $description = 'List SuryaCMS sites and their mapped domains.';

    public function handle(): int
    {
        $sites = Site::query()->with('domains')->orderBy('id')->get();

        if ($sites->isEmpty()) {
            $this->warn('No sites are configured.');

            return self::SUCCESS;
        }

        $this->table(
            ['ID', 'Name', 'Domains'],
            $sites->map(fn (Site $site): array => [
                $site->getKey(),
                $site->name,
                $site->domains->pluck('domain')->implode(', '),
            ])->all()
        );

        return self::SUCCESS;
    }
}
