<?php

namespace Tests\Support;

use Shift\Cli\Sdk\Contracts\Task;
use Shift\Cli\Sdk\Traits\FindsFiles;

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
