---
name: filament-storage-monitor
description: Install and configure a Filament dashboard widget to monitor server disk usage.
tags:
  - filament
  - widget
  - plugin
  - laravel
  - monitoring
  - storage
  - livewire
---

# filament-storage-monitor

## Context

You are helping a Laravel/Filament app install and configure the `achyutn/filament-storage-monitor` package.

It auto-registers a Filament dashboard widget (once disks or directories are configured) that displays disk usage (total/used/%). Disks are partition-based, computed with native PHP filesystem calls. Directories report their own recursively-computed size. Results are cached by default for 300 seconds.

## Rules

- Install via Composer: `composer require achyutn/filament-storage-monitor`.
- Configure via the Filament panel plugin API (there is no config file).
- The widget only appears after you configure at least one disk.
- Prefer `laravelDisk('public')`/`laravelDisk('local')` for local disks that have `filesystems.disks.<name>.root`.
- Avoid `laravelDisk('s3')` (and similar) unless the disk has a local `root`; otherwise the plugin will surface an error or throw in strict mode.
- Use `visible()` / per-disk `isVisible` closures to restrict server storage info to authorized users.
- Use `throwException()` only when you want missing/invalid disks to fail loudly (useful in local/dev).
- Prefer `addDirectory(Directory::make(...))` when you need the size of a specific folder rather than the partition it sits on.
- Keep `cacheResults()` enabled (default) to avoid repeated directory scans; raise the TTL for very large trees.
- Remember the disk limitation: two different paths on the same partition show the same total/free.

## Gotchas

- `laravelDisk($name)` resolves `config("filesystems.disks.$name.root")`; if it’s missing the widget will show an error row (or throw in strict mode).
- `addDirectory()` accepts a `Directory` DTO; directory rows show used + total and omit free space (a partition concept that is identical across folders).
- Directory size calculation walks the filesystem, so it is cached via `cacheResults()` (default `true`, 300s TTL); cached values may be up to the TTL stale.
- To override how space is computed (non-local disks, custom logic), provide a custom calculator via `Disk::calculator(...)`.
- Views can be published/overridden via the package publish tag `filament-storage-monitor-views`.

## Examples

### Consumer usage: register the plugin in a Filament panel

```php
use AchyutN\FilamentStorageMonitor\FilamentStorageMonitor;

return $panel
    ->plugins([
        FilamentStorageMonitor::make()
            ->addDisk('/mnt/data', label: 'Data Partition')
            ->laravelDisk(name: 'public', label: 'Media Storage'),
    ]);
```

### Add a disk with custom label/icon/color and visibility

```php
use AchyutN\FilamentStorageMonitor\DTO\Disk;
use AchyutN\FilamentStorageMonitor\FilamentStorageMonitor;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;

FilamentStorageMonitor::make()
    ->add(
        Disk::make('web-root')
            ->path('/var/www/html')
            ->label('Web Root')
            ->color(Color::Green)
            ->icon(Heroicon::ComputerDesktop)
            ->visible(fn () => auth()->user()?->isAdmin() ?? false),
    )
    ->compact();
```

### Strict mode in local/dev only

```php
use AchyutN\FilamentStorageMonitor\FilamentStorageMonitor;

FilamentStorageMonitor::make()
    ->throwException(fn () => app()->isLocal())
    ->laravelDisk(name: 'public', label: 'Public Disk');
```

### Publish the widget views to customize markup

```bash
php artisan vendor:publish --tag=filament-storage-monitor-views
```

### Monitor a specific directory size

```php
use AchyutN\FilamentStorageMonitor\DTO\Directory;

FilamentStorageMonitor::make()
    ->addDirectory(
        Directory::make('uploads')
            ->path('/var/www/storage')
            ->label('Uploads'),
    );
```

### Adjust result caching

```php
FilamentStorageMonitor::make()
    ->cacheResults(ttl: 60); // cache for 60 seconds instead of the default 300

// or disable entirely
FilamentStorageMonitor::make()
    ->cacheResults(cache: false);
```

### Extend: provide a custom StorageCalculator

```php
use AchyutN\FilamentStorageMonitor\Contracts\StorageCalculator;

final class MyCalculator implements StorageCalculator
{
    public function getTotalSpace(): float { /* ... */ }
    public function getFreeSpace(): float { /* ... */ }
    public function getUsedSpace(): float { return max(0, $this->getTotalSpace() - $this->getFreeSpace()); }
    public function getUsagePercentage(): float { /* ... */ }
    public function format(float $bytes): string { /* ... */ }
}
```

Then attach it:

```php
use AchyutN\FilamentStorageMonitor\DTO\Disk;

Disk::make('custom')->path('/')->calculator(new MyCalculator());
```

## Anti-Patterns

- Don’t expect directory-specific sizes from `addDisk()`/`laravelDisk()`: disks are partition-based.
- Don’t register multiple paths on the same partition expecting different totals.
- Don’t use `addDirectory()` for partition-level metrics (it reports the folder’s own used size against the partition total).
- Don’t expect a per-directory “free” value: free space is a partition concept.
- Don’t call `laravelDisk()` for non-local disks that don’t have a `root` path.

## References

- Repo README: `README.md`
