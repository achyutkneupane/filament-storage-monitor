<?php

declare(strict_types=1);

namespace AchyutN\FilamentStorageMonitor\Contracts;

interface StorageCalculator
{
    /**
     * Get the total storage space in bytes.
     */
    public function getTotalSpace(): float;

    /**
     * Get the free storage space in bytes.
     */
    public function getFreeSpace(): float;

    /**
     * Get the used storage space in bytes.
     */
    public function getUsedSpace(): float;

    /**
     * Get the usage percentage (0-100).
     */
    public function getUsagePercentage(): float;

    /**
     * Format the given bytes into a human-readable string (e.g., "10 GB").
     */
    public function format(float $bytes): string;
}
