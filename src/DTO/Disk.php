<?php

declare(strict_types=1);

namespace AchyutN\FilamentStorageMonitor\DTO;

use AchyutN\FilamentStorageMonitor\Calculators\LocalCalculator;
use AchyutN\FilamentStorageMonitor\Concerns\CanBeHidden;
use AchyutN\FilamentStorageMonitor\Concerns\HasError;
use AchyutN\FilamentStorageMonitor\Contracts\MonitoredDisk;
use AchyutN\FilamentStorageMonitor\Contracts\StorageCalculator;
use Filament\Schemas\Components\Concerns\HasLabel;
use Filament\Support\Concerns\EvaluatesClosures;
use Filament\Support\Concerns\HasColor;
use Filament\Support\Concerns\HasIcon;
use Illuminate\Contracts\Support\Htmlable;

final class Disk implements MonitoredDisk
{
    use CanBeHidden;
    use EvaluatesClosures;
    use HasColor;
    use HasError;
    use HasIcon;
    use HasLabel;

    private string $path = '/';

    private string $name = 'disk';

    private ?StorageCalculator $calculator = null;

    public function __construct(string $name)
    {
        $this->name($name);
    }

    public static function make(string $name): self
    {
        return new self($name);
    }

    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function path(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function calculator(?StorageCalculator $calculator): self
    {
        $this->calculator = $calculator;

        return $this;
    }

    public function getCalculator(): StorageCalculator
    {
        return $this->calculator ?? new LocalCalculator($this->path);
    }

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
