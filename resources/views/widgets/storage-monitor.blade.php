@php
    use Filament\Support\Icons\Heroicon;
    use Filament\Support\Enums\IconSize;
    use function Filament\Support\get_color_css_variables;
    use function Filament\Support\generate_icon_html;
@endphp

<x-filament-widgets::widget>
    <x-filament::section collapsible>
        <x-slot name="heading">
            {{ __('filament-storage-monitor::plugin.widget.title') }}
        </x-slot>

        <div class="fi-storage-monitor-list">
            @foreach($disks as $disk)
                @php
                    $icon = generate_icon_html($disk['icon'] ?? Heroicon::OutlinedServer, size: IconSize::TwoExtraLarge);
                @endphp

                <div class="fi-storage-monitor-item">
                    <div class="fi-storage-monitor-icon" style="{{ get_color_css_variables($disk['color'], [300, 400]) }}">
                        {{ $icon }}
                    </div>

                    <div class="fi-storage-monitor-content">
                        <div class="fi-storage-monitor-meta">
                            <div class="fi-storage-monitor-identity">
                                <span class="fi-storage-monitor-label">{{ $disk['label'] }}</span>
                                <span class="fi-storage-monitor-path">{{ $disk['path'] }}</span>
                            </div>
                            <div class="fi-storage-monitor-percentage">
                                {{ number_format($disk['percentage'], 1) }}%
                            </div>
                        </div>

                        <div class="fi-storage-monitor-progress-container">
                            <div class="fi-storage-monitor-progress-bg">
                                <div
                                    class="fi-storage-monitor-progress-bar"
                                    style="{{ get_color_css_variables($disk['color'], [500, 600]) }}; width: {{ $disk['percentage'] }}%"
                                ></div>
                            </div>
                        </div>

                        <div class="fi-storage-monitor-details">
                            <span class="fi-storage-monitor-usage">
                                {{ $disk['used'] }} {{ __('filament-storage-monitor::plugin.widget.labels.used') }}
                            </span>
                            <span class="fi-storage-monitor-total">
                                {{ $disk['total'] }} {{ __('filament-storage-monitor::plugin.widget.labels.total') }}
                                <span class="fi-storage-monitor-free-pill">
                                    ({{ $disk['free'] }} {{ __('filament-storage-monitor::plugin.widget.labels.free') }})
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
