<?php

namespace Uiaciel\SuryaCMS\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model {

    protected $fillable = [
        'post_id',
        'parent_id',
        'author_name',
        'content',
        'likes',
        'ip_address',
        'user_agent',
        'status',
        'approved_at',
        'reactions',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function children()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function approve()
    {
        $this->update(['status' => 'approved', 'approved_at' => now()]);
    }
}
