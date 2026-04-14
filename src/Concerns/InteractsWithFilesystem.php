<?php

declare(strict_types=1);

namespace AchyutN\FilamentStorageMonitor\Concerns;

trait InteractsWithFilesystem
{
    /**
     * Internal wrapper for native disk_total_space.
     */
    protected function getDiskTotalSpace(string $path): float
    {
        return @disk_total_space($path) ?: 0.0;
    }

    /**
     * Internal wrapper for native disk_free_space.
     */
    protected function getDiskFreeSpace(string $path): float
    {
        return @disk_free_space($path) ?: 0.0;
    }
}
