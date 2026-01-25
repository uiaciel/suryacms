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
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_spam' => 'boolean',
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
