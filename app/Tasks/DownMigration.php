<?php

namespace App\Tasks;

use App\Contracts\Task;
use App\Support\File;
use App\Traits\FindsFiles;

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

        $finder ??= new \App\Parsers\NikicParser(new \App\Parsers\Finders\ClassDefinition());

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

        $first_line = $class['methods']['down']['startLine'];
        $file->removeSegment($class['methods']['down']['startLine'], $class['methods']['down']['endLine']);

        if (isset($class['methods']['down']['docblock'])) {
            $first_line = $class['methods']['down']['docblock']['line']['start'];
            $file->removeSegment($class['methods']['down']['docblock']['line']['start'], $class['methods']['down']['docblock']['line']['end']);
        }

        $file->removeBlankLinesBefore($first_line);

        return $file->contents();
    }
}
