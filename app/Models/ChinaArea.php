<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChinaArea extends Model
{
    public function parent()
    {
        return $this->belongsTo(ChinaArea::class, 'parent_id');
    }
    public function children()
    {
        return $this->hasMany(ChinaArea::class, 'parent_id');
    }

}
