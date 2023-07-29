<?php

namespace Shift\Cli\Tasks;

use Shift\Cli\Contracts\Task;
use Shift\Cli\Traits\FindsFiles;

class LaravelCarbon implements Task
{
    use FindsFiles;

    public function perform(): int
    {
        foreach ($this->findFiles() as $path) {
            $contents = file_get_contents($path);
            $contents = preg_replace('/Carbon\\\\Carbon(?![\w\\\\])/', 'Illuminate\\Support\\Carbon', $contents);
            file_put_contents($path, $contents);
        }

        return 0;
    }
}
