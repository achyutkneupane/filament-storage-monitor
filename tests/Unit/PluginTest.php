<?php

declare(strict_types=1);

use AchyutN\FilamentStorageMonitor\DTO\Disk;
use AchyutN\FilamentStorageMonitor\FilamentStorageMonitorPlugin;

test('plugin can store multiple disks', function () {
    $plugin = FilamentStorageMonitorPlugin::make()
        ->add(Disk::make('local')->label('Local')->path('/'))
        ->add(Disk::make('backup')->label('Backup Drive')->path('/mnt/backups'));

    expect($plugin->getDisks())->toHaveCount(2)
        ->and($plugin->getDisks()->first()->getLabel())->toBe('Local');
});

test('plugin has a unique identifier', function () {
    $plugin = FilamentStorageMonitorPlugin::make();

    expect($plugin->getId())->toBe('filament-storage-monitor');
});
