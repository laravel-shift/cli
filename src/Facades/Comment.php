<?php

namespace Shift\Cli\Facades;

use Shift\Cli\Support\CommentRepository;

/**
 * @method static void addComment(string $comment, array $paths = [], string $reference = null)
 * @method static \Shift\Cli\Models\Comment[] flush()
 *
 * @see \Shift\Cli\Support\CommentRepository
 */
class Comment extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Comment::class;
    }

    protected static function getInstance()
    {
        return new CommentRepository();
    }
}
