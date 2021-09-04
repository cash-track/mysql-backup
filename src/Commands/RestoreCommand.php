<?php

namespace App\Commands;

use App\Services\GZipExecutor;
use App\Services\MySQLExecutor;
use App\Services\Pipeline;
use App\Services\S3Executor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RestoreCommand extends AbstractCommand
{
    use FileFormat;

    protected static $defaultName = 'restore';

    protected function configure(): void
    {
        $this
            ->setDescription('Restore backup of database.')
            ->setHelp('This command allows you to take backup from S3 bucket and put it into a database.')
            ->addArgument('id', InputArgument::REQUIRED, 'Identification of an backup to restore, ex: 2021-09-30');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $id = $input->getArgument('id');
        $file = $this->renderFormat($id);

        if (! $this->isExists($id)) {
            $this->log($output, "Backup file {$file} does not exists on S3 bucket.");
            return Command::SUCCESS;
        }

        $this->log($output, 'Preparing database..');

        $output->write(exec(MySQLExecutor::make()->init(), $out, $result));

        $this->log($output, 'Done.');

        $command = Pipeline::make([
            S3Executor::make()->download($this->renderFormat($id)),
            GZipExecutor::make()->decompress(),
            MySQLExecutor::make()->restore(),
        ]);

        $this->log($output, "Restoring backup {$file}...");

        $output->write(exec($command, $out, $result));

        $this->log($output, 'Done.');

        return $result;
    }
}
