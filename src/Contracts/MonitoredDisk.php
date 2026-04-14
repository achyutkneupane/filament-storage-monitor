<?php

declare(strict_types=1);

namespace AchyutN\FilamentStorageMonitor\Contracts;

interface MonitoredDisk
{
    public function getPath(): string;

    public function getLabel(): ?string;

    public function getCalculator(): StorageCalculator;
}
