<?php

declare(strict_types=1);

namespace AchyutN\FilamentStorageMonitor\Support;

use AchyutN\FilamentStorageMonitor\DTO\Directory;
use AchyutN\FilamentStorageMonitor\DTO\Disk;
use Filament\Support\Colors\Color;
use Throwable;

final class DiskPresenter
{
    /** @return array<string, mixed> */
    public function present(Disk|Directory $item, bool $truncatePath, bool $isStrict): array
    {
        $path = $item->getPath();
        $isDirectory = $item instanceof Directory;
        $pathParts = $truncatePath
            ? Path::abbreviate($path)
            : ['start' => $path, 'end' => ''];

        if (! $item->hasError()) {
            try {
                $calculator = $item->getCalculator();
                $percentage = round($calculator->getUsagePercentage(), 1);

                $data = [
                    'label' => $item->getLabel(),
                    'icon' => $item->getIcon(),
                    'color' => $item->getColor() ?? 'primary',
                    'progressColor' => $this->progressColor($percentage),
                    'path' => $path,
                    'pathStart' => $pathParts['start'],
                    'pathEnd' => $pathParts['end'],
                    'total' => $calculator->format($calculator->getTotalSpace()),
                    'used' => $calculator->format($calculator->getUsedSpace()),
                    'percentage' => $percentage,
                    'directory' => $isDirectory,
                ];

                if (! $isDirectory) {
                    $data['free'] = $calculator->format($calculator->getFreeSpace());
                }

                return $data;
            } catch (Throwable $e) {
                if ($isStrict) {
                    throw $e;
                }

                $item->error($e->getMessage());
            }
        }

        return [
            'label' => $item->getLabel(),
            'icon' => $item->getIcon(),
            'path' => $path,
            'pathStart' => $pathParts['start'],
            'pathEnd' => $pathParts['end'],
            'error' => $item->getError(),
        ];
    }

    /** @return array<int, string> */
    private function progressColor(float $percentage): array
    {
        return match (true) {
            $percentage > 90 => Color::Red,
            $percentage > 70 => Color::Yellow,
            default => Color::Green,
        };
    }
}
