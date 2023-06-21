<?php

namespace App\Tasks;

use App\Contracts\Task;
use App\Traits\FindsFiles;

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
        foreach ($this->files as $file) {
            $contents = file_get_contents($file);

            $contents = $this->replaceAliasImports($contents);
            $contents = $this->replaceAliasReferences($contents);
            $contents = $this->replaceHelperReferences($contents);

            file_put_contents($file, $contents);
        }

        return 0;
    }

    private function replaceAliasImports(string $contents): string
    {
        return preg_replace(
            '/use (' . implode('|', $this->coreFacades) . ');/',
            'use Illuminate\\Support\\Facades\\\\$1;',
            $contents
        );
    }

    private function replaceAliasReferences(string $contents): string
    {
        return $this->replaceReferences($contents, $this->coreFacades);
    }

    private function replaceHelperReferences(string $contents): string
    {
        $contents = preg_replace(
            '/use (' . implode('|', $this->helperClasses) . ');/',
            'use Illuminate\\Support\\\\$1;',
            $contents
        );

        $contents = $this->replaceReferences($contents, $this->helperClasses);

        return $contents;
    }

    private function replaceReferences(string $contents, array $references): string
    {
        // TODO: does it have the correct import...
        return preg_replace(
            '/(\W)\\\\(' . implode('|', $references) . ')::/',
            '$1$2::',
            $contents
        );
    }
}
