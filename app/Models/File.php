<?php

namespace App\Models;

use App\Traits\TracksDirty;

class File
{
    use TracksDirty;

    private array $lines = [];

    private int $count = 0;

    private function __construct(array $lines)
    {
        $this->lines = $lines;
        $this->count = count($lines);
    }

    public static function fromPath(string $path): static
    {
        return new self(file($path));
    }

    public static function fromString(string $string, $newlines_only = false): static
    {
        return new self(preg_split(
            '/(?<=' . ($newlines_only ? '\n' : '\r\n|\r|\n') . ')/',
            $string,
            flags: PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
        ));
    }

    public function append(string $content): void
    {
        $this->lines[] = $content;
        $this->count++;
        $this->dirty();
    }

    public function contents(): string
    {
        return implode($this->lines);
    }

    public function lineAtCharacter(int $offset): int
    {
        // This only works when contents have not been manipulated.
        // For example, by calling `insert` with multiple lines.
        return substr_count($this->contents(), PHP_EOL, 0, $offset) + 1;
    }

    public function find(string $needle, int $offset = 1): int|bool
    {
        for ($line = $offset; $line < $this->count; $line++) {
            if (str_contains($this->lines[$line - 1], $needle)) {
                return $line;
            }
        }

        return false;
    }

    public function lineCount(): int
    {
        return $this->count;
    }

    public function line($number): string
    {
        return $this->lines[$number - 1];
    }

    public function removeLine(int $number): void
    {
        array_splice($this->lines, $number - 1, 1);
        $this->count = count($this->lines);
        $this->dirty();
    }

    public function removeSegment(int $start, int $end): void
    {
        if ($start === $end) {
            $this->removeLine($start);

            return;
        }

        array_splice($this->lines, $start - 1, $end - $start + 1);
        $this->count = count($this->lines);
        $this->dirty();
    }

    public function segment(int $start, int $end): string
    {
        if ($start < 1 || $start > $this->count) {
            return '';
        }

        if ($start === $end) {
            return $this->lines[$start - 1];
        }

        if ($end > $this->count) {
            return implode(array_slice($this->lines, $start - 1));
        }

        return implode(array_slice($this->lines, $start - 1, $end - $start + 1));
    }

    public function replaceSegment(int $start, int $end, string $content = ''): void
    {
        $this->lines[$start - 1] = $content;

        if ($start === $end) {
            return;
        }

        array_splice($this->lines, $start, $end - $start);
        $this->count = count($this->lines);
        $this->dirty();
    }

    public function insert(int $start, string $content): void
    {
        array_splice($this->lines, $start, 0, $content);
        $this->count = count($this->lines);
        $this->dirty();
    }

    public function removeBlankLinesBefore(int $start): void
    {
        while (trim($this->line($start - 1)) === '') {
            $this->removeLine($start - 1);
            $start--;
        }
    }

    public function removeBlankLinesAfter(int $start): void
    {
        while ($start < $this->count && trim($this->line($start + 1)) === '') {
            $this->removeLine($start + 1);
        }
    }
}
