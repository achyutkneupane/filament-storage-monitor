<?php

declare(strict_types=1);

namespace AchyutN\FilamentStorageMonitor;

use AchyutN\FilamentStorageMonitor\Contracts\StorageCalculator;
use AchyutN\FilamentStorageMonitor\DTO\Disk;
use AchyutN\FilamentStorageMonitor\Widgets\StorageMonitorWidget;
use BackedEnum;
use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;

final readonly class FilamentStorageMonitor implements Plugin
{
    /** @var Collection<int, Disk> */
    private Collection $disks;

    public function __construct()
    {
        $this->disks = collect();
    }

    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'filament-storage-monitor';
    }

    public function add(Disk $disk): self
    {
        $this->disks->push($disk);

        return $this;
    }

    /**
     * @param  string|array<string>|Closure|null  $color
     */
    public function addDisk(
        string $path,
        string|Closure|null $label,
        string|array|Closure|null $color = null,
        string|BackedEnum|Htmlable|Closure|null $icon = null,
        ?StorageCalculator $calculator = null,
    ): self {
        $newDiskId = $this->disks->count() + 1;

        $disk = Disk::make('disk-'.$newDiskId)
            ->path($path)
            ->label($label)
            ->color($color)
            ->icon($icon)
            ->calculator($calculator);

        return $this->add($disk);
    }

    /** @return Collection<int, Disk> */
    public function getDisks(): Collection
    {
        return $this->disks;
    }

    public function register(Panel $panel): void
    {
        if ($this->disks->isNotEmpty()) {
            $panel->widgets([
                StorageMonitorWidget::class,
            ]);
        }
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
