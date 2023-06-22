<?php

namespace Tests\Support;

use App\Contracts\Task;
use App\Facades\Comment;

class CommentTask implements Task
{
    public function perform(): int
    {
        Comment::addComment('Leaving a test comment.', ['file-1.php', 'file-2.php'], 'https://laravel.com/docs/');

        return 0;
    }
}
