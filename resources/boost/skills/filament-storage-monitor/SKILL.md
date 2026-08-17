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

It auto-registers a Filament dashboard widget (once disks are configured) that displays disk usage (total/used/free/%). Values are computed using native PHP filesystem calls, so they are partition-based.

## Rules

- Install via Composer: `composer require achyutn/filament-storage-monitor`.
- Configure via the Filament panel plugin API (there is no config file).
- The widget only appears after you configure at least one disk.
- Prefer `laravelDisk('public')`/`laravelDisk('local')` for local disks that have `filesystems.disks.<name>.root`.
- Avoid `laravelDisk('s3')` (and similar) unless the disk has a local `root`; otherwise the plugin will surface an error or throw in strict mode.
- Use `visible()` / per-disk `isVisible` closures to restrict server storage info to authorized users.
- Use `throwException()` only when you want missing/invalid disks to fail loudly (useful in local/dev).
- Remember the limitation: two different paths on the same partition show the same total/free.

## Gotchas

- `laravelDisk($name)` resolves `config("filesystems.disks.$name.root")`; if it’s missing the widget will show an error row (or throw in strict mode).
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

- Don’t expect directory-specific sizing: totals/free are partition-based.
- Don’t register multiple paths on the same partition expecting different totals.
- Don’t call `laravelDisk()` for non-local disks that don’t have a `root` path.

## References

- Repo README: `README.md`
