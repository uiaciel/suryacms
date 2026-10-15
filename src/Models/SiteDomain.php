<?php

namespace Uiaciel\SuryaCms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteDomain extends Model
{
    protected $fillable = ['site_id', 'domain', 'is_primary'];

    protected $casts = ['is_primary' => 'boolean'];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public static function normalize(string $domain): string
    {
        $value = trim($domain);
        $host = parse_url(str_contains($value, '://') ? $value : 'http://'.$value, PHP_URL_HOST);

        if (! is_string($host) || $host === '') {
            throw new \InvalidArgumentException('A valid hostname is required.');
        }

        return strtolower(rtrim($host, '.'));
    }
}
