<?php

namespace Tests\Support;

use Shift\Cli\Contracts\Task;
use Shift\Cli\Traits\FindsFiles;

class DirtyTask implements Task
{
    use FindsFiles;

    public function perform(): int
    {
        if (! $this->dirty) {
            throw new \RuntimeException('This task expects the `dirty` option to be set');
        }

        return 0;
    }
}
