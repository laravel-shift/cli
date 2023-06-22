<?php

namespace App\Commands;

use App\Facades\Comment;
use App\Support\TaskManifest;
use App\Traits\FindsFiles;
use InvalidArgumentException;
use LaravelZero\Framework\Commands\Command;

class RunCommand extends Command
{
    protected $signature = 'run {task* : The name of the automated task} {--dirty} {--path=* : The paths to scan}';

    protected $description = 'Run one or more automated tasks';

    public function handle(): int
    {
        foreach ($this->argument('task') as $task) {
            $result = ($this->createTask($this->taskRegistry($task)))->perform();
            if ($result !== 0) {
                $this->error('Failed to run task: ' . $task);

                return $result;
            }

            foreach (Comment::flush() as $comment) {
                $this->outputComment($comment);
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

    private function outputComment(\App\Models\Comment $comment): void
    {
        $this->line($comment->content());

        if ($comment->hasReference()) {
            $this->line('Reference: ' . $comment->reference());
        }

        if ($comment->hasPaths()) {
            $this->newLine();
        }

        foreach ($comment->paths() as $path) {
            $this->line('  - ' . $path);
        }
    }

    private function taskRegistry(string $task): string
    {
        $tasks = resolve(TaskManifest::class)->list();

        if (! isset($tasks[$task])) {
            throw new InvalidArgumentException('Task not registered: ' . $task);
        }

        return $tasks[$task];
    }
}
