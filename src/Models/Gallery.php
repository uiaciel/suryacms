<?php

namespace Uiaciel\SuryaCMS\Models;

use Illuminate\Database\Eloquent\Model;
use Uiaciel\SuryaCms\Models\Concerns\BelongsToSite;

class Gallery extends Model
{
    use BelongsToSite;

    protected $fillable = [
        'name',
        'description',
        'image_path',
        'category',
        'status',
    ];
}
