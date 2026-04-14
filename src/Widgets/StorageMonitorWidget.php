<?php

declare(strict_types=1);

namespace AchyutN\FilamentStorageMonitor\Widgets;

use Filament\Widgets\Widget;
use AchyutN\FilamentStorageMonitor\FilamentStorageMonitor;

final class StorageMonitorWidget extends Widget
{
    protected string $view = 'filament-storage-monitor::widgets.storage-monitor';

    protected static bool $isLazy = false;

    protected function getViewData(): array
    {
        /** @var FilamentStorageMonitor $plugin */
        $plugin = filament('filament-storage-monitor');

        return [
            'disks' => $plugin->getDisks()->map(fn ($disk) => [
                'label' => $disk->getLabel(),
                'icon' => $disk->getIcon(),
                'color' => $disk->getColor() ?? 'gray',
                'path' => $disk->getPath(),
                'total' => $disk->getCalculator()->format($disk->getCalculator()->getTotalSpace()),
                'used' => $disk->getCalculator()->format($disk->getCalculator()->getUsedSpace()),
                'percentage' => round($disk->getCalculator()->getUsagePercentage(), 1),
            ]),
        ];
    }
}
