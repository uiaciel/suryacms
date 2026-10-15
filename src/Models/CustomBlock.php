<?php

namespace Uiaciel\SuryaCms\Models;

use Illuminate\Database\Eloquent\Model;
use Uiaciel\SuryaCms\Models\Concerns\BelongsToSite;

class CustomBlock extends Model
{
    use BelongsToSite;

    protected $fillable = ['name', 'category', 'html', 'css', 'settings', 'thumbnail'];

    protected $casts = ['settings' => 'array'];
}
