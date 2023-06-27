<?php

namespace App\Tasks;

use App\Contracts\Task;
use App\Traits\FindsFiles;
use Illuminate\Support\Str;

class AnonymousMigrations implements Task
{
    use FindsFiles;

    public function perform(): int
    {
        $this->updateMigrations();
        $this->updateStubs();

        return 0;
    }

    private function updateMigrations(): void
    {
        foreach ($this->findFilesContaining('/\bclass\s+\S+\s+extends\s+Migration\s/') as $path) {
            if (str_starts_with($path, 'stubs/')) {
                continue;
            }

            $contents = $this->convertClassDefinition(file_get_contents($path));
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

            $contents = $this->convertClassDefinition($contents);
            if (is_null($contents)) {
                continue;
            }

            $contents = str_replace('ShiftTemporaryClassNamePlaceholder', '{{ class }}', $contents);
            $contents = str_replace('ShiftTemporaryTableNamePlaceholder', '{{ table }}', $contents);

            file_put_contents('stubs/' . $stub, $contents);
        }
    }

    private function convertClassDefinition($contents): ?string
    {
        $found = preg_match('/^class\s+(\S+)\s+extends\s+Migration(\s+)/m', $contents, $matches);
        if (! $found) {
            return null;
        }

        $contents = str_replace(rtrim($matches[0]), 'return new class extends Migration', $contents);
        $contents = preg_replace('/\b' . preg_quote($matches[1], '/') . '::/', 'self::', $contents);

        return Str::replaceLast('}', '};', $contents);
    }
}
