<?php

declare(strict_types=1);

namespace AchyutN\FilamentStorageMonitor\Widgets;

use AchyutN\FilamentStorageMonitor\DTO\Disk;
use AchyutN\FilamentStorageMonitor\FilamentStorageMonitor;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Colors\Color;
use Filament\Widgets\Widget;

final class StorageMonitorWidget extends Widget
{
    // @phpstan-ignore-next-line
    protected string $view = 'filament-storage-monitor::widgets.storage-monitor';

    public static function canView(): bool
    {
        $plugin = self::getPlugin();

        $isEmpty = $plugin->getDisks()->filter(fn (Disk $disk): bool => $disk->isVisible())->isEmpty();
        $isVisible = $plugin->isVisible();

        return $isVisible && ! $isEmpty;
    }

    protected static function getPlugin(?Panel $panel = null): FilamentStorageMonitor
    {
        $panel ??= filament()->getCurrentPanel();
        $storageMonitor = FilamentStorageMonitor::make();

        if ($panel?->hasPlugin($storageMonitor->getId())) {
            /** @var FilamentStorageMonitor */
            return $panel->getPlugin($storageMonitor->getId());
        }

        return $storageMonitor;
    }

    protected function getViewData(): array
    {
        $plugin = self::getPlugin();

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
