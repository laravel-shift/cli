<?php

namespace App\Contracts;

interface Task
{
    public function perform(): int;
}
