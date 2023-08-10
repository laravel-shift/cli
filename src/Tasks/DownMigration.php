<?php

namespace Shift\Cli\Tasks;

use Shift\Cli\Sdk\Contracts\Task;
use Shift\Cli\Sdk\Models\File;
use Shift\Cli\Sdk\Traits\FindsFiles;

class DownMigration implements Task
{
    use FindsFiles;

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
        foreach ($this->findFilesContaining('/\s+extends\s+Migration\s/') as $path) {
            if (str_starts_with($path, 'stubs/')) {
                continue;
            }

            $contents = $this->removeDownMethod(file_get_contents($path));
            if (is_null($contents)) {
                continue;
            }

            file_put_contents($path, $contents);
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
            if (! file_exists('stubs/' . $stub)) {
                continue;
            }

            $contents = file_get_contents('stubs/' . $stub);
            $contents = str_replace(
                ['DummyClass', '{{ class }}', '{{class}}'],
                'ShiftTemporaryClassNamePlaceholder',
                $contents
            );
            $contents = str_replace(
                ['DummyTable', '{{ table }}', '{{table}}'],
                'ShiftTemporaryTableNamePlaceholder',
                $contents
            );
            $contents = $this->removeDownMethod($contents);
            if (is_null($contents)) {
                continue;
            }

            $contents = str_replace('ShiftTemporaryClassNamePlaceholder', '{{ class }}', $contents);
            $contents = str_replace('ShiftTemporaryTableNamePlaceholder', '{{ table }}', $contents);
            file_put_contents('stubs/' . $stub, $contents);
        }
    }

    private function removeDownMethod(string $contents): ?string
    {
        $file = File::fromString($contents);
        $class = $this->parseClass($file->contents());
        if (! isset($class['methods']['down'])) {
            return null;
        }

        $first_line = $class['methods']['down']['line']['start'];
        $file->removeSegment($class['methods']['down']['line']['start'], $class['methods']['down']['line']['end']);

        if (isset($class['methods']['down']['comment'])) {
            $first_line = $class['methods']['down']['comment']['line']['start'];
            $file->removeSegment($class['methods']['down']['comment']['line']['start'], $class['methods']['down']['comment']['line']['end']);
        }

        $file->removeBlankLinesBefore($first_line);

        return $file->contents();
    }
}
