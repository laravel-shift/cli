<?php

namespace Tests\Support;

use App\Traits\FindsFiles;

class PathTask
{
    use FindsFiles;

    public function perform()
    {
        if (count($this->files) !== 3) {
            throw new \RuntimeException('This task expects 3 paths to be passed');
        }

        return 0;
    }
}
