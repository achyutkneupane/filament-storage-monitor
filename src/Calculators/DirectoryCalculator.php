<?php

declare(strict_types=1);

namespace AchyutN\FilamentStorageMonitor\Calculators;

use AchyutN\FilamentStorageMonitor\Concerns\InteractsWithFilesystem;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;

final class DirectoryCalculator extends BaseCalculator
{
    use InteractsWithFilesystem;

    public function __construct(private readonly string $path)
    {
        if (! is_dir($this->path)) {
            throw new DirectoryNotFoundException(__('filament-storage-monitor::plugin.errors.invalid_path', ['path' => $this->path]));
        }
    }

    public function getTotalSpace(): float
    {
        return $this->getDiskTotalSpace($this->path);
    }

    public function getFreeSpace(): float
    {
        return $this->getDiskFreeSpace($this->path);
    }

    public function getUsedSpace(): float
    {
        $size = 0.0;
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->path, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY,
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && ! $file->isLink()) {
                $size += $file->getSize();
            }
        }

        return $size;
    }
}