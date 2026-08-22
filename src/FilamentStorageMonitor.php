<?php

declare(strict_types=1);

namespace AchyutN\FilamentStorageMonitor;

use AchyutN\FilamentStorageMonitor\Calculators\CachingCalculator;
use AchyutN\FilamentStorageMonitor\Concerns\CachesResults;
use AchyutN\FilamentStorageMonitor\Concerns\CanBeHidden;
use AchyutN\FilamentStorageMonitor\Concerns\HasWidgetProperties;
use AchyutN\FilamentStorageMonitor\Concerns\IsCompact;
use AchyutN\FilamentStorageMonitor\Concerns\IsStrict;
use AchyutN\FilamentStorageMonitor\Concerns\TruncatesPath;
use AchyutN\FilamentStorageMonitor\Contracts\StorageCalculator;
use AchyutN\FilamentStorageMonitor\DTO\Directory;
use AchyutN\FilamentStorageMonitor\DTO\Disk;
use AchyutN\FilamentStorageMonitor\DTO\MonitoredItem;
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
    use CachesResults;
    use CanBeHidden;
    use HasWidgetProperties;
    use IsCompact;
    use IsStrict;
    use TruncatesPath;

    /** @var Collection<int, Disk> */
    private Collection $disks;

    /** @var Collection<int, Directory> */
    private Collection $directories;

    public function __construct()
    {
        $this->disks = new Collection();
        $this->directories = new Collection();
    }

    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'filament-storage-monitor';
    }

    public function add(Disk|Directory $item): self
    {
        $this->guardPath($item);
        $this->wrapWithCaching($item, $item instanceof Directory ? 'directory' : 'disk');

        if ($item instanceof Directory) {
            $this->directories->push($item);
        } else {
            $this->disks->push($item);
        }

        return $this;
    }

    /**
     * @param  string|array<string>|Closure|null  $color
     */
    public function addDirectory(
        Directory|string $path,
        string|Closure|null $label = null,
        string|array|Closure|null $color = null,
        string|BackedEnum|Htmlable|Closure|null $icon = null,
        bool|Closure $isVisible = true,
        ?StorageCalculator $calculator = null,
    ): self {
        $directory = $path instanceof Directory
            ? $path
            : Directory::make('directory-'.($this->directories->count() + 1))
                ->visible($isVisible)
                ->path($path)
                ->label($label)
                ->color($color)
                ->icon($icon)
                ->calculator($calculator);

        return $this->add($directory);
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
        /** @var array<string, mixed>|null $config */
        $config = config("filesystems.disks.{$name}");
        $isStrict = $this->isStrict();

        if ($config === null) {
            $error = __('filament-storage-monitor::plugin.errors.disk_not_found', ['name' => $name]);
            $this->abortIfStrict($isStrict, $error);

            return $this->add($this->makeDisk($name, $label, $color, $icon, $isVisible, path: $name, error: $error));
        }

        $root = $config['root'] ?? null;

        if (! is_string($root)) {
            $error = __('filament-storage-monitor::plugin.errors.root_not_found', ['name' => $name]);
            $this->abortIfStrict($isStrict, $error);

            return $this->add($this->makeDisk($name, $label, $color, $icon, $isVisible, path: $name, error: $error));
        }

        return $this->add($this->makeDisk($name, $label, $color, $icon, $isVisible, path: $root));
    }

    /** @return Collection<int, Disk> */
    public function getDisks(): Collection
    {
        return $this->disks;
    }

    /** @return Collection<int, Directory> */
    public function getDirectories(): Collection
    {
        return $this->directories;
    }

    public function register(Panel $panel): void
    {
        if ($this->disks->isNotEmpty() || $this->directories->isNotEmpty()) {
            $panel->widgets([
                StorageMonitorWidget::class,
            ]);
        }
    }

    public function boot(Panel $panel): void
    {
        //
    }

    /**
     * @param  string|array<string>|Closure|null  $color
     * @param  array<int, mixed>|string|null  $error
     */
    private function makeDisk(
        string $name,
        string|Closure|null $label,
        string|array|Closure|null $color,
        string|BackedEnum|Htmlable|Closure|null $icon,
        bool|Closure $isVisible,
        string $path,
        array|string|null $error = null,
    ): Disk {
        return Disk::make($name)
            ->visible($isVisible)
            ->label($label)
            ->color($color)
            ->icon($icon)
            ->path($path)
            ->error($error);
    }

    private function guardPath(MonitoredItem $item): void
    {
        $path = $item->getPath();

        if ($item->hasError() || is_dir($path)) {
            return;
        }

        $error = trim($path) === ''
            ? __('filament-storage-monitor::plugin.errors.path_required', ['name' => $item->getName()])
            : __('filament-storage-monitor::plugin.errors.invalid_path', ['path' => $path]);

        if ($this->isStrict()) {
            throw new DirectoryNotFoundException($error);
        }

        $item->error($error);
    }

    private function wrapWithCaching(MonitoredItem $item, string $kind): void
    {
        if ($item->hasError() || ! $this->shouldCacheResults()) {
            return;
        }

        $path = realpath($item->getPath()) ?: $item->getPath();
        $key = "filament-storage-monitor:sizes:{$kind}:{$path}";

        $item->calculator(new CachingCalculator($item->getCalculator(), $key, $this->getCacheTtl()));
    }

    private function abortIfStrict(bool $isStrict, string $error): void
    {
        if ($isStrict) {
            throw new InvalidArgumentException($error);
        }
    }
}
