<?php

declare(strict_types=1);

namespace Modules\Booking\Domain;

use Modules\Shared\ValueObjects\Id;

final class Booking
{
    public function __construct(
        public readonly Id $id,
        public readonly string $eventId,
        public readonly string $userId,
    ) {}
}
