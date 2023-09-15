<?php

namespace Tests\Support;

use Shift\Cli\Sdk\Contracts\Task;

class GoodTask implements Task
{
    public static string $name = 'good-task';

    public static string $description = 'A test task which immediately returns';

    public function perform(): int
    {
        return 0;
    }
}
