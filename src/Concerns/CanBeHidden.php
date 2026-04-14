<?php

declare(strict_types=1);

namespace AchyutN\FilamentStorageMonitor\Concerns;

use Closure;
use Filament\Support\Concerns\EvaluatesClosures;

trait CanBeHidden
{
    use EvaluatesClosures;

    protected bool|Closure $isVisible = true;

    public function visible(bool|Closure $condition = true): static
    {
        $this->isVisible = $condition;

        return $this;
    }

    public function hidden(bool|Closure $condition = true): static
    {
        $this->isVisible = is_bool($condition) ? ! $condition : fn (): bool => ! $this->evaluate($condition);

        return $this;
    }

    public function isVisible(): bool
    {
        return (bool) $this->evaluate($this->isVisible);
    }
}
