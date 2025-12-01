<?php

namespace Uiaciel\SuryaCMS\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'status'];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function posts_count()
    {
        return $this->posts()->where('status', 'Publish')->count();
    }
}
