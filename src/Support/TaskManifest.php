<?php

namespace Shift\Cli\Support;

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
            'anonymous-migrations' => \Shift\Cli\Tasks\AnonymousMigrations::class,
            'check-lint' => \Shift\Cli\Tasks\CheckLint::class,
            'class-strings' => \Shift\Cli\Tasks\ClassStrings::class,
            'debug-calls' => \Shift\Cli\Tasks\DebugCalls::class,
            'declare-strict' => \Shift\Cli\Tasks\DeclareStrictTypes::class,
            'down-migration' => \Shift\Cli\Tasks\DownMigration::class,
            'explicit-orderby' => \Shift\Cli\Tasks\ExplicitOrderBy::class,
            'facade-aliases' => \Shift\Cli\Tasks\FacadeAliases::class,
            'faker-methods' => \Shift\Cli\Tasks\FakerMethods::class,
            'laravel-carbon' => \Shift\Cli\Tasks\LaravelCarbon::class,
            'latest-oldest' => \Shift\Cli\Tasks\LatestOldest::class,
            'model-table' => \Shift\Cli\Tasks\ModelTableName::class,
            'order-model' => \Shift\Cli\Tasks\OrderModel::class,
            'remove-docblocks' => \Shift\Cli\Tasks\RemoveDocBlocks::class,
            'rules-arrays' => \Shift\Cli\Tasks\RulesArrays::class,
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
