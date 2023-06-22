<?php

namespace App\Tasks;

use App\Contracts\Task;
use App\Traits\FindsFiles;

class CheckLint implements Task
{
    use FindsFiles;

    public function perform(): int
    {
        $files = $this->findFiles();
        if (empty($files)) {
            return 0;
        }

        $failure = false;
        foreach ($files as $file) {
            $output = [];
            $exit_code = 0;
            exec('php -l ' . $file . ' 2>&1', $output, $exit_code);

            if ($exit_code !== 0) {
                [$line, $error] = $this->parseError($output);
                $this->displayError($file, $line, $error);
                $failure = true;
            }
        }

        return $failure ? 1 : 0;
    }

    private function parseError(array $lines): array
    {
        preg_match('/PHP (?:Fatal|Parse) error:\s+(?:syntax error, )?(.+?)\s+in\s+.+?\.php\s+on\s+line\s+(\d+)/', $lines[0], $matches);

        return [$matches[2], $matches[1]];
    }

    private function displayError(string $path, int $line, string $error): void
    {
        echo $path;
        echo PHP_EOL;
        echo '  - Line ', $line, ': ', $error;
        echo PHP_EOL;
        echo PHP_EOL;
    }
}
