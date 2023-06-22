<?php

namespace App\Traits;

trait TracksDirty
{
    private bool $dirty = false;

    public function isDirty(): bool
    {
        return $this->dirty;
    }

    public function isNotDirty(): bool
    {
        return ! $this->isDirty();
    }

    private function dirty(): void
    {
        $this->dirty = true;
    }

    private function dirtyIf(bool $condition): void
    {
        if ($condition === true) {
            $this->dirty();
        }
    }

    private function resetDirty(): void
    {
        $this->dirty = false;
    }
}
