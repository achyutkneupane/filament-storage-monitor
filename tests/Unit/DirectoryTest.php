<?php

declare(strict_types=1);

use AchyutN\FilamentStorageMonitor\Calculators\DirectoryCalculator;
use AchyutN\FilamentStorageMonitor\Contracts\StorageCalculator;
use AchyutN\FilamentStorageMonitor\DTO\Directory;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;

it('can be configured with getters', function () {
    $directory = Directory::make('uploads')
        ->label('Uploads')
        ->path('/var/www/storage')
        ->color(Color::Blue)
        ->icon(Heroicon::Folder);

    expect($directory->getLabel())->toBe('Uploads')
        ->and($directory->getPath())->toBe('/var/www/storage')
        ->and($directory->getColor())->toBe(Color::Blue)
        ->and($directory->getIcon())->toBe(Heroicon::Folder);
});

it('uses the directory calculator by default', function () {
    $directory = Directory::make('uploads')->path('/');

    expect($directory->getCalculator())->toBeInstanceOf(DirectoryCalculator::class)
        ->and($directory->hasCalculator())->toBeFalse();
});

it('tracks a custom calculator', function () {
    $calculator = Mockery::mock(StorageCalculator::class);
    $directory = Directory::make('uploads')->calculator($calculator);

    expect($directory->getCalculator())->toBe($calculator)
        ->and($directory->hasCalculator())->toBeTrue();
});