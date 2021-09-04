<?php

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand extends Command
{
    protected function log(OutputInterface $output, string $message = '')
    {
        $time = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

        $output->writeln("[{$time}] {$message}");
    }
}
