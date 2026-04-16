<?php

declare(strict_types=1);

namespace AchyutN\FilamentStorageMonitor\Concerns;

trait HasError
{
    /** @var array<int, mixed>|string|null */
    protected array|string|null $error = null;

    /**
     * @internal This is used by the plugin to store configuration errors.
     *
     * @param  array<int, mixed>|string|null  $error
     */
    public function error(array|string|null $error): static
    {
        $this->error = $error;

        return $this;
    }

    /** @return array<int, mixed>|string|null */
    public function getError(): array|string|null
    {
        return $this->error;
    }

    public function hasError(): bool
    {
        return $this->error !== null;
    }
}
