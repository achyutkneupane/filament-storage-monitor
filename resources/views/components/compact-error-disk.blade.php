<div class="fi-storage-monitor-item fi-compact opacity-70">
    <div class="fi-storage-monitor-content">
        <div class="fi-storage-monitor-meta !items-center">
            <div class="fi-storage-monitor-identity">
                <span class="fi-storage-monitor-label font-bold">{{ $disk['label'] }}</span>
            </div>
            <x-filament::badge color="danger" size="sm">
                {{ $disk['error'] }}
            </x-filament::badge>
        </div>
    </div>
</div>
