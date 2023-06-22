<?php

namespace App\Support;

class CommentRepository
{
    private array $comments = [];

    public function addWarningComment(string $comment, string $reference = null, array $paths = []): void
    {
        $this->comments[] = new Comment($type, $comment, $reference, $paths);
    }

    public function comments(): array
    {
        return $this->comments;
    }
}
