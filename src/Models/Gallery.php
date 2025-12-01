<?php

namespace Uiaciel\SuryaCMS\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $fillable = [
        'name',
        'description',
        'image_path',
        'category',
        'status',
    ];
}
