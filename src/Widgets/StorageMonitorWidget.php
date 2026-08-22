<?php

declare(strict_types=1);

namespace AchyutN\FilamentStorageMonitor\Widgets;

use AchyutN\FilamentStorageMonitor\DTO\Directory;
use AchyutN\FilamentStorageMonitor\DTO\Disk;
use AchyutN\FilamentStorageMonitor\DTO\MonitoredItem;
use AchyutN\FilamentStorageMonitor\FilamentStorageMonitor;
use AchyutN\FilamentStorageMonitor\Support\DiskPresenter;
use Filament\Panel;
use Filament\Widgets\Widget;

final class StorageMonitorWidget extends Widget
{
    // @phpstan-ignore-next-line
    protected string $view = 'filament-storage-monitor::widgets.storage-monitor';

    public static function canView(): bool
    {
        $plugin = self::getPlugin();

        $hasMonitoredItems = $plugin->getDisks()
            ->concat($plugin->getDirectories())
            ->filter(fn (MonitoredItem $item): bool => $item->isVisible())
            ->isNotEmpty();
        $isVisible = $plugin->isVisible();

        return $isVisible && $hasMonitoredItems;
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
        $presenter = new DiskPresenter();
        $truncatePath = $plugin->isTruncatingPath();
        $isStrict = $plugin->isStrict();

        $disks = $plugin->getDisks()
            ->filter(fn (Disk $disk): bool => $disk->isVisible())
            ->map(fn (Disk $disk): array => $presenter->present($disk, $truncatePath, $isStrict));

        $directories = $plugin->getDirectories()
            ->filter(fn (Directory $directory): bool => $directory->isVisible())
            ->map(fn (Directory $directory): array => $presenter->present($directory, $truncatePath, $isStrict));

        return [
            'isCompact' => $plugin->isCompact(),
            'truncatePath' => $truncatePath,
            'disks' => $disks->concat($directories),
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
