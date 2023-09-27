<?php

namespace Shift\Cli\Commands;

use Shift\Cli\Support\TaskManifest;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'discover',
    description: 'Register any additional automated tasks within the project',
)]
class DiscoverCommand extends Command
{
    private TaskManifest $taskManifest;

    public function __construct(TaskManifest $taskManifest)
    {
        parent::__construct();

        $this->taskManifest = $taskManifest;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Discovering tasks');

        $this->taskManifest->build();

        \collect($this->taskManifest->list())
            ->keys()
            ->each(fn ($task) => $output->writeln($task))
            ->whenNotEmpty(fn () => $output->writeln(''));

        return 0;
    }
}
