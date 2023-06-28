<?php

namespace App\Commands;

use App\Facades\Configuration;
use LaravelZero\Framework\Commands\Command;

class PublishCommand extends Command
{
    protected $signature = 'publish {--force : Overwrite any existing files}';

    protected $description = 'Publish a default Shift CLI configuration file';

    public function handle(): int
    {
        if (file_exists('shift-cli.json') && ! $this->option('force')) {
            $this->line('<comment>A configuration file already exists.</comment> Use the `--force` option to overwrite yours.');

            return 1;
        }

        file_put_contents('shift-cli.json', json_encode($this->defaultConfig(), JSON_PRETTY_PRINT));

        return 0;
    }

    private function defaultConfig(): array
    {
        return array_replace(Configuration::defaults(), [
            'ignore' => [],
        ]);
    }
}
