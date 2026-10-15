<?php

namespace Uiaciel\SuryaCms\Support;

use Uiaciel\SuryaCms\Models\SiteDomain;

class SiteManagementAccess
{
    public function allowsAccess(): bool
    {
        if (! config('suryacms.multisite.enabled', false)) {
            return false;
        }

        $site = app(SiteContext::class)->current();
        $domain = app()->bound('suryacms.current_domain')
            ? app('suryacms.current_domain')
            : null;

        return $site !== null
            && $domain instanceof SiteDomain
            && (int) $site->getKey() === (int) config('suryacms.multisite.management_site_id', 1)
            && (int) $domain->site_id === (int) $site->getKey()
            && $domain->is_primary;
    }

    public function ensureAccess(): void
    {
        abort_unless($this->allowsAccess(), 403, 'Site management is available only on the primary administration domain.');
    }
}
