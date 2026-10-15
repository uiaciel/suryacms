<?php

namespace Uiaciel\SuryaCMS\Models;

use Illuminate\Database\Eloquent\Model;
use Uiaciel\SuryaCms\Models\Concerns\BelongsToSite;

class YoutubeVideo extends Model
{
    use BelongsToSite;

    protected $fillable = [
        'title',
        'description',
        'video_url',
        'video_id',
        'thumbnail_url',
        'category',
        'status',
        'published_at',
    ];
}
