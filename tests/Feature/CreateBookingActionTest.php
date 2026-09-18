<?php

declare(strict_types=1);

use Modules\Booking\Actions\CreateBookingAction;
use Modules\Booking\Domain\BookingPolicy;
use Modules\Catalog\Services\InMemoryEventCatalogService;
use Modules\Identity\Domain\UserAccount;

function createBookingAction(): CreateBookingAction
{
    return new CreateBookingAction(
        InMemoryEventCatalogService::fromSeed(),
        new UserAccount('user_01', 'Ada'),
        new BookingPolicy(),
    );
}

it('creates a booking through public module contracts', function () {
    $booking = createBookingAction()->execute('evt_01');

    expect($booking->eventId)->toBe('evt_01')
        ->and($booking->userId)->toBe('user_01');
});

it('fails clearly when the event does not exist', function () {
    expect(fn () => createBookingAction()->execute('missing'))
        ->toThrow(\RuntimeException::class, 'Event not found');
});

it('refuses to book an unavailable event', function () {
    expect(fn () => createBookingAction()->execute('evt_02'))
        ->toThrow(\DomainException::class, 'Event is not available for booking');
});
