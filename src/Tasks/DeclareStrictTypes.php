<?php

namespace Shift\Cli\Tasks;

use Illuminate\Support\Str;
use Shift\Cli\Sdk\Contracts\Task;
use Shift\Cli\Sdk\Traits\FindsFiles;

class DeclareStrictTypes implements Task
{
    use FindsFiles;

    public function perform(): int
    {
        $files = $this->findFiles();
        if (empty($files)) {
            return 0;
        }

        foreach ($files as $file) {
            if (str_ends_with($file, '.blade.php')) {
                continue;
            }

            $contents = file_get_contents($file);

            $found = preg_match('/\s+declare\([^)]+?\);/', $contents, $matches);
            if ($found) {
                if (str_contains($matches[0], 'strict_types')) {
                    continue;
                }

                $contents = str_replace(
                    $matches[0],
                    Str::beforeLast($matches[0], ')') . ',strict_types=1);',
                    $contents
                );
            } else {
                $contents = Str::replaceFirst(
                    '<?php',
                    '<?php' . PHP_EOL . PHP_EOL . 'declare(strict_types=1);',
                    $contents,
                );
            }

            file_put_contents($file, $contents);
        }

        return 0;
    }
}
