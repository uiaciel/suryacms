<?php

namespace Uiaciel\SuryaCMS\Models;

use Illuminate\Database\Eloquent\Model;

class YoutubeVideo extends Model
{
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
