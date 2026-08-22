<?php

declare(strict_types=1);

namespace AchyutN\FilamentStorageMonitor\DTO;

use AchyutN\FilamentStorageMonitor\Calculators\LocalCalculator;
use AchyutN\FilamentStorageMonitor\Contracts\StorageCalculator;

final class Disk extends MonitoredItem
{
    public function getCalculator(): StorageCalculator
    {
        return $this->calculator ?? new LocalCalculator($this->path);
    }
}