<?php

namespace Tests\Support;

use App\Contracts\Task;

class BadTask implements Task
{
    public function perform(): int
    {
        return 123;
    }
}
