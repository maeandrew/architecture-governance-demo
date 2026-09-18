<?php

declare(strict_types=1);

namespace Modules\Booking\Http\Controllers;

use Modules\Booking\Actions\CreateBookingAction;
use Modules\Booking\Domain\Booking;

final class BookingController
{
    public function __construct(private readonly CreateBookingAction $action) {}

    public function create(string $eventId): Booking
    {
        return $this->action->execute($eventId);
    }
}
