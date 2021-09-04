<?php

namespace App\Services;

class S3Executor extends AbstractExecutor
{
    protected $endpoint;

    protected $bucket;

    public function __construct()
    {
        $this->bucket = (string) getenv('S3_BUCKET');
        $this->endpoint = (string) getenv('S3_ENDPOINT');

        if (empty($this->endpoint)) {
            $this->endpoint = null;
        }
    }

    public function list(string $path = ''): string
    {
        return $this->command($path, 'ls --recursive');
    }

    public function upload(string $path): string
    {
        return $this->command($path, 'cp -');
    }

    public function download(string $path): string
    {
        return $this->command("{$path} -", 'cp');
    }

    public function remove(string $path): string
    {
        return $this->command("{$path}", 'rm');
    }

    protected function command(string $path = '', string $arguments = ''): string
    {
        $endpoint = '';

        if (!empty($this->endpoint)) {
            $endpoint = "--endpoint={$this->endpoint}";
        }

        return "aws s3 {$arguments} {$endpoint} s3://{$this->bucket}/{$path}";
    }
}