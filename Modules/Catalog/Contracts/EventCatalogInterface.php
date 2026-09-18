<?php

declare(strict_types=1);

namespace Modules\Catalog\Contracts;

use Modules\Catalog\Domain\EventStatus;

interface EventCatalogInterface
{
    /** @return array<int, string> */
    public function events(): array;

    public function findById(string $id): ?string;

    public function statusOf(string $id): ?EventStatus;
}
