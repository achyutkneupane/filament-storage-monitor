<?php

declare(strict_types=1);

namespace AchyutN\FilamentStorageMonitor\Concerns;

use Closure;
use Filament\Support\Concerns\EvaluatesClosures;

trait TruncatesPath
{
    use EvaluatesClosures;

    protected bool|Closure $truncatesPath = false;

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
