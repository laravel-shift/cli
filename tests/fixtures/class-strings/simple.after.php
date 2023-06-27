<?php

namespace App\Models;

use Illuminate\Eloquent\Model;

class Post extends Model
{
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function comments()
    {
        return $this->hasMany(\App\Models\Comment::class);
    }
}
