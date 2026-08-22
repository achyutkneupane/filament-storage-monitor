<?php

declare(strict_types=1);

use AchyutN\FilamentStorageMonitor\Calculators\CachingCalculator;
use AchyutN\FilamentStorageMonitor\Calculators\DirectoryCalculator;
use AchyutN\FilamentStorageMonitor\Calculators\LocalCalculator;
use AchyutN\FilamentStorageMonitor\DTO\Directory;
use AchyutN\FilamentStorageMonitor\DTO\Disk;
use AchyutN\FilamentStorageMonitor\FilamentStorageMonitor;
use AchyutN\FilamentStorageMonitor\Support\Path;
use InvalidArgumentException;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;

test('plugin can store multiple disks', function () {
    $plugin = FilamentStorageMonitor::make()
        ->add(Disk::make('local')->label('Local')->path('/'))
        ->add(Disk::make('backup')->label('Backup Drive')->path('/mnt/backups'));

    expect($plugin->getDisks())->toHaveCount(2)
        ->and($plugin->getDisks()->first()->getLabel())->toBe('Local');
});

test('laravelDisk() with an unknown disk adds a disk with an error', function () {
    $plugin = FilamentStorageMonitor::make()->laravelDisk('missing-disk');

    expect($plugin->getDisks())->toHaveCount(1)
        ->and($plugin->getDisks()->first()->hasError())->toBeTrue()
        ->and($plugin->getDisks()->first()->getPath())->toBe('missing-disk');
});

test('laravelDisk() with a disk lacking a root key adds a disk with an error', function () {
    config()->set('filesystems.disks.rootless', ['driver' => 's3', 'bucket' => 'example']);

    $plugin = FilamentStorageMonitor::make()->laravelDisk('rootless');

    expect($plugin->getDisks())->toHaveCount(1)
        ->and($plugin->getDisks()->first()->hasError())->toBeTrue()
        ->and($plugin->getDisks()->first()->getPath())->toBe('rootless');
});

test('laravelDisk() with an unknown disk throws in strict mode', function () {
    $this->expectException(InvalidArgumentException::class);

    FilamentStorageMonitor::make()->throwException()->laravelDisk('missing-disk');
});

test('plugin has a unique identifier', function () {
    $plugin = FilamentStorageMonitor::make();

    expect($plugin->getId())->toBe('filament-storage-monitor');
});

test('addDirectory() stores directories', function () {
    $plugin = FilamentStorageMonitor::make()
        ->addDirectory(Directory::make('uploads')->label('Uploads')->path('/'));

    expect($plugin->getDirectories())->toHaveCount(1)
        ->and($plugin->getDirectories()->first()->getLabel())->toBe('Uploads');
});

test('addDirectory() with an invalid path adds an error', function () {
    $plugin = FilamentStorageMonitor::make()
        ->addDirectory(Directory::make('bad')->path('/non/existent/path'));

    expect($plugin->getDirectories()->first()->hasError())->toBeTrue();
});

test('addDirectory() with an invalid path throws in strict mode', function () {
    $this->expectException(DirectoryNotFoundException::class);

    FilamentStorageMonitor::make()->throwException()
        ->addDirectory(Directory::make('bad')->path('/non/existent/path'));
});

test('cacheResults() is enabled by default with a 300 second ttl', function () {
    $plugin = FilamentStorageMonitor::make();

    expect($plugin->shouldCacheResults())->toBeTrue()
        ->and($plugin->getCacheTtl())->toBe(300);
});

test('cacheResults() toggles caching and ttl', function () {
    expect(FilamentStorageMonitor::make()->cacheResults(false)->shouldCacheResults())->toBeFalse()
        ->and(FilamentStorageMonitor::make()->cacheResults(true, 120)->getCacheTtl())->toBe(120)
        ->and(FilamentStorageMonitor::make()->cacheResults()->shouldCacheResults())->toBeTrue();
});

test('disks and directories are wrapped in a caching calculator by default', function () {
    $plugin = FilamentStorageMonitor::make()
        ->add(Disk::make('local')->path('/'))
        ->addDirectory(Directory::make('uploads')->path('/'));

    expect($plugin->getDisks()->first()->getCalculator())->toBeInstanceOf(CachingCalculator::class)
        ->and($plugin->getDirectories()->first()->getCalculator())->toBeInstanceOf(CachingCalculator::class);
});

test('disabling caching keeps the default calculators', function () {
    $plugin = FilamentStorageMonitor::make()->cacheResults(false)
        ->add(Disk::make('local')->path('/'))
        ->addDirectory(Directory::make('uploads')->path('/'));

    expect($plugin->getDisks()->first()->getCalculator())->toBeInstanceOf(LocalCalculator::class)
        ->and($plugin->getDirectories()->first()->getCalculator())->toBeInstanceOf(DirectoryCalculator::class);
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
    'separator-less path keeps the segment as the trailing part' => [
        'data',
        ['start' => '', 'end' => 'data'],
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
    'trailing slash is stripped before splitting' => [
        '/var/www/',
        ['start' => '/var', 'end' => '/www'],
    ],
    'windows trailing slash is stripped before splitting' => [
        'C:\\Users\\',
        ['start' => 'C:', 'end' => '/Users'],
    ],
    'empty string returns empty parts' => [
        '',
        ['start' => '', 'end' => ''],
    ],
]);

test('Path::abbreviate() separates the trailing segment from the prefix', function (string $input, array $expected) {
    expect(Path::abbreviate($input))->toBe($expected);
})->with('paths to split');
