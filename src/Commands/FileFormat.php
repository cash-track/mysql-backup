<?php

namespace App\Commands;

use App\Services\S3Executor;
use Symfony\Component\Console\Command\Command;

trait FileFormat
{
    protected $format = 'backup-{id}.sql.gz';

    public function setFormat(string $format)
    {
        $this->format = $format;
    }

    public function renderFormat(string $id = ''): string
    {
        return str_replace('{id}', $id, $this->format);
    }

    public function isExists(string $id): bool
    {
        foreach ($this->getBackups() as $backup) {
            if ($id === ($backup['id'] ?? null)) {
                return true;
            }
        }

        return false;
    }

    protected function getBackups(): array
    {
        $command = S3Executor::make()->list();

        $result = Command::SUCCESS;
        $lines = [];

        exec($command, $lines, $result);

        return $this->parse($lines);
    }

    protected function parse(array $lines): array
    {
        $backups = [];

        foreach ($lines as $line) {
            $date = $this->parseBackupDate($line);
            $size = $this->parseBackupSize($line);
            $time = $this->parseBackupTime($line, $date, $size);

            if ($date === null || $size === null) {
                continue;
            }

            $backups[] = [
                'id' => $date,
                'file' => $this->renderFormat($date),
                'size' => $size,
                'dateTime' => "{$date} {$time}",
            ];
        }

        return $backups;
    }

    protected function parseBackupDate(string $line):? string
    {
        $match = [];

        $format = str_replace('{preg-id}', '(.+)', str_replace('.', '\.', $this->renderFormat('{preg-id}')));

        preg_match("/.+{$format}/", $line, $match, PREG_OFFSET_CAPTURE);

        return $match[1][0] ?? null;
    }

    protected function parseBackupSize(string $line):? string
    {
        $match = [];

        preg_match('/.+\s(\d+)\sbackup.+/', $line, $match, PREG_OFFSET_CAPTURE);

        return $match[1][0] ?? null;
    }

    protected function parseBackupTime(string $line, string $date, string $size): string
    {
        return trim(str_replace([$date, $size, $this->renderFormat()], '', $line));
    }
}
