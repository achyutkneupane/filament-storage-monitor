<?php

declare(strict_types=1);

namespace AchyutN\FilamentStorageMonitor\Contracts;

use Illuminate\Contracts\Support\Htmlable;

interface MonitoredDisk
{
    public function getPath(): string;

    public function getLabel(): string|Htmlable|null;

    public function getCalculator(): StorageCalculator;
}
