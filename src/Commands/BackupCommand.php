<?php

namespace App\Commands;

use App\Services\GZipExecutor;
use App\Services\MySQLExecutor;
use App\Services\Pipeline;
use App\Services\S3Executor;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BackupCommand extends AbstractCommand
{
    use FileFormat;

    protected static $defaultName = 'backup';

    protected function configure(): void
    {
        $this
            ->setDescription('Creates a new backup of database.')
            ->setHelp('This command allows you to create a new backup of the MySQL database and upload it to S3 bucket.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $now = new \DateTimeImmutable();
        $id = $now->format('Y-m-d');
        $file = $this->renderFormat($id);

        $command = Pipeline::make([
            MySQLExecutor::make()->backup(),
            GZipExecutor::make()->compress(),
            S3Executor::make()->upload($file),
        ]);

        $this->log($output, "Making backup {$file}");

        $output->write(exec($command, $out, $result));

        return $result;
    }
}
