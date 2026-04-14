<?php

declare(strict_types=1);

use AchyutN\FilamentStorageMonitor\DTO\Disk;

it('can be configured with getters', function () {
    $disk = Disk::make('local')
        ->label('Local FS')
        ->path('/var/www/storage')
        ->color(Filament\Support\Colors\Color::Blue)
        ->icon(Filament\Support\Icons\Heroicon::ServerStack);

    expect($disk->getLabel())->toBe('Local FS')
        ->and($disk->getPath())->toBe('/var/www/storage')
        ->and($disk->getColor())->toBe(Filament\Support\Colors\Color::Blue)
        ->and($disk->getIcon())->toBe(Filament\Support\Icons\Heroicon::ServerStack);
});
