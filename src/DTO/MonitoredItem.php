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

    protected string $name;

    protected ?StorageCalculator $calculator = null;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function make(string $name): static
    {
        return new static($name);
    }

    public function name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function path(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function calculator(?StorageCalculator $calculator): static
    {
        $this->calculator = $calculator;

        return $this;
    }

    public function hasCalculator(): bool
    {
        return $this->calculator !== null;
    }

    abstract public function getCalculator(): StorageCalculator;

    public function getLabel(): string|Htmlable
    {
        /** @var string|Htmlable|null $evaluatedLabel */
        $evaluatedLabel = $this->evaluate($this->label);
        $defaultLabel = $this->getDefaultLabel();

        return $evaluatedLabel ?? $defaultLabel;
    }

    public function getDefaultLabel(): string
    {
        return (string) str($this->name)
            ->afterLast('.')
            ->kebab()
            ->replace(['-', '_'], ' ')
            ->ucwords();
    }
}