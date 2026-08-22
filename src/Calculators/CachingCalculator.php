<?php

declare(strict_types=1);

namespace AchyutN\FilamentStorageMonitor\Calculators;

use AchyutN\FilamentStorageMonitor\Contracts\StorageCalculator;
use Closure;
use Illuminate\Support\Facades\Cache;

final readonly class CachingCalculator implements StorageCalculator
{
    public function __construct(
        private StorageCalculator $calculator,
        private string $cacheKey,
        private int $ttl,
    ) {}

    public function getTotalSpace(): float
    {
        return $this->remember('total', fn (): float => $this->calculator->getTotalSpace());
    }

    public function getFreeSpace(): float
    {
        return $this->remember('free', fn (): float => $this->calculator->getFreeSpace());
    }

    public function getUsedSpace(): float
    {
        return $this->remember('used', fn (): float => $this->calculator->getUsedSpace());
    }

    public function getUsagePercentage(): float
    {
        $total = $this->getTotalSpace();
        $used = $this->getUsedSpace();

        return $total > 0 ? round(($used / $total) * 100, 2) : 0.0;
    }

    public function format(float $bytes): string
    {
        return $this->calculator->format($bytes);
    }

    /**
     * @param  Closure(): float  $callback
     */
    private function remember(string $metric, Closure $callback): float
    {
        return Cache::remember("{$this->cacheKey}:{$metric}", $this->ttl, $callback);
    }
}
