<?php

declare(strict_types=1);

namespace AchyutN\FilamentStorageMonitor\Concerns;

use Closure;
use Filament\Support\Concerns\EvaluatesClosures;

trait HasWidgetProperties
{
    use EvaluatesClosures;

    protected static ?int $sort = -3;
    protected static bool $isLazy = true;

    protected int|string|array|Closure $columnSpan = 'full';

    protected int|string|array|Closure $columnStart = [];

    /**
     * Set the column span of the widget.
     * Accepts a number (1-12), 'full', or a responsive array.
     */
    public function columnSpan(int|string|array|Closure $span): static
    {
        $this->columnSpan = $span;

        return $this;
    }

    /**
     * Set the column start position of the widget.
     */
    public function columnStart(int|string|array|Closure $start): static
    {
        $this->columnStart = $start;

        return $this;
    }

    public function sort(int|Closure|null $sort): static
    {
        static::$sort = $sort;

        return $this;
    }

    public function lazy(bool|Closure|null $isLazy = true): static
    {
        static::$isLazy = $isLazy;

        return $this;
    }

    /** @return array<string, int|null>|int|string */
    public function getColumnSpan(): int|string|array
    {
        return $this->evaluate($this->columnSpan);
    }

    /** @return array<string, int|null>|int|string */
    public function getColumnStart(): int|string|array
    {
        return $this->evaluate($this->columnStart);
    }

    public function getSort(): int
    {
        return $this->evaluate(static::$sort) ?? -3;
    }

    public function isLazy(): bool
    {
        return $this->evaluate(static::$isLazy) ?? true;
    }
}
