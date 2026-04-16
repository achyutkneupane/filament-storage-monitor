<?php

declare(strict_types=1);

namespace AchyutN\FilamentStorageMonitor\Widgets;

use AchyutN\FilamentStorageMonitor\DTO\Disk;
use AchyutN\FilamentStorageMonitor\FilamentStorageMonitor;
use Filament\Panel;
use Filament\Support\Colors\Color;
use Filament\Widgets\Widget;
use Throwable;

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

    public static function getSort(): int
    {
        return self::getPlugin()->getSort();
    }

    public static function isLazy(): bool
    {
        return self::getPlugin()->isLazy();
    }

    /** @return array<string, int|null>|int|string */
    public function getColumnSpan(): int|string|array
    {
        return self::getPlugin()->getColumnSpan();
    }

    /** @return array<string, int|null>|int|string */
    public function getColumnStart(): int|string|array
    {
        return self::getPlugin()->getColumnStart();
    }

    protected function getViewData(): array
    {
        $plugin = self::getPlugin();
        $isStrict = $plugin->isStrict();

        return [
            'disks' => $plugin->getDisks()
                ->filter(fn (Disk $disk): bool => $disk->isVisible())
                ->map(function (Disk $disk) use ($isStrict): array {
                    try {
                        $calculator = $disk->getCalculator();
                        $percentage = round($calculator->getUsagePercentage(), 1);

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
                            'total' => $calculator->format($calculator->getTotalSpace()),
                            'used' => $calculator->format($calculator->getUsedSpace()),
                            'free' => $calculator->format($calculator->getFreeSpace()),
                            'percentage' => $percentage,
                        ];
                    } catch (Throwable $e) {
                        if ($isStrict) {
                            throw $e;
                        }

                        return [
                            'label' => $disk->getLabel(),
                            'icon' => $disk->getIcon(),
                            'color' => $disk->getColor() ?? 'primary',
                            'progressColor' => Color::Red,
                            'path' => $disk->getPath(),
                            'error' => $e->getMessage(),
                        ];
                    }
                }),
        ];
    }

    private static function getPlugin(?Panel $panel = null): FilamentStorageMonitor
    {
        $panel ??= filament()->getCurrentPanel();
        $storageMonitor = FilamentStorageMonitor::make();

        if ($panel?->hasPlugin($storageMonitor->getId())) {
            /** @var FilamentStorageMonitor */
            return $panel->getPlugin($storageMonitor->getId());
        }

        return $storageMonitor;
    }
}
