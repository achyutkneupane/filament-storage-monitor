@php
    use Filament\Support\Enums\IconSize;
    use Filament\Support\Icons\Heroicon;
    use function Filament\Support\generate_icon_html;
    use function Filament\Support\get_color_css_variables;

    $icon = generate_icon_html($disk['icon'] ?? Heroicon::OutlinedServer, size: IconSize::TwoExtraLarge);
@endphp

<div class="fi-storage-monitor-item opacity-70">
    <div class="fi-storage-monitor-icon" style="{{ get_color_css_variables('gray', [300, 400]) }}">
        {{ $icon }}
    </div>

    <div class="fi-storage-monitor-content">
        <div class="fi-storage-monitor-meta">
            <div class="fi-storage-monitor-identity">
                <span class="fi-storage-monitor-label">{{ $disk['label'] }}</span>
                <span class="fi-storage-monitor-path text-danger-500">{{ $disk['path'] }}</span>
            </div>
        </div>

        <div>
            <x-filament::badge color="danger" size="sm" icon="heroicon-m-exclamation-triangle">
                {{ $disk['error'] }}
            </x-filament::badge>
        </div>
    </div>
</div>
