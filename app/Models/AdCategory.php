<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdCategory extends Model
{
    protected $guarded=['id'];

    // 广告
    public function ads()
    {
        return $this->hasMany(Ad::class);
    }
}
