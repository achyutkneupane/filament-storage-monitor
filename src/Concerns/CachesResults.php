<?php

declare(strict_types=1);

namespace AchyutN\FilamentStorageMonitor\Concerns;

use Closure;
use Filament\Support\Concerns\EvaluatesClosures;

trait CachesResults
{
    use EvaluatesClosures;

    protected bool|Closure $cacheResults = true;

    protected int $cacheTtl = 300;

    public function cacheResults(bool|Closure $cache = true, int $ttl = 300): static
    {
        $this->cacheResults = $cache;
        $this->cacheTtl = $ttl;

        return $this;
    }

    public function shouldCacheResults(): bool
    {
        return (bool) $this->evaluate($this->cacheResults);
    }

    public function getCacheTtl(): int
    {
        return $this->cacheTtl;
    }
}
