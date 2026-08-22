<?php

declare(strict_types=1);

namespace AchyutN\FilamentStorageMonitor\Support;

use AchyutN\FilamentStorageMonitor\DTO\Disk;
use Filament\Support\Colors\Color;
use Throwable;

final class DiskPresenter
{
    /** @return array<string, mixed> */
    public function present(Disk $disk, bool $truncatePath, bool $isStrict): array
    {
        $path = $disk->getPath();
        $pathParts = $truncatePath
            ? Path::abbreviate($path)
            : ['start' => $path, 'end' => ''];

        if (! $disk->hasError()) {
            try {
                $calculator = $disk->getCalculator();
                $percentage = round($calculator->getUsagePercentage(), 1);

                return [
                    'label' => $disk->getLabel(),
                    'icon' => $disk->getIcon(),
                    'color' => $disk->getColor() ?? 'primary',
                    'progressColor' => $this->progressColor($percentage),
                    'path' => $path,
                    'pathStart' => $pathParts['start'],
                    'pathEnd' => $pathParts['end'],
                    'total' => $calculator->format($calculator->getTotalSpace()),
                    'used' => $calculator->format($calculator->getUsedSpace()),
                    'free' => $calculator->format($calculator->getFreeSpace()),
                    'percentage' => $percentage,
                ];
            } catch (Throwable $e) {
                if ($isStrict) {
                    throw $e;
                }

                $disk->error($e->getMessage());
            }
        }

        return [
            'label' => $disk->getLabel(),
            'icon' => $disk->getIcon(),
            'path' => $path,
            'pathStart' => $pathParts['start'],
            'pathEnd' => $pathParts['end'],
            'error' => $disk->getError(),
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
