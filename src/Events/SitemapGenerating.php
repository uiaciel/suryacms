<?php

namespace Uiaciel\SuryaCms\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Collection;

class SitemapGenerating
{
    use Dispatchable;

    public $urls;

    public function __construct(Collection $urls)
    {
        // Kita menggunakan Collection agar bisa diubah (mutable) oleh listener
        $this->urls = $urls;
    }
}
