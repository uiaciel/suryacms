<?php

namespace Uiaciel\SuryaCms\Models;

use Illuminate\Database\Eloquent\Model;
use Uiaciel\SuryaCms\Models\Concerns\BelongsToSite;

class SuryacmsSetting extends Model
{
    use BelongsToSite;

    protected $table = 'suryacms_settings';

    protected $fillable = ['key', 'value'];
}
