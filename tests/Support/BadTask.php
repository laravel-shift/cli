<?php

namespace Tests\Support;

use Shift\Cli\Sdk\Contracts\Task;

class BadTask implements Task
{
    public static string $name = 'bad-task';

    public static string $description = 'A test task which returns an error code';

    public function perform(): int
    {
        return 123;
    }
}
