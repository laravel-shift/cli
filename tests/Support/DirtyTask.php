<?php

namespace Tests\Support;

use App\Traits\FindsFiles;

class DirtyTask
{
    use FindsFiles;

    public function perform()
    {
        if (! $this->dirty) {
            throw new \RuntimeException('This task expects the `dirty` option to be set');
        }

        return 0;
    }
}
