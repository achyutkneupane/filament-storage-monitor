<?php

declare(strict_types=1);

namespace AchyutN\FilamentStorageMonitor\Calculators;

use AchyutN\FilamentStorageMonitor\Concerns\InteractsWithFilesystem;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;

final class LocalCalculator extends BaseCalculator
{
    use InteractsWithFilesystem;

    public function __construct(private readonly string $path)
    {
        if (! is_dir($this->path)) {
            throw new DirectoryNotFoundException(__('filament-storage-monitor::plugin.errors.invalid_path', ['path' => $this->path]));
        }
    }

    public function getTotalSpace(): float
    {
        return $this->getDiskTotalSpace($this->path);
    }

    public function getFreeSpace(): float
    {
        return $this->getDiskFreeSpace($this->path);
    }
}
