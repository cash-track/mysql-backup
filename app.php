<?php

require __DIR__.'/vendor/autoload.php';

$application = new Symfony\Component\Console\Application();

$application->add(new App\Commands\ListCommand());
$application->add(new App\Commands\BackupCommand());
$application->add(new App\Commands\RestoreCommand());
$application->add(new App\Commands\ClearCommand());

$application->run();
