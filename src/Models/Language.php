<?php

namespace Uiaciel\SuryaCMS\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Uiaciel\SuryaCms\Models\Concerns\BelongsToSite;

class Language extends Model
{
    use HasFactory;
    use BelongsToSite;

    protected $fillable = ['name', 'code', 'status'];
}
