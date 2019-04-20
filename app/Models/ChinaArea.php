<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class ChinaArea extends Model
{
    use NodeTrait;

    public function parent()
    {
        return $this->belongsTo(ChinaArea::class, 'parent_id');
    }
    public function children()
    {
        return $this->hasMany(ChinaArea::class, 'parent_id');
    }

    public function brothers()
    {
        return $this->parent->children();
    }

    public static function options($id)
    {
        if (! $self = static::find($id)) {
            return [];
        }
        return $self->brothers()->pluck('name', 'id');
    }

}
