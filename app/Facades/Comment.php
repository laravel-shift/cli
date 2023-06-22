<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void addWarningComment(string $comment, string $reference = null, array $paths = [])
 * @method static array comments()
 */
class Comment extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Comment::class;
    }
}
