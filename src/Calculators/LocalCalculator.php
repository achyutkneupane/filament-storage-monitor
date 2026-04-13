<?php

declare(strict_types=1);

namespace AchyutN\FilamentStorageMonitor\Calculators;

final class LocalCalculator extends BaseCalculator
{
    public function __construct(protected string $path) {}

    public function getTotalSpace(): float
    {
        return @disk_total_space($this->path) ?: 0;
    }

    public function getFreeSpace(): float
    {
        return @disk_free_space($this->path) ?: 0;
    }
}
