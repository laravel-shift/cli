<?php

namespace App\Support;

class TaskManifest
{
    private array $manifest = [];

    private string $manifestPath;

    private string $vendorPath;

    public function __construct(string $vendorPath)
    {
        $this->manifestPath = $vendorPath.'/shift-tasks.php';
        $this->vendorPath = $vendorPath;
    }

    public function list()
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

    public function build()
    {
        $packages = [];

        if (file_exists($path = $this->vendorPath.'/composer/installed.json')) {
            $installed = json_decode(file_get_contents($path), true);

            $packages = $installed['packages'] ?? $installed;
        }

        $this->write(collect($packages)
            ->mapWithKeys(function ($package) {
                return $package['extra']['shift']['tasks'] ?? [];
            })
            ->filter()
            ->merge($this->defaultTasks())
            ->all());
    }

    private function defaultTasks()
    {
        return [
            'check-lint' => \App\Tasks\CheckLint::class,
            'debug-calls' => \App\Tasks\DebugCalls::class,
            'format-code' => \App\Tasks\FormatCode::class,
        ];
    }

    protected function write(array $manifest)
    {
        if (! is_writable($dirname = dirname($this->manifestPath))) {
            throw new \Exception("The {$dirname} directory must be present and writable.");
        }

        file_put_contents(
            $this->manifestPath, '<?php return '.var_export($manifest, true).';'
        );
    }
}
