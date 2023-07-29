<?php

namespace Shift\Cli\Models;

class Comment
{
    private string $content;

    private ?string $reference;

    private array $paths;

    public function __construct(string $content, array $paths, ?string $reference)
    {
        $this->content = $content;
        $this->paths = $paths;
        $this->reference = $reference;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function hasPaths(): bool
    {
        return count($this->paths) > 0;
    }

    public function hasReference(): bool
    {
        return $this->reference !== null;
    }

    public function paths(): array
    {
        return $this->paths;
    }

    public function reference(): ?string
    {
        return $this->reference;
    }
}
