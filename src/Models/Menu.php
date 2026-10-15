<?php

namespace Uiaciel\SuryaCMS\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Uiaciel\SuryaCms\Models\Concerns\BelongsToSite;

class Menu extends Model
{
    use HasFactory;
    use BelongsToSite;

    protected $fillable = ['name', 'category', 'type', 'link', 'parent_id', 'order'];

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id');
    }
}
