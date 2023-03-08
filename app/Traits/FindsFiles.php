<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

trait FindsFiles
{
    protected array $files = [];

    protected bool $dirty = false;

    protected function findFiles()
    {
        if (!empty($this->files)) {
            return $this->files;
        }

        if ($this->dirty) {
            return $this->findDirtyFiles();
        }

        $finder = new Finder();
        $finder->files()
            ->in(rtrim(getcwd() . DIRECTORY_SEPARATOR . $this->subPath(), DIRECTORY_SEPARATOR))
            ->exclude('vendor')
            ->name('*.php');

        return array_map(fn ($file) => $file->getRealPath(), iterator_to_array($finder, false));
    }

    protected function findDirtyFiles(): array
    {
        $process = tap(new Process(['git', 'status', '--short', '--', '*.php']))->run();

        if (! $process->isSuccessful()) {
            abort(1, 'The [--dirty] option is only available when using Git.');
        }

        return collect(preg_split('/\R+/', $process->getOutput(), flags: PREG_SPLIT_NO_EMPTY))
            ->mapWithKeys(fn ($file) => [substr($file, 3) => trim(substr($file, 0, 3))])
            ->reject(fn ($status) => $status === 'D')
            ->map(fn ($status, $file) => $status === 'R' ? Str::after($file, ' -> ') : $file)
            ->values()
            ->all();
    }

    public function setFiles(array $files): void
    {
        $this->files = $files;
    }

    public function setDirty(bool $dirty): void
    {
        $this->dirty = $dirty;
    }

    protected function subPath(): string
    {
        return '';
    }
}
