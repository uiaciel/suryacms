<?php

namespace Uiaciel\SuryaCms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    protected $fillable = ['name'];

    public function domains(): HasMany
    {
        return $this->hasMany(SiteDomain::class);
    }
}
