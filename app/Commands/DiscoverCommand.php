<?php

namespace App\Commands;

use App\Support\TaskManifest;
use LaravelZero\Framework\Commands\Command;

class DiscoverCommand extends Command
{
    protected $signature = 'discover';

    protected $description = 'Load any additional automated tasks within the project';

    public function handle(TaskManifest $manifest)
    {
        $this->components->info('Discovering tasks');

        $manifest->build();

        collect($manifest->list())
            ->keys()
            ->each(fn ($task) => $this->components->task($task))
            ->whenNotEmpty(fn () => $this->newLine());
    }
}
