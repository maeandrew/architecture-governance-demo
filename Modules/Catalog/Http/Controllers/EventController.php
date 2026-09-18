<?php

declare(strict_types=1);

namespace Modules\Catalog\Http\Controllers;

use Modules\Catalog\Contracts\EventCatalogInterface;

final class EventController
{
    public function __construct(private readonly EventCatalogInterface $catalog)
    {
    }

    public function show(string $eventId): ?string
    {
        return $this->catalog->findById($eventId);
    }
}
