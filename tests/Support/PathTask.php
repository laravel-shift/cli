<?php

namespace Tests\Support;

use Shift\Cli\Sdk\Contracts\Task;
use Shift\Cli\Sdk\Traits\FindsFiles;

class PathTask implements Task
{
    use FindsFiles;

    public static string $name = 'path-task';

    public static string $description = 'A test task which expects 3 paths';

    public function perform(): int
    {
        if (\count($this->findFiles()) !== 3) {
            throw new \RuntimeException('This task expects 3 paths to be passed');
        }

        return 0;
    }
}
