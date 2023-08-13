<?php

namespace Shift\Cli\Support;

class TaskManifest
{
    private array $manifest = [];

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
        if (! empty($this->manifest)) {
            return $this->manifest;
        }

        if (! is_file($this->manifestPath)) {
            $this->build();
        }

        return $this->manifest = is_file($this->manifestPath) ?
            require $this->manifestPath : [];
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
                return $package['extra']['shift']['tasks'] ?? [];
            })
            ->filter()
            ->merge($this->defaultTasks)
            ->all());
    }

    protected function write(array $manifest): void
    {
        if (! is_writable($dirname = dirname($this->manifestPath))) {
            throw new \Exception("The {$dirname} directory must be present and writable.");
        }

        file_put_contents(
            $this->manifestPath, '<?php return ' . var_export($manifest, true) . ';'
        );
    }
}
