<?php

declare(strict_types=1);

namespace AchyutN\FilamentStorageMonitor\Widgets;

use AchyutN\FilamentStorageMonitor\DTO\Disk;
use AchyutN\FilamentStorageMonitor\FilamentStorageMonitor;
use Filament\Support\Colors\Color;
use Filament\Widgets\Widget;

final class StorageMonitorWidget extends Widget
{
    // @phpstan-ignore-next-line
    protected string $view = 'filament-storage-monitor::widgets.storage-monitor';

    public static function canView(): bool
    {
        /** @var FilamentStorageMonitor $plugin */
        $plugin = filament('filament-storage-monitor');

        $isEmpty = $plugin->getDisks()->filter(fn (Disk $disk): bool => $disk->isVisible())->isEmpty();
        $isVisible = $plugin->isVisible();

        return $isVisible && ! $isEmpty;
    }

    protected function getViewData(): array
    {
        /** @var FilamentStorageMonitor $plugin */
        $plugin = filament('filament-storage-monitor');

        return [
            'disks' => $plugin->getDisks()
                ->filter(fn (Disk $disk): bool => $disk->isVisible())
                ->map(function (Disk $disk): array {
                    $percentage = round($disk->getCalculator()->getUsagePercentage(), 1);

                    return [
                        'label' => $disk->getLabel(),
                        'icon' => $disk->getIcon(),
                        'color' => $disk->getColor() ?? 'primary',
                        'progressColor' => match (true) {
                            $percentage > 90 => Color::Red,
                            $percentage > 70 => Color::Yellow,
                            default => Color::Green,
                        },
                        'path' => $disk->getPath(),
                        'total' => $disk->getCalculator()->format($disk->getCalculator()->getTotalSpace()),
                        'used' => $disk->getCalculator()->format($disk->getCalculator()->getUsedSpace()),
                        'free' => $disk->getCalculator()->format($disk->getCalculator()->getFreeSpace()),
                        'percentage' => $percentage,
                    ];
                }),
        ];
    }
}
