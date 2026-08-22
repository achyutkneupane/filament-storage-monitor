<?php

declare(strict_types=1);

use AchyutN\FilamentStorageMonitor\Calculators\DirectoryCalculator;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;

it('sums file sizes recursively', function () {
    Storage::fake('local');
    Storage::disk('local')->put('a.bin', str_repeat('a', 1024));
    Storage::disk('local')->put('nested/b.bin', str_repeat('b', 2048));
    Storage::disk('local')->put('nested/deep/c.bin', str_repeat('c', 512));

    $calculator = new DirectoryCalculator(Storage::disk('local')->path(''));

    expect($calculator->getUsedSpace())->toBe(3584.0);
});

it('includes hidden files and directories', function () {
    Storage::fake('local');
    Storage::disk('local')->put('.hidden.bin', str_repeat('h', 2048));
    Storage::disk('local')->put('nested/.cache', str_repeat('c', 512));

    $calculator = new DirectoryCalculator(Storage::disk('local')->path(''));

    expect($calculator->getUsedSpace())->toBe(2560.0);
});

it('reports zero for an empty directory', function () {
    Storage::fake('local');

    $calculator = new DirectoryCalculator(Storage::disk('local')->path(''));

    expect($calculator->getUsedSpace())->toBe(0.0);
});

it('reports percentage against the partition total', function () {
    Storage::fake('local');
    Storage::disk('local')->put('a.bin', str_repeat('a', 1024));

    $calculator = new DirectoryCalculator(Storage::disk('local')->path(''));

    $expected = round($calculator->getUsedSpace() / $calculator->getTotalSpace() * 100, 2);

    expect($calculator->getTotalSpace())->toBeGreaterThan($calculator->getUsedSpace())
        ->and($calculator->getUsagePercentage())->toBe($expected)
        ->and($calculator->getUsagePercentage())->toBeLessThan(100);
});

it('throws an exception if the path does not exist', function () {
    expect(fn () => new DirectoryCalculator('/non/existent/path'))
        ->toThrow(DirectoryNotFoundException::class);
});
