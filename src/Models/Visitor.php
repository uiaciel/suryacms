<?php

namespace Uiaciel\SuryaCMS\Models;

use Illuminate\Database\Eloquent\Model;
use Uiaciel\SuryaCms\Models\Concerns\BelongsToSite;

class Visitor extends Model
{
    use BelongsToSite;

    protected $fillable = [
        'ip_address',
        'user_agent',
        'visited_date',
    ];
}
