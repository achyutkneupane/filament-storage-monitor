<?php

declare(strict_types=1);

namespace AchyutN\FilamentStorageMonitor\Tests\Unit;

use AchyutN\FilamentStorageMonitor\Contracts\StorageCalculator;
use AchyutN\FilamentStorageMonitor\DTO\Disk;
use AchyutN\FilamentStorageMonitor\Support\DiskPresenter;
use Filament\Support\Colors\Color;
use Mockery;
use RuntimeException;

test('presents a healthy disk with formatted usage', function () {
    $calculator = Mockery::mock(StorageCalculator::class);
    $calculator->shouldReceive('getTotalSpace')->andReturn(100.0);
    $calculator->shouldReceive('getFreeSpace')->andReturn(40.0);
    $calculator->shouldReceive('getUsedSpace')->andReturn(60.0);
    $calculator->shouldReceive('getUsagePercentage')->andReturn(60.0);
    $calculator->shouldReceive('format')->andReturnUsing(fn (float $bytes): string => (string) $bytes);

    $disk = Disk::make('local')->path('/var/www')->calculator($calculator);

    $data = (new DiskPresenter())->present($disk, truncatePath: false, isStrict: false);

    expect($data)
        ->toHaveKeys(['label', 'icon', 'color', 'progressColor', 'path', 'pathStart', 'pathEnd', 'total', 'used', 'free', 'percentage'])
        ->and($data['label'])->toBe('Local')
        ->and($data['path'])->toBe('/var/www')
        ->and($data['pathStart'])->toBe('/var/www')
        ->and($data['pathEnd'])->toBe('')
        ->and($data['total'])->toBe('100')
        ->and($data['used'])->toBe('60')
        ->and($data['free'])->toBe('40')
        ->and($data['percentage'])->toBe(60.0)
        ->and($data['progressColor'])->toBe(Color::Green);
});

test('splits the path when truncation is enabled', function () {
    $calculator = Mockery::mock(StorageCalculator::class);
    $calculator->shouldReceive('getTotalSpace')->andReturn(100.0);
    $calculator->shouldReceive('getFreeSpace')->andReturn(100.0);
    $calculator->shouldReceive('getUsedSpace')->andReturn(0.0);
    $calculator->shouldReceive('getUsagePercentage')->andReturn(0.0);
    $calculator->shouldReceive('format')->andReturn('1 B');

    $disk = Disk::make('local')->path('/var/www/html')->calculator($calculator);

    $data = (new DiskPresenter())->present($disk, truncatePath: true, isStrict: false);

    expect($data['pathStart'])->toBe('/var/www')
        ->and($data['pathEnd'])->toBe('/html');
});

test('returns an error payload without touching the calculator', function () {
    $calculator = Mockery::mock(StorageCalculator::class);
    $calculator->shouldNotReceive(['getTotalSpace', 'getFreeSpace', 'getUsedSpace', 'getUsagePercentage', 'format']);

    $disk = Disk::make('broken')->path('/missing')->error('Disk not found')->calculator($calculator);

    $data = (new DiskPresenter())->present($disk, truncatePath: false, isStrict: false);

    expect($data)
        ->toHaveKeys(['label', 'icon', 'path', 'pathStart', 'pathEnd', 'error'])
        ->and($data['error'])->toBe('Disk not found');
});

test('rethrows calculator exceptions in strict mode', function () {
    $calculator = Mockery::mock(StorageCalculator::class);
    $calculator->shouldReceive('getUsagePercentage')->andThrow(new RuntimeException('boom'));

    $disk = Disk::make('local')->path('/var/www')->calculator($calculator);

    $this->expectException(RuntimeException::class);
    $this->expectExceptionMessage('boom');

    (new DiskPresenter())->present($disk, truncatePath: false, isStrict: true);
});

test('catches calculator exceptions and records the error', function () {
    $calculator = Mockery::mock(StorageCalculator::class);
    $calculator->shouldReceive('getUsagePercentage')->andThrow(new RuntimeException('boom'));

    $disk = Disk::make('local')->path('/var/www')->calculator($calculator);

    $data = (new DiskPresenter())->present($disk, truncatePath: false, isStrict: false);

    expect($data)
        ->toHaveKeys(['label', 'icon', 'path', 'pathStart', 'pathEnd', 'error'])
        ->and($data['error'])->toBe('boom')
        ->and($disk->hasError())->toBeTrue();
});
