<?php

declare(strict_types=1);

namespace Modules\Booking\Domain;

use Modules\Catalog\Domain\EventStatus;

final class BookingPolicy
{
    public function canPlaceBooking(EventStatus $status): bool
    {
        return $status->isAvailable();
    }
}
