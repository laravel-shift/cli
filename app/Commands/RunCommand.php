<?php

namespace App\Commands;

use InvalidArgumentException;
use LaravelZero\Framework\Commands\Command;

class RunCommand extends Command
{
    protected $signature = 'run {task* : The name of the automated task} {--dirty?}';

    protected $description = 'Run one or more automated tasks';

    public function handle()
    {
        foreach ($this->argument('task') as $task) {
            $result = (new ($this->taskRegistry($task)))->perform();
            if ($result !== 0) {
                $this->error('Failed to run task: '.$task);

                return $result;
            }
        }

        return 0;
    }

    private function taskRegistry(string $task): string
    {
        $tasks = [
            'check-lint' => \App\Tasks\CheckLint::class,
            'debug-calls' => \App\Tasks\DebugCalls::class,
            'format-code' => \App\Tasks\FormatCode::class,
            // ...
        ];

        if (! isset($tasks[$task])) {
            throw new InvalidArgumentException('Task not registered: '.$task);
        }

        return $tasks[$task];
    }
}
