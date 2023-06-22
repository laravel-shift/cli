<?php

namespace Tests\Support;

use App\Contracts\Task;

class GoodTask implements Task
{
    public function perform(): int
    {
        return 0;
    }
}
