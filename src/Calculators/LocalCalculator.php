<?php

declare(strict_types=1);

namespace AchyutN\FilamentStorageMonitor\Calculators;

use AchyutN\FilamentStorageMonitor\Concerns\InteractsWithFilesystem;

final class LocalCalculator extends BaseCalculator
{
    use InteractsWithFilesystem;

    public function __construct(private readonly string $path) {}

    public function getTotalSpace(): float
    {
        return $this->getDiskTotalSpace($this->path);
    }

    public function getFreeSpace(): float
    {
        return $this->getDiskFreeSpace($this->path);
    }
}
