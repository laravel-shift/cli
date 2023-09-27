<?php

namespace Shift\Cli\Commands;

use Shift\Cli\Sdk\Facades\Configuration;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'publish',
    description: 'Publish a default Shift CLI configuration file',
)]
class PublishCommand extends Command
{
    protected function configure(): void
    {
        $this->addOption('force', mode: InputOption::VALUE_NONE, description: 'Overwrite existing configuration file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (\file_exists('shift-cli.json') && ! $input->getOption('force')) {
            $output->writeln('<comment>A configuration file already exists.</comment> Use the `--force` option to overwrite yours.');

            return 1;
        }

        \file_put_contents('shift-cli.json', \json_encode(Configuration::defaults(), JSON_PRETTY_PRINT));

        return 0;
    }
}
