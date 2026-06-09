<?php

declare(strict_types=1);

use AchyutN\FilamentStorageMonitor\DTO\Disk;
use AchyutN\FilamentStorageMonitor\FilamentStorageMonitor;

test('plugin can store multiple disks', function () {
    $plugin = FilamentStorageMonitor::make()
        ->add(Disk::make('local')->label('Local')->path('/'))
        ->add(Disk::make('backup')->label('Backup Drive')->path('/mnt/backups'));

    expect($plugin->getDisks())->toHaveCount(2)
        ->and($plugin->getDisks()->first()->getLabel())->toBe('Local');
});

test('plugin has a unique identifier', function () {
    $plugin = FilamentStorageMonitor::make();

    expect($plugin->getId())->toBe('filament-storage-monitor');
});

test('path truncation is disabled by default', function () {
    expect(FilamentStorageMonitor::make()->isTruncatingPath())->toBeFalse();
});

test('truncatePath() toggles the flag', function () {
    expect(FilamentStorageMonitor::make()->truncatePath()->isTruncatingPath())->toBeTrue()
        ->and(FilamentStorageMonitor::make()->truncatePath(false)->isTruncatingPath())->toBeFalse()
        ->and(FilamentStorageMonitor::make()->truncatePath(fn (): bool => true)->isTruncatingPath())->toBeTrue();
});

dataset('paths to split', [
    'multi-segment absolute path splits on the last separator' => [
        '/DATA/sites/dev.example.com/webspace/storage',
        ['start' => '/DATA/sites/dev.example.com/webspace', 'end' => '/storage'],
    ],
    'two-segment absolute path still splits' => [
        '/var/www',
        ['start' => '/var', 'end' => '/www'],
    ],
    'single segment keeps slash as the trailing part' => [
        '/data',
        ['start' => '', 'end' => '/data'],
    ],
    'root path stays as a trailing part' => [
        '/',
        ['start' => '', 'end' => '/'],
    ],
    'relative path splits without a leading slash' => [
        'var/www/html/app',
        ['start' => 'var/www/html', 'end' => '/app'],
    ],
    'windows-style separators are normalized to forward slashes' => [
        'C:\\Users\\Public\\Documents\\reports',
        ['start' => 'C:/Users/Public/Documents', 'end' => '/reports'],
    ],
    'empty string returns empty parts' => [
        '',
        ['start' => '', 'end' => ''],
    ],
]);

test('splitPath() separates the trailing segment from the prefix', function (string $input, array $expected) {
    expect(FilamentStorageMonitor::splitPath($input))->toBe($expected);
})->with('paths to split');
