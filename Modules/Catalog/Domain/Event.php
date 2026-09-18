<?php

declare(strict_types=1);

namespace Modules\Catalog\Domain;

final class Event
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly EventStatus $status,
    ) {}
}
