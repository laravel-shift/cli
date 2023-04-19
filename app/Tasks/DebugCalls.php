<?php

namespace App\Tasks;

use App\Traits\FindsFiles;

class DebugCalls
{
    use FindsFiles;

    public function perform(): int
    {
        $files = $this->findFiles();
        if (empty($files)) {
            return 0;
        }

        $finder = new \App\Parsers\NikicParser(new \App\Parsers\Finders\DebugCalls());
        $failure = false;

        foreach ($files as $file) {
            $contents = file_get_contents($file);

            $found = preg_match_all('/\b(print_r|var_dump|var_export|dd)\(/', $contents, $matches);
            if (! $found) {
                continue;
            }

            $instances = $finder->parse($contents);
            if (empty($instances)) {
                continue;
            }

            $failure = true;
            $this->displayError($file, $instances);

            foreach (array_reverse($instances) as $instance) {
                $contents = substr_replace(
                    $contents,
                    '',
                    $instance['offset']['start'],
                    $instance['offset']['end'] - $instance['offset']['start'] + 1
                );
            }

            file_put_contents($file, $contents);
        }

        return $failure ? 1 : 0;
    }

    private function displayError(string $path, array $calls)
    {
        echo $path;
        echo PHP_EOL;
        foreach ($calls as $call) {
            echo '  - Line ', $call['line']['start'], ': contains call to `', $call['function'], '`', PHP_EOL;
        }
        echo PHP_EOL;
    }
}
