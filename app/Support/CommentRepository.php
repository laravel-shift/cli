<?php

namespace App\Support;

use App\Models\Comment;

class CommentRepository
{
    private array $comments = [];

    public function addComment(string $content, array $paths = [], string $reference = null): void
    {
        $this->comments[] = new Comment($content, $paths, $reference);
    }

    public function flush(): array
    {
        $comments = $this->comments;
        $this->comments = [];

        return $comments;
    }
}
