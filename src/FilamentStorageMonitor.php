<?php

declare(strict_types=1);

namespace AchyutN\FilamentStorageMonitor;

use AchyutN\FilamentStorageMonitor\Concerns\CanBeHidden;
use AchyutN\FilamentStorageMonitor\Concerns\HasWidgetProperties;
use AchyutN\FilamentStorageMonitor\Concerns\IsStrict;
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
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;

final class FilamentStorageMonitor implements Plugin
{
    use CanBeHidden;
    use HasWidgetProperties;
    use IsStrict;

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
        $isStrict = $this->isStrict();
        $path = $disk->getPath();

        if (! $disk->hasError() && ! is_dir($path)) {
            $error = __('filament-storage-monitor::plugin.errors.invalid_path', ['path' => $path]);

            if ($isStrict) {
                throw new DirectoryNotFoundException($error);
            }

            $disk->error($error);
        }

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
        $isStrict = $this->isStrict();
        $error = null;

        if ($config === null) {
            $error = __('filament-storage-monitor::plugin.errors.disk_not_found', ['name' => $name]);
            if ($isStrict) {
                throw new InvalidArgumentException($error);
            }
        }

        $path = $config['root'] ?? null;

        if ($path === null && ! $error) {
            $error = __('filament-storage-monitor::plugin.errors.root_not_found', ['name' => $name]);
            if ($isStrict) {
                throw new InvalidArgumentException($error);
            }
        }

        return $this->add(
            Disk::make($name)
                ->visible($isVisible)
                ->label($label)
                ->path($path ?? $name)
                ->color($color)
                ->icon($icon)
                ->error($error)
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
