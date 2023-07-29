<?php

namespace Tests\Support;

use Shift\Cli\Contracts\Task;

class GoodTask implements Task
{
    public function perform(): int
    {
        return 0;
    }
}
