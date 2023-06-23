<?php

namespace Tests\Support;

use App\Contracts\Task;
use App\Traits\FindsFiles;

class PathTask implements Task
{
    use FindsFiles;

    public function perform(): int
    {
        if (count($this->findFiles()) !== 3) {
            throw new \RuntimeException('This task expects 3 paths to be passed');
        }

        return 0;
    }
}
