<?php

namespace Tests\Support;

use Shift\Cli\Contracts\Task;
use Shift\Cli\Traits\FindsFiles;

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
