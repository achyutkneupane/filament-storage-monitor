# Filament Storage Monitor

![Packagist Version](https://img.shields.io/packagist/v/achyutn/filament-storage-monitor?label=Latest%20Version)
![Packagist Downloads](https://img.shields.io/packagist/dt/achyutn/filament-storage-monitor?label=Packagist%20Downloads)
![Packagist Stars](https://img.shields.io/packagist/stars/achyutn/filament-storage-monitor?label=Stars)
[![Lint & Test PR](https://github.com/achyutkneupane/filament-storage-monitor/actions/workflows/prlint.yml/badge.svg)](https://github.com/achyutkneupane/filament-storage-monitor/actions/workflows/prlint.yml)

A strictly typed, highly expressive Filament plugin to monitor server storage. This package provides a clean, native-feeling dashboard widget that displays disk usage with support for multiple partitions, custom labeling, and dynamic health-based coloring.

![Screenshot of the Filament Storage Monitor widget](https://hamrocdn.com/5MjqZYhjK9zz)

## Requirements

- PHP: **8.2+**
- Filament: **4.x** or **5.x**

## Installation

You can install the package via Composer:

```bash
composer require achyutn/filament-storage-monitor
```

Register the plugin inside your Filament panel:

```php
use AchyutN\FilamentStorageMonitor\FilamentStorageMonitor;

return $panel
    ->plugins([
        FilamentStorageMonitor::make()
            ->addDisk('/mnt/data', label: 'Data Partition')
            ->laravelDisk(name: 'public', label: 'Media Storage'),
    ]);
```

## Usage

The plugin automatically registers a dashboard widget once disks are configured.

### Adding Disks

You can add disks manually by providing a path, or resolve them directly from your Laravel filesystem configuration.

#### Manual Registration

You can either use `addDisk()` for adding a disk through parameters or `add()` using the `Disk` DTO:

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
            ->icon(Heroicon::ComputerDesktop),
    )
    ->addDisk(
        path: '/mnt/backup',
        label: 'Backups',
        color: Color::Blue,
        icon: Heroicon::ArchiveBox,
    );
```

#### Laravel Disk Registration

You can also register disks directly from your Laravel filesystem configuration file `config/filesystems.php`:

```php
FilamentStorageMonitor::make()
    ->laravelDisk(name: 'local', label: 'Local Storage');
```

### Authorization & Visibility

You can control the visibility of the entire widget or individual disks using boolean values or closures. This is useful for restricting sensitive server information to administrators.

```php
FilamentStorageMonitor::make()
    ->visible(fn () => auth()->user()->is_admin) // Hide entire widget
    ->addDisk(
        path: '/var/www/html', 
        label: 'App Files',
        isVisible: fn () => auth()->user()->can('view_server_stats') // Hide specific disk
    );
```

> [!NOTE]
> This package currently monitors Disk Partitions using native PHP filesystem functions.
> If you add two different paths that reside on the same partition (e.g., `/var/www/html` and `/var/www/html/laravel-project`), they will display the same total/free space because they belong to the same filesystem boundary.
> 
> Directory-specific size calculation is planned for a future release.

## Localization

Filament Storage Monitor includes built-in translations for:

- [English](resources/lang/en/plugin.php)

Translations are applied automatically based on your application's current locale.

> Missing your language? Feel free to [submit a PR](https://github.com/achyutkneupane/filament-storage-monitor/pulls) to add it!

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).

## Contributing

Contributions are welcome! Please create a pull request or open an issue if you find any bugs or have feature requests.

## Support

If you find this package useful, please consider starring the repository on GitHub to show your support.
