<?php

declare(strict_types=1);

namespace AchyutN\FilamentStorageMonitor\DTO;

use AchyutN\FilamentStorageMonitor\Calculators\DirectoryCalculator;
use AchyutN\FilamentStorageMonitor\Contracts\StorageCalculator;

final class Directory extends MonitoredItem
{
    public function getCalculator(): StorageCalculator
    {
        return $this->calculator ?? new DirectoryCalculator($this->path);
    }
}