<?php

namespace App\Services;

class GZipExecutor extends AbstractExecutor
{
    public function compress(): string
    {
        return 'gzip -9 -c';
    }

    public function decompress(): string
    {
        return 'gzip -dc';
    }
}
