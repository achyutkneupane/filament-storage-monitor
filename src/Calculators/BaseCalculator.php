<?php

declare(strict_types=1);

namespace AchyutN\FilamentStorageMonitor\Calculators;

use AchyutN\FilamentStorageMonitor\Contracts\StorageCalculator;
use Illuminate\Support\Number;

abstract class BaseCalculator implements StorageCalculator
{
    final public function getUsedSpace(): float
    {
        return max(0, $this->getTotalSpace() - $this->getFreeSpace());
    }

    final public function getUsagePercentage(): float
    {
        $total = $this->getTotalSpace();

        $percentage = 0;

        if ($total > 0) {
            $percentage = ($this->getUsedSpace() / $total) * 100;
        }

        return round($percentage, 2);
    }

    /**
     * Helper to format bytes into human-readable labels.
     */
    final public function format(float $bytes): string
    {
        return Number::fileSize($bytes, precision: 2);
    }
}
