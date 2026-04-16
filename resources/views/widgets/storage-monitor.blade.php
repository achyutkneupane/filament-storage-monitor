<x-filament-widgets::widget>
    <x-filament::section collapsible>
        <x-slot name="heading">
            {{ __('filament-storage-monitor::plugin.title') }}
        </x-slot>

        <div class="fi-storage-monitor-list">
            @foreach($disks as $disk)
                @if (array_key_exists('error', $disk))
                    <x-filament-storage-monitor::error-disk :$disk />
                @else
                    <x-filament-storage-monitor::disk :$disk />
                @endif
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
