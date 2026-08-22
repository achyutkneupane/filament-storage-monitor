<?php

declare(strict_types=1);

use AchyutN\FilamentStorageMonitor\Calculators\BaseCalculator;
use AchyutN\FilamentStorageMonitor\DTO\MonitoredItem;

arch('no dd, dump, or ray calls')
    ->expect(['dd', 'dump', 'ray'])
    ->each
    ->not
    ->toBeUsed();

arch('all classes are final')
    ->expect('AchyutN\FilamentStorageMonitor')
    ->classes()
    ->toBeFinal()
    ->ignoring([MonitoredItem::class, BaseCalculator::class]);

arch('MonitoredItem is an abstract base')
    ->expect(MonitoredItem::class)
    ->toBeAbstract();
