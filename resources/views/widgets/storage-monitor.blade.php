<x-filament-widgets::widget>
    <x-filament::section collapsible>
        <x-slot name="heading">
            {{ __('filament-storage-monitor::plugin.title') }}
        </x-slot>

        <div @class([
            'fi-storage-monitor-list',
            'fi-compact-list' => $isCompact,
        ])>
            @foreach($disks as $disk)
                @php
                    $isError = array_key_exists('error', $disk);
                    $component = match(true) {
                        $isCompact && $isError => 'compact-error-disk',
                        $isCompact => 'compact-disk',
                        $isError => 'error-disk',
                        default => 'disk',
                    };
                @endphp

                <x-dynamic-component
                    :component="'filament-storage-monitor::' . $component"
                    :$disk
                />
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
