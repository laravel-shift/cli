<?php

namespace Tests\Support;

use Shift\Cli\Sdk\Contracts\Task;
use Shift\Cli\Sdk\Facades\Comment;

class CommentTask implements Task
{
    public function perform(): int
    {
        Comment::addComment('Leaving a test comment.', ['file-1.php', 'file-2.php'], 'https://laravel.com/docs/');

        return 0;
    }
}
