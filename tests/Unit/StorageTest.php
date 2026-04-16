<?php

declare(strict_types=1);

namespace AchyutN\FilamentStorageMonitor\Tests\Unit;

use AchyutN\FilamentStorageMonitor\Calculators\BaseCalculator;
use AchyutN\FilamentStorageMonitor\Calculators\LocalCalculator;
use Mockery;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;

it('calculates percentage correctly through the contract', function () {
    $calculator = Mockery::mock(BaseCalculator::class);

    $calculator->shouldReceive('getTotalSpace')->andReturn(100.0);
    $calculator->shouldReceive('getFreeSpace')->andReturn(40.0);

    expect($calculator->getTotalSpace())->toBe(100.0)
        ->and($calculator->getFreeSpace())->toBe(40.0)
        ->and($calculator->getUsedSpace())->toBe(60.0)
        ->and($calculator->getUsagePercentage())->toBe(60.0);
});

it('throws an exception if the path does not exist', function () {
    $this->expectException(DirectoryNotFoundException::class);
    $this->expectExceptionMessage(__('filament-storage-monitor::plugin.errors.invalid_path', ['path' => '/non/existent/path']));

    new LocalCalculator('/non/existent/path');
});

it('formats storage sizes correctly', function () {
    $calculator = new LocalCalculator('/');
    $fiveTerabytes = 5.21 * pow(1024, 4);

    expect($calculator->format($fiveTerabytes))->toBe('5.21 TB');
});
