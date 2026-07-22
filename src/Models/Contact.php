<?php

namespace Uiaciel\SuryaCMS\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'message',
        'status',
        'is_read',
        'ip_address',
        'user_agent',
        'is_spam',
        'referrer',
        'forwarded_at',
        'is_important',
        'notes',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_spam' => 'boolean',
        'is_important' => 'boolean',
        'forwarded_at' => 'datetime',
    ];

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeSpam($query)
    {
        return $query->where('is_spam', true);
    }
}
