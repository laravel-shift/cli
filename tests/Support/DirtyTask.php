<?php

namespace Tests\Support;

use App\Contracts\Task;
use App\Traits\FindsFiles;

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
