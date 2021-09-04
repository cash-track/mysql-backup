<?php

namespace App\Commands;

use App\Services\S3Executor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ClearCommand extends Command
{
    use FileFormat;

    protected static $defaultName = 'clear';

    protected function configure(): void
    {
        $this
            ->setDescription('Remove old backups on S3')
            ->setHelp('This command allows you to clear old backups stored in S3 bucket.')
            ->addOption('days', 'd', InputOption::VALUE_REQUIRED, 'The retention days of backups date');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $executor = S3Executor::make();

        $days = (int) $input->getOption('days');

        if ($days === 0) {
            $output->writeln('Nothing to clear as days is set to zero.');
            return Command::SUCCESS;
        }

        $today = new \DateTimeImmutable();
        $retentionDate = $today->sub(new \DateInterval("P{$days}D"));
        $retentionTimeStamp = $retentionDate->getTimestamp();

        foreach ($this->getBackups() as $backup) {
            $backupDateTime = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $backup['dateTime']);

            if (! $backupDateTime instanceof \DateTimeImmutable) {
                continue;
            }

            if ($retentionTimeStamp < $backupDateTime->getTimestamp()) {
                continue;
            }

            $output->writeln("Removing {$backup['file']}");

            exec($executor->remove($backup['file']));
        }

        return Command::SUCCESS;
    }
}
