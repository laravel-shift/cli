<?php

namespace App\Tasks;

use App\Traits\FindsFiles;

class RemoveDocBlocks
{
    use FindsFiles;

    public function perform(): int
    {
        $files = $this->findFiles();
        if (empty($files)) {
            return 0;
        }

        foreach ($files as $file) {
            $contents = file_get_contents($file);
            $contents = preg_replace('/^[ \t]*\/\*\*\R+([ \t]+\*[^\r\n]*\R)+[ \t]+\*\/\R/m', '', $contents);
            $contents = preg_replace('/^[ \t]*\/\*\*[^\r\n]+\*\/\R/m', '', $contents);
            file_put_contents($file, $contents);
        }

        return 0;
    }
}
