<?php

namespace Shift\Cli\Tasks;

use Shift\Cli\Contracts\Task;
use Shift\Cli\Traits\FindsFiles;

class FacadeAliases implements Task
{
    use FindsFiles;

    private array $coreFacades = [
        'App',
        'Artisan',
        'Auth',
        'Blade',
        'Broadcast',
        'Bus',
        'Cache',
        'Config',
        'Cookie',
        'Crypt',
        'DB',
        'Event',
        'File',
        'Gate',
        'Hash',
        'Lang',
        'Log',
        'Mail',
        'Notification',
        'Password',
        'Queue',
        'Redirect',
        'Redis',
        'Request',
        'Response',
        'Route',
        'Schema',
        'Session',
        'Storage',
        'URL',
        'Validator',
        'View',
    ];

    private array $helperClasses = [
        'Arr',
        'Str',
    ];

    public function perform(): int
    {
        foreach ($this->findFiles() as $file) {
            $contents = file_get_contents($file);

            $contents = $this->replaceAliasImports($contents);
            $contents = $this->replaceHelperReferences($contents);

            file_put_contents($file, $contents);
        }

        return 0;
    }

    private function addImport(string $import, mixed $contents): string
    {
        $count = 0;
        $replacement = 'use ' . $import . ';';

        $contents = preg_replace('/^use\s+/m', $replacement . PHP_EOL . '\0', $contents, 1, $count);
        if ($count) {
            return $contents;
        }

        $contents = preg_replace('/^namespace\s+[^;]+;/m', '\0' . PHP_EOL . PHP_EOL . $replacement, $contents, count: $count);
        if ($count) {
            return $contents;
        }

        $contents = preg_replace('/^declare\([^;]+;/m', '\0' . PHP_EOL . PHP_EOL . $replacement, $contents, count: $count);
        if ($count) {
            return $contents;
        }

        return preg_replace('/^<\\?php/', '\0' . PHP_EOL . PHP_EOL . $replacement, $contents, count: $count);
    }

    private function replaceAliasImports(string $contents): string
    {
        $contents = preg_replace(
            '/use (' . implode('|', $this->coreFacades) . ');/',
            'use Illuminate\\Support\\Facades\\\\$1;',
            $contents
        );

        return $this->replaceReferences($contents, $this->coreFacades);
    }

    private function replaceHelperReferences(string $contents): string
    {
        $contents = preg_replace(
            '/use (' . implode('|', $this->helperClasses) . ');/',
            'use Illuminate\\Support\\\\$1;',
            $contents
        );

        return $this->replaceReferences($contents, $this->helperClasses);
    }

    private function replaceReferences(string $contents, array $references): string
    {
        $imports = [];

        $contents = preg_replace_callback(
            '/(\W)\\\\(' . implode('|', $references) . ')::/',
            function ($matches) use (&$imports) {
                $imports[] = $matches[2];

                return $matches[1] . $matches[2] . '::';
            },
            $contents
        );

        foreach ($imports as $import) {
            $prefix = 'Illuminate\\Support\\';
            if (in_array($import, $this->coreFacades)) {
                $prefix .= 'Facades\\';
            }

            if (str_contains('use ' . $prefix . $import . ';', $contents)) {
                continue;
            }

            $contents = $this->addImport($prefix . $import, $contents);
        }

        return $contents;
    }
}
