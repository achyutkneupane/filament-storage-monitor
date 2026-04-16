<div class="fi-storage-monitor-item fi-compact-inline">
    <div class="fi-storage-monitor-content">
        <div class="fi-storage-monitor-identity">
            <span class="fi-storage-monitor-label">{{ $disk['label'] }}</span>
            <span class="fi-storage-monitor-path">{{ $disk['used'] }} / {{ $disk['total'] }}</span>
        </div>

        <div class="fi-storage-monitor-progress-wrapper">
            <div class="fi-storage-monitor-progress-bg">
                <div
                    class="fi-storage-monitor-progress-bar"
                    role="progressbar"
                    aria-valuenow="{{ $disk['percentage'] }}"
                    aria-valuemin="0"
                    aria-valuemax="100"
                    style="{{ Filament\Support\get_color_css_variables($disk['progressColor'], [500, 600]) }}; width: {{ $disk['percentage'] }}%"
                ></div>
            </div>

            <div class="fi-storage-monitor-percentage"
                 style="{{ Filament\Support\get_color_css_variables($disk['progressColor'], [500, 600]) }}">
                {{ number_format($disk['percentage'], 0) }}%
            </div>
        </div>
    </div>
</div>
