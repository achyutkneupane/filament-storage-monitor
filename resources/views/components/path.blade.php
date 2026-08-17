@props([
    'path',
    'pathStart' => null,
    'pathEnd' => null,
    'truncatePath' => false,
    'class' => null,
])

@if ($truncatePath)
    <span class="fi-storage-monitor-path {{ $class }}" title="{{ $path }}">
        @if ($pathStart !== null && $pathStart !== '')
            <span class="fi-storage-monitor-path-start">{{ $pathStart }}</span>
        @endif
        <span class="fi-storage-monitor-path-end">{{ $pathEnd }}</span>
    </span>
@else
    <span class="fi-storage-monitor-path {{ $class }}">{{ $path }}</span>
@endif