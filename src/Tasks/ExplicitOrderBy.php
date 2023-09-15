<?php

namespace Shift\Cli\Tasks;

use Shift\Cli\Sdk\Contracts\Task;
use Shift\Cli\Sdk\Models\File;
use Shift\Cli\Sdk\Parsers\Finders\QueryOrderByFinder;
use Shift\Cli\Sdk\Parsers\NikicParser;
use Shift\Cli\Sdk\Traits\FindsFiles;

class ExplicitOrderBy implements Task
{
    use FindsFiles;

    public static string $name = 'explicit-orderby';

    public static string $description = 'Converts `orderBy()` calls to `orderByAsc()`/`orderByDesc()`';

    public function perform(): int
    {
        $finder = new QueryOrderByFinder(false, true);
        $parser = new NikicParser($finder);

        foreach ($this->findFilesContaining('/(::|->)\s*orderBy\(/i') as $path) {
            $file = File::fromPath($path);
            $contents = $file->contents();

            $occurrences = $parser->parse($contents);

            if (empty($occurrences)) {
                continue;
            }

            foreach ($occurrences as $occurrence) {
                $block = $file->segment($occurrence['line']['start'], $occurrence['line']['end']);

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
