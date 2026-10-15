<?php

namespace Uiaciel\SuryaCms\Support;

use Uiaciel\SuryaCms\Models\Site;

class SiteContext
{
    public function current(): ?Site
    {
        $site = app()->bound('suryacms.current_site')
            ? app('suryacms.current_site')
            : null;

        return $site instanceof Site ? $site : null;
    }

    public function id(): ?int
    {
        return $this->current()?->getKey();
    }
}
