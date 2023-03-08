<?php

namespace App\Commands;

use App\Support\TaskManifest;
use App\Traits\FindsFiles;
use InvalidArgumentException;
use LaravelZero\Framework\Commands\Command;

class RunCommand extends Command
{
    protected $signature = 'run {task* : The name of the automated task} {--dirty?} {path* : The paths to automate}';

    protected $description = 'Run one or more automated tasks';

    private TaskManifest $taskManifest;

    public function __construct(TaskManifest $taskManifest)
    {
        parent::__construct();

        $this->taskManifest = $taskManifest;
    }

    public function handle()
    {
        foreach ($this->argument('task') as $task) {
            $result = ($this->createTask($this->taskRegistry($task)))->perform();
            if ($result !== 0) {
                $this->error('Failed to run task: '.$task);

                return $result;
            }
        }

        return 0;
    }

    private function createTask(string $name): object
    {
        $task = new $name;

        if (trait_uses_recursive(FindsFiles::class)) {
            if ($this->hasArgument('path')) {
                $task->setFiles($this->argument('path'));
            }

            if ($this->hasOption('dirty')) {
                $task->setDirty(true);
            }
        }

        return $task;
    }

    private function taskRegistry(string $task): string
    {
        $tasks = $this->taskManifest->list();

        if (! isset($tasks[$task])) {
            throw new InvalidArgumentException('Task not registered: '.$task);
        }

        return $tasks[$task];
    }
}
