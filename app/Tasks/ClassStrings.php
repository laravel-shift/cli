<?php

namespace App\Tasks;

use App\Contracts\Task;
use App\Traits\FindsFiles;

class ClassStrings implements Task
{
    use FindsFiles;

    public function perform(): int
    {
        $namespaces = $this->psr4Namespaces();
        if (empty($namespaces)) {
            return 0;
        }

        $pattern = '/[\'"]' . $this->patternForNamespaces(array_keys($namespaces)) . '/';
        $files = $this->findFilesContaining($pattern);

        if (empty($files)) {
            return 0;
        }

        $pattern = '/([\'"])' . $this->patternForNamespaces(array_keys($namespaces)) . '([\w\\\\]+)\1/';

        foreach ($files as $file) {
            $contents = preg_replace_callback(
                $pattern,
                function ($matches) {
                    // TODO: test for file...
                    return sprintf('\\%s\\%s::class', $matches[2], preg_replace('/\\\\+/', '\\', $matches[3]));
                },
                file_get_contents($file));

            file_put_contents($file, $contents);
        }

        return 0;
    }

    private function patternForNamespaces(array $namespaces)
    {
        return '\\\\?(' . implode('|', array_map(fn ($namespace) => preg_quote(rtrim($namespace, '\\'), '/'), $namespaces)) . ')\\\\+';
    }

    private function psr4Namespaces()
    {
        $composer = json_decode(file_get_contents('composer.json'), true);

        return $composer['autoload']['psr-4'] ?? [];
    }
}
