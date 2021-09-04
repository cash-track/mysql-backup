<?php

namespace App\Services;

class MySQLExecutor extends AbstractExecutor
{
    protected $host;

    protected $port;

    protected $user;

    protected $password;

    protected $database;

    public function __construct()
    {
        $this->host = (string) getenv('MYSQL_HOST');
        $this->port = (string) getenv('MYSQL_PORT');
        $this->user = (string) getenv('MYSQL_USER');
        $this->password = (string) getenv('MYSQL_PASSWORD');
        $this->database = (string) getenv('MYSQL_DATABASE');
    }

    public function backup(): string
    {
        return "mysqldump -h {$this->host} -P {$this->port} -u {$this->user} -p\"{$this->password}\" {$this->database}";
    }

    public function init(): string
    {
        return "echo \"DROP DATABASE IF EXISTS {$this->database}; CREATE DATABASE {$this->database}; GRANT ALL PRIVILEGES ON *.* TO '{$this->user}'@'%' IDENTIFIED BY '{$this->password}' WITH GRANT OPTION; FLUSH PRIVILEGES;\"" . $this->exec();
    }

    public function restore(): string
    {
        return $this->exec($this->database);
    }

    protected function exec(string $database = ''): string
    {
        return "mysql -h {$this->host} -P {$this->port} --user={$this->user} --password=\"{$this->password}\" {$database}";
    }
}
