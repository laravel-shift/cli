<?php

namespace Tests\Support;

use Shift\Cli\Sdk\Contracts\Task;

class BadTask implements Task
{
    public function perform(): int
    {
        return 123;
    }
}
