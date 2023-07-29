<?php

namespace Shift\Cli\Tasks;

use Shift\Cli\Contracts\Task;
use Shift\Cli\Models\File;
use Shift\Cli\Parsers\Finders\QueryOrderByFinder;
use Shift\Cli\Parsers\NikicParser;
use Shift\Cli\Traits\FindsFiles;

class LatestOldest implements Task
{
    use FindsFiles;

    public function perform(): int
    {
        $finder = new QueryOrderByFinder(false, false);
        $parser = new NikicParser($finder);

        foreach ($this->findFilesContaining('/(::|->)\s*orderBy\(/i') as $path) {
            $file = File::fromPath($path);
            $contents = $file->contents();
            $occurrences = $parser->parse($contents);

            if (empty($occurrences)) {
                continue;
            }

            foreach ($occurrences as $occurrence) {
                if (isset($occurrence['directionNotString']) && $occurrence['directionNotString'] === true) {
                    continue;
                }

                $block = $file->segment($occurrence['line']['start'], $occurrence['line']['end']);

                $pattern = '/\b' . $occurrence['method'] . '\((\'|")([^,)>]+?)\1(?:,\s*(\'|")([^)]+?)\3)?\)/i';

                $new_block = preg_replace_callback(
                    $pattern,
                    function ($match) use ($occurrence) {
                        $method = $occurrence['method'] === 'orderbydesc' || strtoupper($match[4] ?? 'ASC') === 'DESC' ? 'latest' : 'oldest';
                        $column = $match[2];

                        return sprintf('%s(%s)', $method, $column !== 'created_at' ? "'$column'" : null);
                    },
                    $block
                );

                $contents = str_replace($block, $new_block, $contents);
            }

            file_put_contents($path, $contents);
        }

        return 0;
    }
}