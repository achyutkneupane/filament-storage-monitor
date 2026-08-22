<?php

declare(strict_types=1);

namespace AchyutN\FilamentStorageMonitor\Support;

use Illuminate\Support\Str;

final class Path
{
    /**
     * Split a path into a shrinkable prefix and a pinned trailing segment so
     * the view can apply CSS-based middle-ellipsis.
     *
     * @return array{start: string, end: string}
     */
    public static function abbreviate(string $path): array
    {
        $path = str_replace('\\', '/', $path);

        if ($path === '' || $path === '/') {
            return ['start' => '', 'end' => $path];
        }

        $path = rtrim($path, '/');

        if (! str_contains($path, '/')) {
            return ['start' => '', 'end' => $path];
        }

        return [
            'start' => Str::beforeLast($path, '/'),
            'end' => '/'.Str::afterLast($path, '/'),
        ];
    }
}
