<?php

namespace App\Commands;

use App\Services\S3Executor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends AbstractCommand
{
    use FileFormat;

    protected static $defaultName = 'list';

    protected function configure(): void
    {
        $this
            ->setDescription('List backups on S3')
            ->setHelp('This command allows you to list all backups stored in S3 bucket.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $command = S3Executor::make()->list();

        $result = Command::SUCCESS;
        $lines = [];

        exec($command, $lines, $result);

        $backups = $this->parse($lines);

        $output->writeln('Backups:');

        foreach ($backups as $backup) {
            $output->writeln("[id: {$backup['id']}] {$backup['file']} ({$backup['size']} bytes) (created: {$backup['dateTime']})");
        }

        return $result;
    }
}
