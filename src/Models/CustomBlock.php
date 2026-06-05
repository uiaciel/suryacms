<?php

namespace Uiaciel\SuryaCms\Models;

use Illuminate\Database\Eloquent\Model;

class CustomBlock extends Model
{
    protected $fillable = ['name', 'category', 'html', 'css', 'settings', 'thumbnail'];
    protected $casts = ['settings' => 'array'];
}
