<?php

namespace Shift\Cli\Commands;

use Illuminate\Console\Command;
use Shift\Cli\Support\TaskManifest;

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
