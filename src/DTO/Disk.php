<?php

declare(strict_types=1);

namespace AchyutN\FilamentStorageMonitor\DTO;

use AchyutN\FilamentStorageMonitor\Calculators\LocalCalculator;
use AchyutN\FilamentStorageMonitor\Contracts\MonitoredDisk;
use AchyutN\FilamentStorageMonitor\Contracts\StorageCalculator;
use Filament\Support\Concerns\EvaluatesClosures;
use Filament\Support\Concerns\HasColor;
use Filament\Support\Concerns\HasIcon;

final class Disk implements MonitoredDisk
{
    use EvaluatesClosures;
    use HasColor;
    use HasIcon;

    private string $path = '/';

    private ?string $label = null;

    private ?StorageCalculator $calculator = null;

    public function __construct(private string $name) {}

    public static function make(string $name): self
    {
        return new self($name);
    }

    public function path(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function calculator(StorageCalculator $calculator): self
    {
        $this->calculator = $calculator;

        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getLabel(): string
    {
        return $this->label ?? $this->name;
    }

    public function getCalculator(): StorageCalculator
    {
        return $this->calculator ?? new LocalCalculator($this->path);
    }
}
