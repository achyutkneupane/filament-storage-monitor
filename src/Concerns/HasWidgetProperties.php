<?php

declare(strict_types=1);

namespace AchyutN\FilamentStorageMonitor\Concerns;

use Closure;
use Filament\Support\Concerns\EvaluatesClosures;

trait HasWidgetProperties
{
    use EvaluatesClosures;

    protected static int|Closure|null $sort = -3;

    protected static bool|Closure $isLazy = true;

    /** @var array<string, int|null>|int|string */
    protected int|string|array|Closure $columnSpan = 'full';

    /** @var array<string, int|null>|int|string */
    protected int|string|array|Closure $columnStart = [];

    /** @param array<string, int|null>|int|string|Closure $span */
    public function columnSpan(int|string|array|Closure $span): static
    {
        $this->columnSpan = $span;

        return $this;
    }

    /** @param array<string, int|null>|int|string|Closure $start */
    public function columnStart(int|string|array|Closure $start): static
    {
        $this->columnStart = $start;

        return $this;
    }

    public function sort(int|Closure $sort = -2): static
    {
        static::$sort = $sort;

        return $this;
    }

    public function lazy(bool|Closure $isLazy = true): static
    {
        static::$isLazy = $isLazy;

        return $this;
    }

    /** @return array<string, int|null>|int|string */
    public function getColumnSpan(): int|string|array
    {
        /** @var array<string, int|null>|int|string */
        return $this->evaluate($this->columnSpan);
    }

    /** @return array<string, int|null>|int|string */
    public function getColumnStart(): int|string|array
    {
        /** @var array<string, int|null>|int|string */
        return $this->evaluate($this->columnStart);
    }

    public function getSort(): int
    {
        /** @var int $sort */
        $sort = $this->evaluate(static::$sort);

        return $sort ?? -3;
    }

    public function isLazy(): bool
    {
        return (bool) ($this->evaluate(static::$isLazy) ?? true);
    }
}
