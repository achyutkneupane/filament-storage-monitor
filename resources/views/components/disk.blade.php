@php
    use Filament\Support\Enums\IconSize;
    use Filament\Support\Icons\Heroicon;
    use function Filament\Support\generate_icon_html;
    use function Filament\Support\get_color_css_variables;

    $icon = generate_icon_html($disk['icon'] ?? Heroicon::OutlinedServer, size: IconSize::TwoExtraLarge);
    $truncatePath = $truncatePath ?? false;
@endphp

<div class="fi-storage-monitor-item">
    <div class="fi-storage-monitor-icon" style="{{ get_color_css_variables($disk['color'], [300, 400]) }}">
        {{ $icon }}
    </div>

    <div class="fi-storage-monitor-content">
        <div class="fi-storage-monitor-meta">
            <div class="fi-storage-monitor-identity">
                <span class="fi-storage-monitor-label">{{ $disk['label'] }}</span>
                <x-filament-storage-monitor::path
                    :path="$disk['path']"
                    :path-start="$disk['pathStart'] ?? null"
                    :path-end="$disk['pathEnd'] ?? null"
                    :truncate-path="$truncatePath"
                />
            </div>
            <div class="fi-storage-monitor-percentage"
                 style="{{ get_color_css_variables($disk['progressColor'], [500, 600]) }}">
                {{ number_format($disk['percentage'], 1) }}%
            </div>
        </div>

        <div class="fi-storage-monitor-progress-container">
            <div class="fi-storage-monitor-progress-bg">
                <div
                    class="fi-storage-monitor-progress-bar"
                    role="progressbar"
                    aria-valuenow="{{ $disk['percentage'] }}"
                    aria-valuemin="0"
                    aria-valuemax="100"
                    style="{{ get_color_css_variables($disk['progressColor'], [500, 600]) }}; width: {{ $disk['percentage'] }}%"
                ></div>
            </div>
        </div>

        <div class="fi-storage-monitor-details">
            <span class="fi-storage-monitor-usage">
                {{ $disk['used'] }} {{ __('filament-storage-monitor::plugin.labels.used') }}
            </span>
            <span class="fi-storage-monitor-total">
                {{ $disk['total'] }} {{ __('filament-storage-monitor::plugin.labels.total') }}
                @unless ($disk['directory'] ?? false)
                    <span class="fi-storage-monitor-free-pill">
                        ({{ $disk['free'] }} {{ __('filament-storage-monitor::plugin.labels.free') }})
                    </span>
                @endunless
            </span>
        </div>
    </div>
</div>
