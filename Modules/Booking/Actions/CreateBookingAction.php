<?php

declare(strict_types=1);

namespace Modules\Booking\Actions;

use Modules\Booking\Domain\Booking;
use Modules\Booking\Domain\BookingPolicy;
use Modules\Catalog\Contracts\EventCatalogInterface;
use Modules\Identity\Contracts\UserIdentityInterface;
use Modules\Shared\ValueObjects\Id;

final class CreateBookingAction
{
    public function __construct(
        private readonly EventCatalogInterface $catalog,
        private readonly UserIdentityInterface $user,
        private readonly BookingPolicy $policy,
    ) {}

    public function execute(string $eventId): Booking
    {
        $status = $this->catalog->statusOf($eventId);
        if ($status === null) {
            throw new \RuntimeException('Event not found');
        }

        if (!$this->policy->canPlaceBooking($status)) {
            throw new \DomainException('Event is not available for booking');
        }

        return new Booking(new Id('booking_' . uniqid()), $eventId, $this->user->userId());
    }
}
