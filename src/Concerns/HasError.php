<?php

declare(strict_types=1);

namespace AchyutN\FilamentStorageMonitor\Concerns;

trait HasError
{
    protected array|string|null $error = null;

    public function error(array|string|null $error): static
    {
        $this->error = $error;

        return $this;
    }

    public function getError(): array|string|null
    {
        return $this->error;
    }

    public function hasError(): bool
    {
        return $this->error !== null;
    }
}
