<?php

namespace Shift\Cli\Contracts;

interface Task
{
    public function perform(): int;
}
