<?php

namespace Shift\Cli\Support;

use Illuminate\Support\Str;

class TaskManifest
{
    private array $tasks = [];

    private string $manifestPath;

    private string $vendorPath;

    private array $defaultTasks;

    public function __construct(string $vendorPath, array $defaultTasks)
    {
        $this->defaultTasks = $defaultTasks;
        $this->vendorPath = $vendorPath;
        $this->manifestPath = $vendorPath . DIRECTORY_SEPARATOR . 'shift-tasks.php';
    }

    public function list(): array
    {
        if (! empty($this->tasks)) {
            return $this->tasks;
        }

        if (! is_file($this->manifestPath)) {
            $this->build();
        }

        $manifest = require $this->manifestPath;
        if (! $this->isStale($manifest)) {
            return $this->tasks = $manifest['tasks'];
        }

        $this->build();
        $manifest = require $this->manifestPath;
        $this->tasks = $manifest['tasks'];

        return $this->tasks;
    }

    public function build(): void
    {
        $packages = [];
        $path = $this->vendorPath . DIRECTORY_SEPARATOR . 'composer' . DIRECTORY_SEPARATOR . 'installed.json';

        if (file_exists($path)) {
            $installed = json_decode(file_get_contents($path), true);

            $packages = $installed['packages'] ?? $installed;
        }

        $this->write(collect($packages)
            ->mapWithKeys(function ($package) {
                return collect($package['extra']['shift']['tasks'] ?? [])->mapWithKeys(fn ($task) => [$task::$name => $task]);
            })
            ->filter()
            ->merge(collect($this->defaultTasks)->mapWithKeys(fn ($task) => [$task::$name => $task]))
            ->all());
    }

    private static function currentNamespace(): string
    {
        return Str::before(__NAMESPACE__, '\\');
    }

    private function isStale(array $manifest): bool
    {
        return $manifest['namespace'] !== self::currentNamespace();
    }

    protected function write(array $tasks): void
    {
        if (! is_writable($dirname = dirname($this->manifestPath))) {
            throw new \Exception("The {$dirname} directory must be present and writable.");
        }

        $manifest = [
            'namespace' => self::currentNamespace(),
            'tasks' => $tasks,
        ];

        file_put_contents(
            $this->manifestPath, '<?php return ' . var_export($manifest, true) . ';'
        );
    }
}
