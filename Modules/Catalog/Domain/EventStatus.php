<?php

declare(strict_types=1);

namespace Modules\Catalog\Domain;

final class EventStatus
{
    public function __construct(private readonly bool $available) {}

    public function isAvailable(): bool
    {
        return $this->available;
    }
}
