<?php

declare(strict_types=1);

namespace AchyutN\FilamentStorageMonitor;

use AchyutN\FilamentStorageMonitor\Concerns\CanBeHidden;
use AchyutN\FilamentStorageMonitor\Contracts\StorageCalculator;
use AchyutN\FilamentStorageMonitor\DTO\Disk;
use AchyutN\FilamentStorageMonitor\Widgets\StorageMonitorWidget;
use BackedEnum;
use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use InvalidArgumentException;

final class FilamentStorageMonitor implements Plugin
{
    use CanBeHidden;

    /** @var Collection<int, Disk> */
    private Collection $disks;

    public function __construct()
    {
        $this->disks = new Collection();
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
        bool|Closure $isVisible = true,
        ?StorageCalculator $calculator = null,
    ): self {
        $newDiskId = $this->disks->count() + 1;

        $disk = Disk::make('disk-'.$newDiskId)
            ->visible($isVisible)
            ->path($path)
            ->label($label)
            ->color($color)
            ->icon($icon)
            ->calculator($calculator);

        return $this->add($disk);
    }

    /**
     * @param  string|array<string>|Closure|null  $color
     */
    public function laravelDisk(
        string $name,
        string|Closure|null $label = null,
        string|array|Closure|null $color = null,
        string|BackedEnum|Htmlable|Closure|null $icon = null,
        bool|Closure $isVisible = true,
    ): self {
        /** @var array{root: string|null} $config */
        $config = config("filesystems.disks.{$name}");

        if ($config === null) {
            throw new InvalidArgumentException("The specified Laravel disk [{$name}] does not exist in the configuration.");
        }

        $path = $config['root'];

        if ($path === null) {
            throw new InvalidArgumentException("The specified Laravel disk [{$name}] does not have a 'root' configuration.");
        }

        return $this->add(
            Disk::make($name)
                ->visible($isVisible)
                ->label($label ?? str($name)->title()->toString())
                ->path($path)
                ->color($color)
                ->icon($icon)
        );
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
