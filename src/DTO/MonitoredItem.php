<?php

declare(strict_types=1);

namespace AchyutN\FilamentStorageMonitor\DTO;

use AchyutN\FilamentStorageMonitor\Concerns\CanBeHidden;
use AchyutN\FilamentStorageMonitor\Concerns\HasError;
use AchyutN\FilamentStorageMonitor\Contracts\MonitoredDisk;
use AchyutN\FilamentStorageMonitor\Contracts\StorageCalculator;
use Filament\Schemas\Components\Concerns\HasLabel;
use Filament\Support\Concerns\EvaluatesClosures;
use Filament\Support\Concerns\HasColor;
use Filament\Support\Concerns\HasIcon;
use Illuminate\Contracts\Support\Htmlable;

abstract class MonitoredItem implements MonitoredDisk
{
    use CanBeHidden;
    use EvaluatesClosures;
    use HasColor;
    use HasError;
    use HasIcon;
    use HasLabel;

    protected string $path = '/';

    protected ?StorageCalculator $calculator = null;

    public function __construct(protected string $name) {}

    abstract public function getCalculator(): StorageCalculator;

    final public function name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    final public function getName(): string
    {
        return $this->name;
    }

    final public function path(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    final public function getPath(): string
    {
        return $this->path;
    }

    final public function calculator(?StorageCalculator $calculator): static
    {
        $this->calculator = $calculator;

        return $this;
    }

    final public function hasCalculator(): bool
    {
        return $this->calculator instanceof StorageCalculator;
    }

    final public function getLabel(): string|Htmlable
    {
        /** @var string|Htmlable|null $evaluatedLabel */
        $evaluatedLabel = $this->evaluate($this->label);
        $defaultLabel = $this->getDefaultLabel();

        return $evaluatedLabel ?? $defaultLabel;
    }

    final public function getDefaultLabel(): string
    {
        return (string) str($this->name)
            ->afterLast('.')
            ->kebab()
            ->replace(['-', '_'], ' ')
            ->ucwords();
    }
}
