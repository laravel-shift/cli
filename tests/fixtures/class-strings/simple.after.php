<?php

namespace Shift\Cli\Models;

use Illuminate\Eloquent\Model;

class Post extends Model
{
    public function user()
    {
        return $this->belongsTo(\Shift\Cli\Models\User::class);
    }

    public function comments()
    {
        return $this->hasMany(\Shift\Cli\Models\Comment::class);
    }
}
