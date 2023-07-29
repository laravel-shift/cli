<?php

namespace Shift\Cli\Traits;

use Illuminate\Support\Str;
use Shift\Cli\Facades\Configuration;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

trait FindsFiles
{
    protected ?array $files = null;

    protected array $paths = [];

    protected bool $dirty = false;

    protected function findFiles(): array
    {
        $this->files ??= $this->dirty ? $this->dirtyFiles() : $this->projectFiles();

        return $this->files;
    }

    protected function findFilesContaining(string $pattern): array
    {
        return array_filter(
            $this->findFiles(),
            fn ($file) => preg_match($pattern, file_get_contents($file))
        );
    }

    protected function dirtyFiles(): array
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

    private function projectFiles(): array
    {
        $finder = (new Finder())->files();

        if (! empty($this->paths)) {
            $directories = array_filter($this->paths, fn ($path) => is_dir($path));

            if (empty($directories)) {
                return $this->paths;
            }

            $finder->in($directories);

            $files = array_diff($this->paths, $directories);
            if (! empty($files)) {
                $finder->append($files);
            }
        } else {
            $finder->exclude('vendor')
                ->in(rtrim(getcwd() . DIRECTORY_SEPARATOR . $this->subPath(), DIRECTORY_SEPARATOR))
                ->notPath(Configuration::get('ignore', []))
                ->name('*.php');
        }

        return array_map(fn ($file) => $file->getRealPath(), iterator_to_array($finder, false));
    }

    public function setPaths(array $paths): void
    {
        $this->paths = $paths;
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
