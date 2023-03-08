<?php

namespace App\Commands;

use App\Support\TaskManifest;
use App\Traits\FindsFiles;
use InvalidArgumentException;
use LaravelZero\Framework\Commands\Command;

class RunCommand extends Command
{
    protected $signature = 'run {task* : The name of the automated task} {--dirty} {--path=* : The paths to scan}';

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

        if (in_array(FindsFiles::class, class_uses_recursive($task))) {
            if ($this->option('path')) {
                $task->setFiles($this->option('path'));
            }

            if ($this->option('dirty')) {
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
