<?php

namespace Shift\Cli\Tasks;

use Shift\Cli\Sdk\Contracts\Task;
use Shift\Cli\Sdk\Models\File;
use Shift\Cli\Sdk\Traits\FindsFiles;

class AnonymousMigrations implements Task
{
    use FindsFiles;

    public static string $name = 'anonymous-migrations';

    public static string $description = 'Converts migrations to use anonymous classes';

    public function perform(): int
    {
        $this->updateMigrations();
        $this->updateStubs();

        return 0;
    }

    private function parseClass(string $contents)
    {
        static $finder;

        $finder ??= new \Shift\Cli\Sdk\Parsers\NikicParser(new \Shift\Cli\Sdk\Parsers\Finders\ClassDefinition());

        return $finder->parse($contents);
    }

    private function updateMigrations(): void
    {
        foreach ($this->findFilesContaining('/\bclass\s+\S+\s+extends\s+Migration\s/') as $path) {
            if (\str_starts_with($path, 'stubs/')) {
                continue;
            }

            $contents = $this->convertClassDefinition(\file_get_contents($path));
            if (\is_null($contents)) {
                continue;
            }

            \file_put_contents($path, $contents);
        }
    }

    private function updateStubs(): void
    {
        $stubs = [
            'migration.stub',
            'migration.create.stub',
            'migration.update.stub',
        ];

        foreach ($stubs as $stub) {
            if (! \file_exists('stubs/' . $stub)) {
                continue;
            }

            $contents = \file_get_contents('stubs/' . $stub);
            $contents = \str_replace(
                ['DummyClass', '{{ class }}', '{{class}}'],
                'ShiftTemporaryClassNamePlaceholder',
                $contents
            );
            $contents = \str_replace(
                ['DummyTable', '{{ table }}', '{{table}}'],
                'ShiftTemporaryTableNamePlaceholder',
                $contents
            );

            $contents = $this->convertClassDefinition($contents);
            if (\is_null($contents)) {
                continue;
            }

            $contents = \str_replace('ShiftTemporaryClassNamePlaceholder', '{{ class }}', $contents);
            $contents = \str_replace('ShiftTemporaryTableNamePlaceholder', '{{ table }}', $contents);

            \file_put_contents('stubs/' . $stub, $contents);
        }
    }

    private function convertClassDefinition($contents): ?string
    {
        $file = File::fromString($contents);
        $class = $this->parseClass($file->contents());

        $found = \preg_match('/^class\s+(\S+)\s+extends\s+Migration(\s+)/m', $contents, $matches);
        if (! $found) {
            return null;
        }
        $contents = \substr_replace($contents,
            ';',
            $class['offset']['end'] + 1,
            0
        );

        $contents = \str_replace(\rtrim($matches[0]), 'return new class extends Migration', $contents);
        $contents = \preg_replace('/\b' . \preg_quote($matches[1], '/') . '::/', 'self::', $contents);

        return $contents;
    }
}
