<div class="fi-storage-monitor-item fi-compact">
    <div class="fi-storage-monitor-content">
        <div class="fi-storage-monitor-meta">
            <div class="fi-storage-monitor-identity">
                <span class="fi-storage-monitor-label font-bold">{{ $disk['label'] }}</span>
                <span class="fi-storage-monitor-path text-xs">{{ $disk['used'] }} / {{ $disk['total'] }}</span>
            </div>
            <div class="fi-storage-monitor-percentage text-xs font-bold"
                 style="{{ Filament\Support\get_color_css_variables($disk['progressColor'], [500, 600]) }}">
                {{ number_format($disk['percentage'], 0) }}%
            </div>
        </div>

        <div class="fi-storage-monitor-progress-container !mt-1">
            <div class="fi-storage-monitor-progress-bg !h-1.5">
                <div class="fi-storage-monitor-progress-bar"
                     style="{{ Filament\Support\get_color_css_variables($disk['progressColor'], [500, 600]) }}; width: {{ $disk['percentage'] }}%"
                ></div>
            </div>
        </div>
    </div>
</div>
