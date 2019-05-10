<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLoginDetail extends Model
{
    protected $guarded = ['id'];

    // 关联的用户
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
