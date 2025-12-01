<?php

namespace Uiaciel\SuryaCMS\Models;

use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    protected $fillable = [
        'ip_address',
        'user_agent',
    ];
}
