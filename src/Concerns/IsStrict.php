<?php

declare(strict_types=1);

namespace AchyutN\FilamentStorageMonitor\Concerns;

use Closure;
use Filament\Support\Concerns\EvaluatesClosures;

trait IsStrict
{
    use EvaluatesClosures;

    public bool|Closure $throwException = false;

    public function throwException(bool|Closure $throwException = true): static
    {
        $this->throwException = $throwException;

        return $this;
    }

    public function isStrict(): bool
    {
        return (bool) $this->evaluate($this->throwException);
    }
}
