<?php

namespace App\Tasks;

use App\Contracts\Task;
use App\Parsers\Finders\QueryOrderByFinder;
use App\Parsers\NikicParser;
use App\Support\File;
use App\Traits\FindsFiles;

class ExplicitOrderBy implements Task
{
    use FindsFiles;

    public function perform(): int
    {
        $finder = new QueryOrderByFinder(false, true);
        $parser = new NikicParser($finder);

        foreach ($this->files as $path) {
            $file = File::fromPath($path);
            $contents = $file->contents();

            if (! preg_match('/(::|->)\s*orderBy\(/i', $contents)) {
                continue;
            }

            $occurrences = $parser->parse($contents);

            if (empty($occurrences)) {
                continue;
            }

            foreach ($occurrences as $occurrence) {
                $block = $file->segment($occurrence['startLine'], $occurrence['endLine']);

                $pattern = '/\borderBy\(([^,>]+?),\s*(\'|")(asc|desc)\2\s*\)/i';

                $new_block = preg_replace_callback(
                    $pattern,
                    function ($match) {
                        $method = strtoupper($match[3]) === 'DESC' ? 'orderByDesc' : 'orderBy';

                        return sprintf('%s(%s)', $method, trim($match[1]));
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
