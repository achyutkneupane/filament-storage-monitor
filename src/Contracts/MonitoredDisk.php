<?php

declare(strict_types=1);

namespace AchyutN\FilamentStorageMonitor\Contracts;

use BackedEnum;
use Illuminate\Contracts\Support\Htmlable;

interface MonitoredDisk
{
    public function getPath(): string;

    public function getLabel(): string|Htmlable|null;

    public function getCalculator(): StorageCalculator;

    /**
     * @return string | array<string> | null
     */
    public function getColor(): string|array|null;

    public function getIcon(): string|BackedEnum|Htmlable|null;
}
