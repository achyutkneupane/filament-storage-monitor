<?php

declare(strict_types=1);

namespace AchyutN\FilamentStorageMonitor\Concerns;

use Closure;
use Filament\Support\Concerns\EvaluatesClosures;

trait TruncatesPath
{
    use EvaluatesClosures;

    protected bool|Closure $truncatesPath = false;

    /**
     * Split a path into a shrinkable prefix and a pinned trailing segment so
     * the view can apply CSS-based middle-ellipsis.
     *
     * @return array{start: string, end: string}
     */
    public static function splitPath(string $path): array
    {
        $normalized = str_replace('\\', '/', $path);

        while ($normalized !== '/' && str_ends_with($normalized, '/')) {
            $normalized = mb_substr($normalized, 0, -1);
        }

        $lastSlash = mb_strrpos($normalized, '/');

        if ($lastSlash === false || $lastSlash === 0) {
            return ['start' => '', 'end' => $normalized];
        }

        return [
            'start' => mb_substr($normalized, 0, $lastSlash),
            'end' => mb_substr($normalized, $lastSlash),
        ];
    }

    public function truncatePath(bool|Closure $condition = true): static
    {
        $this->truncatesPath = $condition;

        return $this;
    }

    public function isTruncatingPath(): bool
    {
        return (bool) $this->evaluate($this->truncatesPath);
    }
}
