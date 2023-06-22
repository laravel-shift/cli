<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void addComment(string $comment, array $paths = [], string $reference = null)
 * @method static array flush()
 *
 * @see \App\Support\CommentRepository
 */
class Comment extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Comment::class;
    }
}
