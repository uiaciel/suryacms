<?php

namespace Uiaciel\SuryaCms\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Uiaciel\SuryaCms\Support\SiteContext;

trait BelongsToSite
{
    protected static function bootBelongsToSite(): void
    {
        static::addGlobalScope('site', function (Builder $builder): void {
            $siteId = app(SiteContext::class)->id();

            if ($siteId !== null) {
                $builder->where($builder->getModel()->qualifyColumn('site_id'), $siteId);
            }
        });

        static::creating(function (Model $model): void {
            $siteId = app(SiteContext::class)->id();

            if ($siteId !== null) {
                $model->setAttribute('site_id', $siteId);
            }
        });
    }
}
