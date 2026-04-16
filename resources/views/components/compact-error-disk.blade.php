<div class="fi-storage-monitor-item fi-compact-inline">
    <div class="fi-storage-monitor-content">
        <div class="fi-storage-monitor-identity">
            <span class="fi-storage-monitor-label">{{ $disk['label'] }}</span>
        </div>

        <div class="fi-storage-monitor-error-badge">
            <x-filament::badge color="danger" size="sm">
                {{ $disk['error'] }}
            </x-filament::badge>
        </div>
    </div>
</div>
