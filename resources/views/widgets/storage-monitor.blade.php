@php
    use Filament\Support\Icons\Heroicon;
    use function Filament\Support\get_color_css_variables;
    use function Filament\Support\generate_icon_html;
@endphp

<x-filament-widgets::widget>
    <x-filament::section collapsible>
        <x-slot name="heading">
            {{ __('filament-storage-monitor::plugin.widget.title') }}
        </x-slot>

        <div class="fi-storage-list">
            @foreach($disks as $disk)
                @php
                    $icon = generate_icon_html($disk['icon'] ?? Heroicon::OutlinedServer, size: \Filament\Support\Enums\IconSize::TwoExtraLarge);
                @endphp

                <div class="fi-storage-disk">
                    <div class="fi-storage-disk-icon" style="{{ get_color_css_variables($disk['color'], [300]) }}">
                        {{ $icon }}
                    </div>

                    <div class="fi-storage-disk-details">
                        <div class="fi-storage-disk-header">
                            <span class="fi-storage-disk-label">{{ $disk['label'] }}</span>
                            <span class="fi-storage-disk-path">{{ $disk['path'] }}</span>
                        </div>

                        <div class="fi-storage-disk-stats">
                            <span class="fi-storage-disk-usage">{{ $disk['used'] }} / {{ $disk['total'] }}</span>
                            <span class="fi-storage-disk-percentage">{{ number_format($disk['percentage'], 1) }}%</span>
                        </div>

                        <div class="fi-storage-disk-progressbar">
                            <div
                                class="fi-storage-disk-progress"
                                style="{{ get_color_css_variables($disk['color'], [500, 600]) }}; width: {{ $disk['percentage'] }}%"
                            ></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
