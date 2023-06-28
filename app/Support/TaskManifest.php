<?php

namespace App\Support;

class TaskManifest
{
    private array $manifest = [];

    private string $manifestPath;

    private string $vendorPath;

    public function __construct(string $vendorPath)
    {
        $this->manifestPath = $vendorPath . '/shift-tasks.php';
        $this->vendorPath = $vendorPath;
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

        if (file_exists($path = $this->vendorPath . '/composer/installed.json')) {
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

    private function defaultTasks(): array
    {
        return [
            'anonymous-migrations' => \App\Tasks\AnonymousMigrations::class,
            'check-lint' => \App\Tasks\CheckLint::class,
            'class-strings' => \App\Tasks\ClassStrings::class,
            'debug-calls' => \App\Tasks\DebugCalls::class,
            'declare-strict' => \App\Tasks\DeclareStrictTypes::class,
            'down-migration' => \App\Tasks\DownMigration::class,
            'explicit-orderby' => \App\Tasks\ExplicitOrderBy::class,
            'facade-aliases' => \App\Tasks\FacadeAliases::class,
            'faker-methods' => \App\Tasks\FakerMethods::class,
            'laravel-carbon' => \App\Tasks\LaravelCarbon::class,
            'latest-oldest' => \App\Tasks\LatestOldest::class,
            'model-table' => \App\Tasks\ModelTableName::class,
            'order-model' => \App\Tasks\OrderModel::class,
            'remove-docblocks' => \App\Tasks\RemoveDocBlocks::class,
            'rules-arrays' => \App\Tasks\RulesArrays::class,
        ];
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
