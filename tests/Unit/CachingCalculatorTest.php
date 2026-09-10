<?php

declare(strict_types=1);

use AchyutN\FilamentStorageMonitor\Calculators\CachingCalculator;
use AchyutN\FilamentStorageMonitor\Contracts\StorageCalculator;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    config()->set('cache.default', 'array');
});

it('serves repeated reads from the cache', function () {
    $calculator = Mockery::mock(StorageCalculator::class);
    $calculator->shouldReceive('getUsedSpace')->once()->andReturn(100.0);
    $calculator->shouldReceive('getTotalSpace')->once()->andReturn(200.0);
    $calculator->shouldReceive('getFreeSpace')->once()->andReturn(100.0);

    $cached = new CachingCalculator($calculator, 'fsm-test-used', 300);

    expect($cached->getUsedSpace())->toBe(100.0)
        ->and($cached->getUsedSpace())->toBe(100.0)
        ->and($cached->getTotalSpace())->toBe(200.0)
        ->and($cached->getFreeSpace())->toBe(100.0);
});

it('derives the percentage from cached values without touching the inner calculator', function () {
    $calculator = Mockery::mock(StorageCalculator::class);
    $calculator->shouldReceive('getUsagePercentage')->never();
    $calculator->shouldReceive('getUsedSpace')->once()->andReturn(25.0);
    $calculator->shouldReceive('getTotalSpace')->once()->andReturn(100.0);

    $cached = new CachingCalculator($calculator, 'fsm-test-percent', 300);

    expect($cached->getUsagePercentage())->toBe(25.0);
});

it('delegates formatting to the inner calculator', function () {
    $calculator = Mockery::mock(StorageCalculator::class);
    $calculator->shouldReceive('format')->with(2048.0)->once()->andReturn('2.00 KB');

    $cached = new CachingCalculator($calculator, 'fsm-test-format', 300);

    expect($cached->format(2048.0))->toBe('2.00 KB');
});

it('casts string cached values to float', function () {
    Cache::put('fsm-test-string:used', '123.45', 300);
    Cache::put('fsm-test-string:total', '200.5', 300);
    Cache::put('fsm-test-string:free', '77.05', 300);

    $calculator = Mockery::mock(StorageCalculator::class);
    $calculator->shouldReceive('getUsedSpace')->never();
    $calculator->shouldReceive('getTotalSpace')->never();
    $calculator->shouldReceive('getFreeSpace')->never();

    $cached = new CachingCalculator($calculator, 'fsm-test-string', 300);

    expect($cached->getUsedSpace())->toBe(123.45)
        ->and($cached->getTotalSpace())->toBe(200.5)
        ->and($cached->getFreeSpace())->toBe(77.05);
});
