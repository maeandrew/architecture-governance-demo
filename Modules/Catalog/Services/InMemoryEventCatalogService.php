<?php

declare(strict_types=1);

namespace Modules\Catalog\Services;

use Modules\Catalog\Contracts\EventCatalogInterface;
use Modules\Catalog\Domain\Event;
use Modules\Catalog\Domain\EventStatus;

final class InMemoryEventCatalogService implements EventCatalogInterface
{
    /** @param array<int, Event> $events */
    public function __construct(private array $events = []) {}

    public function events(): array
    {
        return array_map(static fn (Event $event): string => $event->id, $this->events);
    }

    public function findById(string $id): ?string
    {
        return $this->find($id)?->name;
    }

    public function statusOf(string $id): ?EventStatus
    {
        return $this->find($id)?->status;
    }

    public static function fromSeed(): self
    {
        return new self([
            new Event('evt_01', 'Spring Launch', new EventStatus(true)),
            new Event('evt_02', 'Design Jam', new EventStatus(false)),
        ]);
    }

    private function find(string $id): ?Event
    {
        foreach ($this->events as $event) {
            if ($event->id === $id) {
                return $event;
            }
        }

        return null;
    }
}
