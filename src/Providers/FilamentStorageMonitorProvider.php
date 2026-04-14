<?php

declare(strict_types=1);

namespace AchyutN\FilamentStorageMonitor\Providers;

use AchyutN\FilamentStorageMonitor\Widgets\StorageMonitorWidget;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Livewire\Livewire;

final class FilamentStorageMonitorProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'filament-storage-monitor');
        $this->loadTranslationsFrom(__DIR__.'/../../resources/lang', 'filament-storage-monitor');

        Livewire::component('storage-monitor-widget', StorageMonitorWidget::class);

        FilamentAsset::register(
            assets: [
                Css::make('filament-storage-monitor-styles', __DIR__.'/../../resources/css/plugin.css'),
            ],
            package: 'achyutn/filament-storage-monitor'
        );

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../resources/views' => resource_path('views/vendor/filament-storage-monitor'),
            ], 'filament-storage-monitor-views');
        }
    }
}
